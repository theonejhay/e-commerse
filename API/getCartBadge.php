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
    // Query to get the total number of items in the cart
    $stmt = $pdo->prepare("SELECT SUM(quantity) as totalItems FROM cart WHERE customer_id = ?");
    $stmt->execute([$customer_id]);

    $result = $stmt->fetch(PDO::FETCH_ASSOC);

    $totalItems = $result['totalItems'] ?? 0; // Default to 0 if no items

    // Return total number of items as JSON
    echo json_encode(['totalItems' => $totalItems]);

} catch (Exception $e) {
    // Handle any errors
    echo json_encode(['error' => 'Failed to fetch cart badge: ' . $e->getMessage()]);
}
?>
