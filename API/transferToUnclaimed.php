<?php
include('../db_connection/db_con.php');

// Get the raw POST data
$data = file_get_contents('php://input');

// Decode the JSON data
$jsonData = json_decode($data, true);

if (isset($jsonData['order_id']) && isset($jsonData['customer_id'])) {
    $order_id = $jsonData['order_id'];
    $customer_id = $jsonData['customer_id'];

    // Start a transaction
    mysqli_begin_transaction($conn);

    try {
        // Insert order into the unclaimed_orders table
        $insertQuery = "INSERT INTO unclaimed_orders (customer_id, description, total_amount, total_orders, confirmation_status, pickup_date, pickup_time, order_id)
                        SELECT customer_id, description, total_amount, total_orders, confirmation_status, pickup_date, pickup_time, order_id
                        FROM order_main WHERE order_id = ?";
        $stmt = $conn->prepare($insertQuery);
        $stmt->bind_param("i", $order_id);
        $stmt->execute();

        // Delete the order from the original table
        $deleteQuery = "DELETE FROM order_main WHERE order_id = ?";
        $stmt = $conn->prepare($deleteQuery);
        $stmt->bind_param("i", $order_id);
        $stmt->execute();

        // Commit the transaction
        mysqli_commit($conn);

        echo json_encode(['success' => true]);
    } catch (Exception $e) {
        // Roll back the transaction in case of error
        mysqli_rollback($conn);
        echo json_encode(['success' => false, 'message' => $e->getMessage()]);
    }
} else {
    echo json_encode(['success' => false, 'message' => 'Missing parameters.']);
}

// Make sure to close the connection
$conn->close();
?>
