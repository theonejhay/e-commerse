<?php
include_once '../db_connection/db_con.php';

// Verify connection
if (!$conn) {
    die(json_encode(array('error' => 'Connection failed: ' . mysqli_connect_error())));
}

// Extract parameters
$order_id = isset($_GET['order_id']) ? intval($_GET['order_id']) : null;
$search_query = isset($_GET['search']) ? $_GET['search'] : null;
$start_date = isset($_GET['startDate']) ? $_GET['startDate'] : '';
$end_date = isset($_GET['endDate']) ? $_GET['endDate'] : '';

// Add date filtering to query if dates are provided
$date_filter = '';
if ($start_date && $end_date) {
    $date_filter = " AND co.pickup_date BETWEEN ? AND ?";
} elseif ($start_date) {
    $date_filter = " AND co.pickup_date >= ?";
} elseif ($end_date) {
    $date_filter = " AND co.pickup_date <= ?";
}

if ($order_id) {
    // Query to retrieve specific claimed order details with add-ons
    $query = "
        SELECT co.order_id, co.customer_id, co.description, co.total_amount, co.total_orders, 
               co.confirmation_status, co.pickup_date, co.pickup_time, co.status, 
               c.username, c.contact_no
        FROM claimed_order co
        LEFT JOIN customer c ON co.customer_id = c.customer_id
        WHERE co.order_id = ? $date_filter
    ";
    $stmt = mysqli_prepare($conn, $query);
    if ($date_filter) {
        if ($start_date && $end_date) {
            mysqli_stmt_bind_param($stmt, 'iss', $order_id, $start_date, $end_date);
        } elseif ($start_date) {
            mysqli_stmt_bind_param($stmt, 'is', $order_id, $start_date);
        } elseif ($end_date) {
            mysqli_stmt_bind_param($stmt, 'is', $order_id, $end_date);
        }
    } else {
        mysqli_stmt_bind_param($stmt, 'i', $order_id);
    }
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);

    // Fetch the order details
    $order = mysqli_fetch_assoc($result);

    // Fetch claimed add-ons for this order
    $addons_query = "
        SELECT addon_name, addon_type, addon_price, quantity, created_at
        FROM claimed_addons
        WHERE order_id = ?
    ";
    $addons_stmt = mysqli_prepare($conn, $addons_query);
    mysqli_stmt_bind_param($addons_stmt, 'i', $order_id);
    mysqli_stmt_execute($addons_stmt);
    $addons_result = mysqli_stmt_get_result($addons_stmt);

    $addons = [];
    while ($addon = mysqli_fetch_assoc($addons_result)) {
        $addons[] = $addon;
    }

    $order['addons'] = $addons;

    echo json_encode($order ? $order : array('error' => 'Order not found.'), JSON_PRETTY_PRINT);

} else {
    // Query to retrieve all claimed orders
    $query = "
        SELECT co.order_id, co.customer_id, co.description, co.total_amount, co.total_orders, 
               co.confirmation_status, co.pickup_date, co.pickup_time, co.status, 
               c.username, c.contact_no
        FROM claimed_order co
        LEFT JOIN customer c ON co.customer_id = c.customer_id
        WHERE 1=1 $date_filter
    ";

    if ($search_query) {
        $query .= " AND (co.order_id LIKE ? OR co.customer_id LIKE ? OR co.description LIKE ? OR co.confirmation_status LIKE ? OR co.status LIKE ? OR c.username LIKE ? OR c.contact_no LIKE ?)";
        $search_param = '%' . $search_query . '%';
    }

    // Prepare the statement
    $stmt = mysqli_prepare($conn, $query);
    if ($date_filter && $search_query) {
        mysqli_stmt_bind_param($stmt, 'ssssssss', $search_param, $search_param, $search_param, $search_param, $search_param, $search_param, $search_param);
    } elseif ($date_filter) {
        if ($start_date && $end_date) {
            mysqli_stmt_bind_param($stmt, 'ss', $start_date, $end_date);
        } elseif ($start_date) {
            mysqli_stmt_bind_param($stmt, 's', $start_date);
        } elseif ($end_date) {
            mysqli_stmt_bind_param($stmt, 's', $end_date);
        }
    } elseif ($search_query) {
        mysqli_stmt_bind_param($stmt, 'sssssss', $search_param, $search_param, $search_param, $search_param, $search_param, $search_param, $search_param);
    }

    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);

    // Collect orders into an array
    $orders = [];
    while ($row = mysqli_fetch_assoc($result)) {
        // Fetch claimed add-ons for each order
        $addons_query = "
            SELECT addon_name, addon_type, addon_price, quantity, created_at
            FROM claimed_addons
            WHERE order_id = ?
        ";
        $addons_stmt = mysqli_prepare($conn, $addons_query);
        mysqli_stmt_bind_param($addons_stmt, 'i', $row['order_id']);
        mysqli_stmt_execute($addons_stmt);
        $addons_result = mysqli_stmt_get_result($addons_stmt);

        $addons = [];
        while ($addon = mysqli_fetch_assoc($addons_result)) {
            $addons[] = $addon;
        }

        $row['addons'] = $addons;
        $orders[] = $row;
    }

    // Get total count of claimed orders
    $totalQuery = "SELECT COUNT(*) as total FROM claimed_order co LEFT JOIN customer c ON co.customer_id = c.customer_id WHERE 1=1 $date_filter";
    if ($search_query) {
        $totalQuery .= " AND (co.order_id LIKE ? OR co.customer_id LIKE ? OR co.description LIKE ? OR co.confirmation_status LIKE ? OR co.status LIKE ? OR c.username LIKE ? OR c.contact_no LIKE ?)";
    }
    $totalStmt = mysqli_prepare($conn, $totalQuery);
    if ($date_filter && $search_query) {
        mysqli_stmt_bind_param($totalStmt, 'ssssssss', $search_param, $search_param, $search_param, $search_param, $search_param, $search_param, $search_param);
    } elseif ($date_filter) {
        if ($start_date && $end_date) {
            mysqli_stmt_bind_param($totalStmt, 'ss', $start_date, $end_date);
        } elseif ($start_date) {
            mysqli_stmt_bind_param($totalStmt, 's', $start_date);
        } elseif ($end_date) {
            mysqli_stmt_bind_param($totalStmt, 's', $end_date);
        }
    } elseif ($search_query) {
        mysqli_stmt_bind_param($totalStmt, 'sssssss', $search_param, $search_param, $search_param, $search_param, $search_param, $search_param, $search_param);
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
mysqli_stmt_close($stmt);
mysqli_close($conn);
?>
