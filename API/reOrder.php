<?php
include '../db_connection/db_con.php';

$data = json_decode(file_get_contents('php://input'), true);
$order_id = $data['order_id'];
$pickup_date = $data['pickup_date']; // Get pickup_date from the frontend
$pickup_time = $data['pickup_time']; // Get pickup_time from the frontend

// Fetch order details from admincancelled_orders
$sql = "SELECT * FROM admincancelled_orders WHERE order_id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $order_id);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows > 0) {
    $order = $result->fetch_assoc();

    // Set confirmation_status to 0 (for Pending)
    $confirmation_status = 'pending'; // Use 0 for 'Pending' if confirmation_status is an integer

    // Insert into order_main with confirmation_status set to 'Pending' (0)
    $insertSql = "INSERT INTO order_main (customer_id, description, total_amount, total_orders, confirmation_status, pickup_date, pickup_time, order_id) VALUES (?, ?, ?, ?, ?, ?, ?, ?)";
    $insertStmt = $conn->prepare($insertSql);
    $insertStmt->bind_param(
        "isdisssi",
        $order['customer_id'],
        $order['description'],
        $order['total_amount'],
        $order['total_orders'],
        $confirmation_status, // Use 0 to represent 'Pending'
        $pickup_date, // Use user-provided pickup_date
        $pickup_time, // Use user-provided pickup_time
        $order['order_id']
    );

    if ($insertStmt->execute()) {
        // Delete from admincancelled_orders
        $deleteSql = "DELETE FROM admincancelled_orders WHERE order_id = ?";
        $deleteStmt = $conn->prepare($deleteSql);
        $deleteStmt->bind_param("i", $order_id);

        if ($deleteStmt->execute()) {
            echo json_encode(['success' => true]);
        } else {
            echo json_encode(['success' => false, 'error' => 'Failed to delete cancelled order.']);
        }
    } else {
        echo json_encode(['success' => false, 'error' => 'Failed to insert into order_main.']);
    }
} else {
    echo json_encode(['success' => false, 'error' => 'Order not found.']);
}

$conn->close();
?>
