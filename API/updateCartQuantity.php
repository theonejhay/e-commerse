<?php
session_start();
include '../db_connection/db_con.php';

// Enable error reporting for debugging
ini_set('display_errors', 1);
error_reporting(E_ALL);

// Get the JSON payload from the request
$data = json_decode(file_get_contents('php://input'), true);

// Ensure the session contains the customer_id
if (!isset($_SESSION['customer_id'])) {
    echo json_encode(['success' => false, 'message' => 'Customer ID not found in session']);
    exit;
}

$customer_id = $_SESSION['customer_id'];
$product_id = $data['product_id'];
$quantity = $data['quantity'];

// Prepare a query to update the cart item quantity
$query = "UPDATE cart_items SET quantity = ? WHERE customer_id = ? AND product_id = ?";
$stmt = $conn->prepare($query);

// Check for preparation error
if (!$stmt) {
    echo json_encode(['success' => false, 'message' => 'Failed to prepare the statement: ' . $conn->error]);
    exit;
}

$stmt->bind_param("iii", $quantity, $customer_id, $product_id);

// Execute the statement and check for errors
if ($stmt->execute()) {
    echo json_encode(['success' => true]);
} else {
    echo json_encode(['success' => false, 'message' => 'Failed to update cart quantity: ' . $stmt->error]);
}
