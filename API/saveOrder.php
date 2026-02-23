<?php
// Include database connection
include '../db_connection/db_con.php';

$data = json_decode(file_get_contents('php://input'), true);

$customer_id = $data['customer_id'];
$cartItems = $data['cartItems'];
$pickupDate = $data['pickupDate'];
$pickupTime = $data['pickupTime'];
$totalAmount = $data['totalAmount'];

try {
    // Get the last inserted order ID
    $orderId = $conn->lastInsertId();

    // Insert each item into order_items table
    foreach ($cartItems as $productId => $quantity) {
        $query = "INSERT INTO order_items (order_id, product_id, quantity, price, total) VALUES (:order_id, :product_id, :quantity, :price, :total)";
        $stmt = $conn->prepare($query);
        $price = $data['products'][$productId]['price'];
        $total = $quantity * $price;
        $stmt->execute([
            ':order_id' => $orderId,
            ':product_id' => $productId,
            ':quantity' => $quantity,
            ':price' => $price,
            ':total' => $total
        ]);
    }

    echo json_encode(['success' => true, 'message' => 'Order saved successfully']);
} catch (Exception $e) {
    echo json_encode(['success' => false, 'message' => $e->getMessage()]);
}
?>
