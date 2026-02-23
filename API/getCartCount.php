<?php
header('Content-Type: application/json');
include_once '../db_connection/db_con.php';

// Get the customer_id from the request
$customer_id = $_GET['customer_id'] ?? null;

if (empty($customer_id)) {
    echo json_encode(['success' => false, 'message' => 'Customer ID is missing']);
    exit();
}

try {
    // Count the total number of rows (orders) in the saved_cart table for the given customer_id
    $stmt = $conn->prepare("
        SELECT COUNT(*) AS totalOrders
        FROM saved_cart
        WHERE customer_id = ?
    ");
    $stmt->bind_param("i", $customer_id);
    $stmt->execute();
    $result = $stmt->get_result();
    $row = $result->fetch_assoc();

    $totalOrders = $row['totalOrders'] ?? 0;

    echo json_encode(['success' => true, 'totalOrders' => $totalOrders]);

} catch (Exception $e) {
    echo json_encode(['success' => false, 'message' => 'Error fetching cart count: ' . $e->getMessage()]);
}
?>
