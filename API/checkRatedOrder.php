<?php
include_once '../db_connection/db_con.php';

if (!$conn) {
    die(json_encode(['success' => false, 'error' => 'Connection failed: ' . mysqli_connect_error()]));
}

if ($_SERVER['REQUEST_METHOD'] === 'GET') {
    $order_id = isset($_GET['order_id']) ? intval($_GET['order_id']) : null;

    if (!$order_id) {
        echo json_encode(['success' => false, 'error' => 'Invalid order ID.']);
        exit();
    }

    // Check if the order has already been rated
    $query = "SELECT rating_id FROM ratings WHERE order_id = ?";
    $stmt = mysqli_prepare($conn, $query);
    mysqli_stmt_bind_param($stmt, 'i', $order_id);
    mysqli_stmt_execute($stmt);
    mysqli_stmt_store_result($stmt);

    if (mysqli_stmt_num_rows($stmt) > 0) {
        echo json_encode(['success' => true, 'error' => 'Order already rated.']);
    } else {
        echo json_encode(['success' => true]);
    }

    mysqli_stmt_close($stmt);
} else {
    echo json_encode(['success' => false, 'error' => 'Invalid request method.']);
}

mysqli_close($conn);
?>
