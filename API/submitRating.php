<?php
include_once '../db_connection/db_con.php';

if (!$conn) {
    die(json_encode(['error' => 'Connection failed: ' . mysqli_connect_error()]));
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $data = json_decode(file_get_contents('php://input'), true);

    $order_id = isset($data['order_id']) ? intval($data['order_id']) : null;
    $rating_value = isset($data['rating_value']) ? intval($data['rating_value']) : null;
    $comments = isset($data['comments']) ? $data['comments'] : null;
    $recommendation = isset($data['recommendation']) ? $data['recommendation'] : null;

    if (!$order_id || !$rating_value) {
        echo json_encode(['success' => false, 'error' => 'Invalid input data.']);
        exit();
    }

    // Validate rating value
    if ($rating_value < 1 || $rating_value > 5) {
        echo json_encode(['success' => false, 'error' => 'Rating value must be between 1 and 5.']);
        exit();
    }

    // Check if the order has already been rated
    $query = "SELECT rating_id FROM ratings WHERE order_id = ?";
    $stmt = mysqli_prepare($conn, $query);
    mysqli_stmt_bind_param($stmt, 'i', $order_id);
    mysqli_stmt_execute($stmt);
    mysqli_stmt_store_result($stmt);

    if (mysqli_stmt_num_rows($stmt) > 0) {
        echo json_encode(['success' => false, 'error' => 'Order already rated.']);
        mysqli_stmt_close($stmt);
        exit();
    }
    mysqli_stmt_close($stmt);

    // Fetch description from claimed_order
    $claimed_order_description = null;
    $query = "SELECT description FROM claimed_order WHERE order_id = ?";
    $stmt = mysqli_prepare($conn, $query);
    mysqli_stmt_bind_param($stmt, 'i', $order_id);
    mysqli_stmt_execute($stmt);
    mysqli_stmt_bind_result($stmt, $claimed_order_description);
    mysqli_stmt_fetch($stmt);
    mysqli_stmt_close($stmt);

    // If description not found in claimed_order, fetch from order_main
    if (!$claimed_order_description) {
        $query = "SELECT description FROM order_main WHERE order_id = ?";
        $stmt = mysqli_prepare($conn, $query);
        mysqli_stmt_bind_param($stmt, 'i', $order_id);
        mysqli_stmt_execute($stmt);
        mysqli_stmt_bind_result($stmt, $claimed_order_description);
        mysqli_stmt_fetch($stmt);
        mysqli_stmt_close($stmt);

        if (!$claimed_order_description) {
            echo json_encode(['success' => false, 'error' => 'Order description not found.']);
            exit();
        }
    }

    // Insert rating into ratings table
    $query = "INSERT INTO ratings (order_id, rating_value, rating_description, comments, recommendation) VALUES (?, ?, ?, ?, ?)";
    $stmt = mysqli_prepare($conn, $query);
    mysqli_stmt_bind_param($stmt, 'iisss', $order_id, $rating_value, $claimed_order_description, $comments, $recommendation);

    if (mysqli_stmt_execute($stmt)) {
        echo json_encode(['success' => true, 'message' => 'Rating saved successfully.']);
    } else {
        echo json_encode(['success' => false, 'error' => 'Database error: ' . mysqli_stmt_error($stmt)]);
    }

    mysqli_stmt_close($stmt);
} else {
    echo json_encode(['success' => false, 'error' => 'Invalid request method.']);
}

mysqli_close($conn);
?>
