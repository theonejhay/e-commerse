<?php
session_start();

include '../db_connection/db_con.php'; // Ensure this path is correct

if (!isset($_SESSION['customer_id'])) {
    echo json_encode(["error" => "Customer not logged in"]);
    exit;
}

$customer_id = $_SESSION['customer_id'];

// SQL to fetch data from the `cart` table including the image
$sql = "SELECT cart_id, customer_id, product_id, price, quantity, created_at, updated_at, description, image
        FROM cart 
        WHERE customer_id = ?";
$stmt = $conn->prepare($sql);

if (!$stmt) {
    echo json_encode(["error" => "SQL preparation failed: " . $conn->error]);
    exit;
}

$stmt->bind_param("i", $customer_id);
$stmt->execute();
$result = $stmt->get_result();

$cart_items = [];
while ($row = $result->fetch_assoc()) {
    // Convert the image to base64 for JSON compatibility
    $image_base64 = base64_encode($row['image']);

    $cart_items[] = [   
        'cart_id' => $row['cart_id'],
        'customer_id' => $row['customer_id'],
        'product_id' => $row['product_id'],
        'price' => $row['price'],
        'quantity' => $row['quantity'],
        'created_at' => $row['created_at'],
        'updated_at' => $row['updated_at'],
        'description' => $row['description'],
        'image' => $image_base64, // Include the base64-encoded image
    ];
}

echo json_encode($cart_items);

$stmt->close();
$conn->close();
?>
