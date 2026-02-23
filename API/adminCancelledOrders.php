<?php
include_once '../db_connection/db_con.php';

// Verify connection
if (!$conn) {
    die(json_encode(array('error' => 'Connection failed: ' . mysqli_connect_error())));
}

// Extract parameters
$order_id = isset($_GET['order_id']) ? intval($_GET['order_id']) : null;
$search_query = isset($_GET['search']) ? $_GET['search'] : null;
$page = isset($_GET['page']) ? intval($_GET['page']) : 1;
$limit = isset($_GET['limit']) ? intval($_GET['limit']) : 10;
$offset = ($page - 1) * $limit;

// Check for customer ID from session
session_start();
$customer_id = isset($_SESSION['customer_id']) ? $_SESSION['customer_id'] : null;

if (!$customer_id) {
    echo json_encode(['error' => 'Customer not logged in.']);
    exit;
}

if ($order_id) {
    // Query for a specific order
    $query = "
        SELECT order_id, customer_id, username, contact_no, description, total_amount, 
               total_orders, confirmation_status, pickup_date, pickup_time, remarks, status
        FROM admincancelled_order
        WHERE order_id = ? AND customer_id = ?
    ";
    $stmt = mysqli_prepare($conn, $query);
    mysqli_stmt_bind_param($stmt, 'ii', $order_id, $customer_id);
} else {
    // Query for multiple orders with optional search and pagination
    $query = "
        SELECT order_id, customer_id, username, contact_no, description, total_amount, 
               total_orders, confirmation_status, pickup_date, pickup_time, remarks, status
        FROM admincancelled_orders
        WHERE customer_id = ?
    ";

    if ($search_query) {
        $query .= " AND (description LIKE ? OR status LIKE ? OR remarks LIKE ?)";
        $search_param = '%' . $search_query . '%';
    }

    $query .= " LIMIT ? OFFSET ?";
    $stmt = mysqli_prepare($conn, $query);

    if ($search_query) {
        mysqli_stmt_bind_param($stmt, 'isssii', $customer_id, $search_param, $search_param, $search_param, $limit, $offset);
    } else {
        mysqli_stmt_bind_param($stmt, 'iii', $customer_id, $limit, $offset);
    }
}

mysqli_stmt_execute($stmt);
$result = mysqli_stmt_get_result($stmt);

$orders = [];
while ($row = mysqli_fetch_assoc($result)) {
    $orders[] = [
        'order_id' => $row['order_id'],
        'customer_id' => $row['customer_id'],
        'username' => $row['username'],
        'contact_no' => $row['contact_no'],
        'description' => $row['description'],
        'total_amount' => $row['total_amount'],
        'total_orders' => $row['total_orders'],
        'confirmation_status' => $row['confirmation_status'],
        'pickup_date' => $row['pickup_date'],
        'pickup_time' => $row['pickup_time'],
        'remarks' => $row['remarks'],
        'status' => $row['status'],
    ];
}

$response = [
    'orders' => $orders,
    'total' => count($orders),
    'page' => $page,
    'limit' => $limit,
];

header('Content-Type: application/json');
echo json_encode($response, JSON_PRETTY_PRINT);
?>
