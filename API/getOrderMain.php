<?php
include_once '../db_connection/db_con.php'; 

// Verify connection
if (!$conn) {
    die(json_encode(array('error' => 'Connection failed: ' . mysqli_connect_error())));
}

// Extract parameters
$order_id = isset($_GET['order_id']) ? intval($_GET['order_id']) : null;
$search_query = isset($_GET['search']) ? $_GET['search'] : null;

if ($order_id) {
    // Query to retrieve specific order and its addons by order_id, including customer details
    $query = "
        SELECT o.*, oa.addon_type, oa.addon_name, oa.addon_price, oa.quantity AS addon_quantity, 
               c.firstname, c.lastname, c.contact_no
        FROM order_main o
        LEFT JOIN ordered_addons oa ON o.order_id = oa.order_id
        LEFT JOIN customer c ON o.customer_id = c.customer_id
        WHERE o.order_id = ?
    ";
    $stmt = mysqli_prepare($conn, $query);
    mysqli_stmt_bind_param($stmt, 'i', $order_id);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);

    // Process the result to include addons and customer details
    $order = array();
    while ($row = mysqli_fetch_assoc($result)) {
        if (empty($order)) {
            // Initialize order data on first row
            $order = array(
                'order_id' => $row['order_id'],
                'customer_id' => $row['customer_id'],
                'firstname' => $row['firstname'],
                'lastname' => $row['lastname'],
                'contact_no' => $row['contact_no'],
                'description' => $row['description'],
                'total_amount' => $row['total_amount'],
                'total_orders' => $row['total_orders'],
                'confirmation_status' => $row['confirmation_status'],
                'pickup_date' => $row['pickup_date'],
                'pickup_time' => $row['pickup_time'],
                'addons' => array(),
            );
        }
        
        // Add addon data if available
        if ($row['addon_type']) {
            $order['addons'][] = array(
                'addon_type' => $row['addon_type'],
                'addon_name' => $row['addon_name'],
                'addon_price' => $row['addon_price'],
                'addon_quantity' => $row['addon_quantity'],
                'total_addon_price' => $row['addon_price'] * $row['addon_quantity'],
            );
        }
    }

    echo json_encode($order ? $order : array('error' => 'Order not found.'), JSON_PRETTY_PRINT);

} else {
    // Query to retrieve all orders with addons based on search query, including customer details
    $query = "
        SELECT o.order_id, o.customer_id, o.description, o.total_amount, o.total_orders, 
               o.confirmation_status, o.pickup_date, o.pickup_time, 
               oa.addon_type, oa.addon_name, oa.addon_price, oa.quantity AS addon_quantity,
               c.firstname, c.lastname, c.contact_no
        FROM order_main o
        LEFT JOIN ordered_addons oa ON o.order_id = oa.order_id
        LEFT JOIN customer c ON o.customer_id = c.customer_id
    ";
    
    if ($search_query) {
        $query .= "WHERE o.order_id LIKE ? OR o.customer_id LIKE ? OR o.confirmation_status LIKE ? ";
        $search_param = '%' . $search_query . '%';
    }

    // No LIMIT or OFFSET applied
    $stmt = mysqli_prepare($conn, $query);

    if ($search_query) {
        mysqli_stmt_bind_param($stmt, 'sss', $search_param, $search_param, $search_param);
    }

    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);

    $orders = array();
    while ($row = mysqli_fetch_assoc($result)) {
        if (!isset($orders[$row['order_id']])) {
            $orders[$row['order_id']] = array(
                'order_id' => $row['order_id'],
                'customer_id' => $row['customer_id'],
                'firstname' => $row['firstname'],
                'lastname' => $row['lastname'],
                'contact_no' => $row['contact_no'],
                'description' => $row['description'],
                'total_amount' => $row['total_amount'],
                'total_orders' => $row['total_orders'],
                'confirmation_status' => $row['confirmation_status'],
                'pickup_date' => $row['pickup_date'],
                'pickup_time' => $row['pickup_time'],
                'addons' => array(),
            );
        }
        
        // Add addon data if available
        if ($row['addon_type']) {
            $orders[$row['order_id']]['addons'][] = array(
                'addon_type' => $row['addon_type'],
                'addon_name' => $row['addon_name'],
                'addon_price' => $row['addon_price'],
                'addon_quantity' => $row['addon_quantity'],
                'total_addon_price' => $row['addon_price'] * $row['addon_quantity'],
            );
        }
    }
    $orders = array_values($orders);

    // Get total count for all orders
    $totalQuery = "SELECT COUNT(*) as total FROM order_main";
    if ($search_query) {
        $totalQuery .= " WHERE order_id LIKE ? OR customer_id LIKE ? OR confirmation_status LIKE ?";
        $totalStmt = mysqli_prepare($conn, $totalQuery);
        mysqli_stmt_bind_param($totalStmt, 'sss', $search_param, $search_param, $search_param);
    } else {
        $totalStmt = mysqli_prepare($conn, $totalQuery);
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
mysqli_stmt_free_result($stmt);
mysqli_stmt_close($stmt);
mysqli_close($conn);
?>
