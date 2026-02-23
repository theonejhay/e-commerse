<?php
include_once '../db_connection/db_con.php'; 

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $input = json_decode(file_get_contents('php://input'), true);

    $confirmation_status = isset($input['confirmation_status']) ? $input['confirmation_status'] : null;

    // Build the SQL query with filters
    $query = 'SELECT * FROM order_main WHERE 1=1';
    $params = [];
    $paramTypes = '';

    if ($confirmation_status !== null && in_array($confirmation_status, ['pending', 'confirmed'])) {
        $query .= ' AND confirmation_status = ?';
        $params[] = $confirmation_status;
        $paramTypes .= 's';
    }

    if (!$conn) {
        http_response_code(500);
        echo json_encode(["message" => "Connection failed: " . mysqli_connect_error()]);
        exit();
    }

    $stmt = mysqli_prepare($conn, $query);
    if ($params) {
        mysqli_stmt_bind_param($stmt, $paramTypes, ...$params);
    }

    if (mysqli_stmt_execute($stmt)) {
        $result = mysqli_stmt_get_result($stmt);
        $orders = mysqli_fetch_all($result, MYSQLI_ASSOC);
        echo json_encode($orders);
    } else {
        http_response_code(500);
        echo json_encode(["message" => "Failed to fetch orders: " . mysqli_error($conn)]);
    }

    mysqli_stmt_close($stmt);
    mysqli_close($conn);
}
?>
