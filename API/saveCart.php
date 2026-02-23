<?php
// Ensure that the response is JSON
header('Content-Type: application/json');

// Include your database connection
include '../db_connection/db_con.php';

session_start();
$customer_id = $_SESSION['customer_id']; // Assuming customer ID is stored in session

// Get the raw POST data
$data = json_decode(file_get_contents("php://input"), true);
file_put_contents('debug_log.txt', print_r($data, true)); // Log the incoming data

// Check if customer is logged in
if (!isset($customer_id)) {
    echo json_encode(['status' => 'error', 'message' => 'Customer not logged in']);
    exit;
}

// Check if the cart data exists
if (!isset($data['cart'])) {
    echo json_encode(['status' => 'error', 'message' => 'Cart data not found']);
    exit;
}

// If the cart is empty, remove all items from the cart table for the customer
if (empty($data['cart'])) {
    $deleteAllQuery = "DELETE FROM cart WHERE customer_id = ?";
    $deleteStmt = $conn->prepare($deleteAllQuery);
    $deleteStmt->bind_param('i', $customer_id);
    
    if (!$deleteStmt->execute()) {
        echo json_encode(['status' => 'error', 'message' => 'Database delete failed: ' . $deleteStmt->error]);
        exit;
    }

    echo json_encode(['status' => 'success', 'message' => 'Cart cleared successfully']);
    exit;
}

foreach ($data['cart'] as $productId => $product) {
    if (is_array($product) && isset($product['quantity'], $product['description'], $product['price'])) {
        $quantity = $product['quantity'];
        $description = $product['description'];
        $price = $product['price'];
        
        // Decode the image if it exists
        $image = $product['image'] ?? null; 
        if ($image) {
            // Remove the "data:image/...;base64," part if present
            $image = preg_replace('#^data:image/\w+;base64,#i', '', $image);
            // Decode base64 string to binary
            $image = base64_decode($image);
        }

    } else {
        file_put_contents('debug_log.txt', "Invalid product data for productId $productId: " . print_r($product, true), FILE_APPEND);
        continue;
    }

    if ($quantity > 0) {
        $query = "SELECT * FROM cart WHERE customer_id = ? AND product_id = ?";
        $stmt = $conn->prepare($query);
        $stmt->bind_param('ii', $customer_id, $productId);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result->num_rows > 0) {
            // Update existing cart item
            $updateQuery = "UPDATE cart SET quantity = ?, price = ?, description = ?, image = ?, updated_at = NOW() WHERE customer_id = ? AND product_id = ?";
$updateStmt = $conn->prepare($updateQuery);
$updateStmt->bind_param('idsbii', $quantity, $price, $description, $null, $customer_id, $productId);
$updateStmt->send_long_data(3, $image); // Send binary data for the 'image' parameter

if (!$updateStmt->execute()) {
    echo json_encode(['status' => 'error', 'message' => 'Database update failed: ' . $updateStmt->error]);
    exit;
}

        } else {
            // Insert new cart item
            $insertQuery = "INSERT INTO cart (customer_id, product_id, price, quantity, description, image, created_at, updated_at) VALUES (?, ?, ?, ?, ?, ?, NOW(), NOW())";
$insertStmt = $conn->prepare($insertQuery);
$insertStmt->bind_param('iidsbs', $customer_id, $productId, $price, $quantity, $description, $null);
$insertStmt->send_long_data(5, $image); // Send binary data for the 'image' parameter

if (!$insertStmt->execute()) {
    echo json_encode(['status' => 'error', 'message' => 'Database insert failed: ' . $insertStmt->error]);
    exit;
}

        }
    } else {
        // Remove item from cart if quantity is zero
        $deleteQuery = "DELETE FROM cart WHERE customer_id = ? AND product_id = ?";
        $deleteStmt = $conn->prepare($deleteQuery);
        $deleteStmt->bind_param('ii', $customer_id, $productId);
        if (!$deleteStmt->execute()) {
            echo json_encode(['status' => 'error', 'message' => 'Database delete failed: ' . $deleteStmt->error]);
            exit;
        }
    }
}

echo json_encode(['status' => 'success', 'message' => 'Cart updated successfully']);

?>
