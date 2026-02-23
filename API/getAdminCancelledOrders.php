<?php
include_once '../db_connection/db_con.php';

// Verify connection
if (!$conn) {
    die(json_encode(array('error' => 'Connection failed: ' . mysqli_connect_error())));
}

// Extract parameters
$order_id = isset($_GET['order_id']) ? intval($_GET['order_id']) : null;
$search_query = isset($_GET['search']) ? $_GET['search'] : null;
$start_date = isset($_GET['startDate']) ? $_GET['startDate'] : null;
$end_date = isset($_GET['endDate']) ? $_GET['endDate'] : null;

// Validate dates
if ($start_date && !preg_match('/^\d{4}-\d{2}-\d{2}$/', $start_date)) {
    die(json_encode(array('error' => 'Invalid start date format. Use YYYY-MM-DD.')));
}
if ($end_date && !preg_match('/^\d{4}-\d{2}-\d{2}$/', $end_date)) {
    die(json_encode(array('error' => 'Invalid end date format. Use YYYY-MM-DD.')));
}

// Function to fetch addons for a given order_id
function fetch_addons($conn, $order_id) {
    $addon_query = "SELECT addon_name, addon_type, addon_price, quantity FROM cancelled_addons WHERE order_id = ?";
    $stmt = mysqli_prepare($conn, $addon_query);
    mysqli_stmt_bind_param($stmt, 'i', $order_id);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);

    $addons = array();
    while ($row = mysqli_fetch_assoc($result)) {
        $addons[] = $row;
    }

    mysqli_stmt_free_result($stmt);
    mysqli_stmt_close($stmt);

    return $addons;
}

if ($order_id) {
    // Query to retrieve specific cancelled order details
    $query = "SELECT * FROM admincancelled_orders WHERE order_id = ?";
    $stmt = mysqli_prepare($conn, $query);
    mysqli_stmt_bind_param($stmt, 'i', $order_id);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);

    // Fetch the order details
    $order = mysqli_fetch_assoc($result);

    if ($order) {
        $order['addons'] = fetch_addons($conn, $order_id);
        echo json_encode($order, JSON_PRETTY_PRINT);
    } else {
        echo json_encode(array('error' => 'Order not found.'), JSON_PRETTY_PRINT);
    }
} else {
    // Base query to retrieve all cancelled orders
    $query = "SELECT * FROM admincancelled_orders WHERE 1=1";
    $params = [];
    $types = '';

    // Apply search filter if provided
    if ($search_query) {
        $query .= " AND (order_id LIKE ? OR customer_id LIKE ? OR username LIKE ? OR contact_no LIKE ? OR confirmation_status LIKE ?)";
        $search_param = '%' . $search_query . '%';
        $params[] = $search_param;
        $params[] = $search_param;
        $params[] = $search_param;
        $params[] = $search_param;
        $params[] = $search_param;
        $types .= 'sssss';
    }

    // Apply date range filter if provided
    if ($start_date && $end_date) {
        $query .= " AND pickup_date BETWEEN ? AND ?";
        $params[] = $start_date;
        $params[] = $end_date;
        $types .= 'ss';
    } elseif ($start_date) {
        $query .= " AND pickup_date >= ?";
        $params[] = $start_date;
        $types .= 's';
    } elseif ($end_date) {
        $query .= " AND pickup_date <= ?";
        $params[] = $end_date;
        $types .= 's';
    }

    // Prepare the statement
    $stmt = mysqli_prepare($conn, $query);

    // Bind parameters if any
    if (!empty($params)) {
        mysqli_stmt_bind_param($stmt, $types, ...$params);
    }

    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);

    // Collect orders into an array
    $orders = array();
    while ($row = mysqli_fetch_assoc($result)) {
        $row['addons'] = fetch_addons($conn, $row['order_id']); // Fetch addons for each order
        $orders[] = $row;
    }

    // Get total count of cancelled orders (with filters applied)
    $totalQuery = "SELECT COUNT(*) as total FROM admincancelled_orders WHERE 1=1";
    $totalParams = [];
    $totalTypes = '';

    if ($search_query) {
        $totalQuery .= " AND (order_id LIKE ? OR customer_id LIKE ? OR username LIKE ? OR contact_no LIKE ? OR confirmation_status LIKE ?)";
        $totalParams[] = $search_param;
        $totalParams[] = $search_param;
        $totalParams[] = $search_param;
        $totalParams[] = $search_param;
        $totalParams[] = $search_param;
        $totalTypes .= 'sssss';
    }

    if ($start_date && $end_date) {
        $totalQuery .= " AND pickup_date BETWEEN ? AND ?";
        $totalParams[] = $start_date;
        $totalParams[] = $end_date;
        $totalTypes .= 'ss';
    } elseif ($start_date) {
        $totalQuery .= " AND pickup_date >= ?";
        $totalParams[] = $start_date;
        $totalTypes .= 's';
    } elseif ($end_date) {
        $totalQuery .= " AND pickup_date <= ?";
        $totalParams[] = $end_date;
        $totalTypes .= 's';
    }

    $totalStmt = mysqli_prepare($conn, $totalQuery);

    if (!empty($totalParams)) {
        mysqli_stmt_bind_param($totalStmt, $totalTypes, ...$totalParams);
    }

    mysqli_stmt_execute($totalStmt);
    $totalResult = mysqli_stmt_get_result($totalStmt);
    $total = mysqli_fetch_assoc($totalResult)['total'];

    $response = array(
        'orders' => $orders,
        'total' => $total
    );

    header('Content-Type: application/json');
    echo json_encode($response, JSON_PRETTY_PRINT);
}

// Close resources
if (isset($stmt)) {
    mysqli_stmt_free_result($stmt);
    mysqli_stmt_close($stmt);
}
mysqli_close($conn);
?>
