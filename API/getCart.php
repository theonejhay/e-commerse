<?php
session_start();
include_once '../db_connection/db_con.php';

// Ensure customer_id is passed
if (!isset($_GET['customer_id'])) {
    echo json_encode(['error' => 'Customer ID is required']);
    exit;
}

$customer_id = $_GET['customer_id'];

try {
    // Query to get the cart items for the customer
    $stmt = $pdo->prepare("
        SELECT ci.product_id, ci.quantity, pm.description, pm.price, pm.image
        FROM cart ci
        JOIN product_maintenance pm ON ci.product_id = pm.product_id
        WHERE ci.customer_id = ?
    ");
    $stmt->execute([$customer_id]);

    $cartItems = $stmt->fetchAll(PDO::FETCH_ASSOC);

    // Prepare the cart array to return
    $cart = [];

    foreach ($cartItems as $item) {
        $cart[$item['product_id']] = [
            'description' => $item['description'],
            'price' => $item['price'],
            'quantity' => $item['quantity'],
            'image' => base64_encode($item['image']) // Encoding image to base64 if it's a blob
        ];
    }

    // Return the cart as JSON
    echo json_encode(['cart' => $cart]);

} catch (Exception $e) {
    // Handle any errors
    echo json_encode(['error' => 'Failed to fetch cart: ' . $e->getMessage()]);
}
?>
