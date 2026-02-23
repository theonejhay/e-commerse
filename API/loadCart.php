<?php
include '../db_connection/db_con.php';
session_start();

// Check if the customer is logged in
if (!isset($_SESSION['customer_id'])) {
    echo json_encode(['error' => 'Customer not logged in']);
    exit;
}

$customer_id = $_SESSION['customer_id'];

try {
    // Query to fetch the cart items along with product details
    $query = "
        SELECT ci.product_id, ci.quantity, pm.description, pm.price, pm.image
        FROM cart ci
        JOIN product_maintenance pm ON ci.product_id = pm.product_id
        WHERE ci.customer_id = ?
    ";
    
    $stmt = $conn->prepare($query);
    $stmt->bind_param('i', $customer_id);
    $stmt->execute();
    $result = $stmt->get_result();

    $cart = [];
    
    // Fetch data and build the cart array
    while ($row = $result->fetch_assoc()) {
        $cart[$row['product_id']] = [
            'quantity' => $row['quantity'],
            'description' => $row['description'],
            'price' => $row['price'],
            'image' => base64_encode($row['image']) // Encode the image in base64 if it's binary data
        ];
    }

    echo json_encode(['cart' => $cart]);

} catch (Exception $e) {
    echo json_encode(['error' => 'Failed to load cart: ' . $e->getMessage()]);
}
?>
