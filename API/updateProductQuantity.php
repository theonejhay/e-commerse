<?php
include '../db_connection/db_con.php';

$data = json_decode(file_get_contents('php://input'), true);

$product_id = $data['product_id'];
$quantity_change = $data['quantity'];
$action = isset($data['action']) ? $data['action'] : 'decrease'; // Default action is 'decrease'

if ($product_id && $quantity_change) {
    // Check the current quantity in the database
$query = "SELECT quantity, category FROM product_maintenance WHERE product_id = ?";

    $stmt = $conn->prepare($query);
    $stmt->bind_param("i", $product_id);
    $stmt->execute();
    $result = $stmt->get_result();
    $product = $result->fetch_assoc();

    if ($product) {
        // Determine whether to increase or decrease the quantity
        if ($action === 'increase') {
            $new_quantity = $product['quantity'] + $quantity_change; // Increase quantity when cancelling
        } else {
            $new_quantity = $product['quantity'] - $quantity_change; // Decrease quantity when confirming
            $new_quantity = max(0, $new_quantity); // Ensure the quantity doesn't go below 0
        }

        // Update the quantity in the database
        $update_query = "UPDATE product_maintenance SET quantity = ? WHERE product_id = ?";
        $update_stmt = $conn->prepare($update_query);
        $update_stmt->bind_param("ii", $new_quantity, $product_id);
        $update_stmt->execute();

        echo json_encode([
            'success' => true,
            'new_quantity' => $new_quantity
        ]);
    } else {
        echo json_encode([
            'success' => false,
            'message' => 'Product not found'
        ]);
    }
} else {
    echo json_encode([
        'success' => false,
        'message' => 'Invalid input'
    ]);
}
?>
