<?php
session_start();
include_once '../db_connection/db_con.php'; 

// Verify connection
if (!$conn) {
    die(json_encode(array('error' => 'Connection failed: ' . mysqli_connect_error())));
}

// Check if customer_id is set in session
if (!isset($_SESSION['customer_id'])) {
    die(json_encode(array('error' => 'Customer not logged in.')));
}

$customer_id = $_SESSION['customer_id'];

// Query to retrieve all orders for the customer (no pagination)
$query = "
    SELECT o.order_id, o.description AS product_description, o.total_amount, 
           o.confirmation_status, o.pickup_date, o.pickup_time, 
           oa.addon_type, oa.addon_name, oa.addon_price, oa.quantity AS addon_quantity
    FROM order_main o
    LEFT JOIN ordered_addons oa ON o.order_id = oa.order_id
    WHERE o.customer_id = ?
";
$stmt = mysqli_prepare($conn, $query);
mysqli_stmt_bind_param($stmt, 'i', $customer_id);

// Execute the prepared statement
mysqli_stmt_execute($stmt);
$result = mysqli_stmt_get_result($stmt);

if (!$result) {
    die(json_encode(array('error' => 'Query failed: ' . mysqli_error($conn))));
}

// Fetch records and store them in an array
$orders = array();
while ($row = mysqli_fetch_assoc($result)) {
    // Check if order already exists in the orders array
    if (!isset($orders[$row['order_id']])) {
        $orders[$row['order_id']] = array(
            'order_id' => $row['order_id'],
            'product_description' => $row['product_description'],
            'total_amount' => $row['total_amount'],
            'confirmation_status' => $row['confirmation_status'],
            'pickup_date' => $row['pickup_date'],
            'pickup_time' => $row['pickup_time'],
            'addons' => array(),
            'total_addon_price' => 0,
            'final_total' => $row['total_amount'], // Initialize final total with the base amount
        );
    }

    // If there are addons, add them to the order
    if ($row['addon_type']) {
        $addon_total_price = $row['addon_price'] * $row['addon_quantity'];
        $orders[$row['order_id']]['addons'][] = array(
            'addon_type' => $row['addon_type'],
            'addon_name' => $row['addon_name'],
            'addon_price' => $row['addon_price'],
            'addon_quantity' => $row['addon_quantity'],
            'total_addon_price' => $addon_total_price,
        );
        $orders[$row['order_id']]['total_addon_price'] += $addon_total_price; // Accumulate addon prices
    }

    // Update final total to include addons
    $orders[$row['order_id']]['final_total'] = $orders[$row['order_id']]['total_amount'] + $orders[$row['order_id']]['total_addon_price'];
}

// Reset array keys to sequential
$orders = array_values($orders);

// Prepare the response with orders
$response = array(
    'orders' => $orders,
    'customer_id' => $customer_id // Add customer ID to the response
);

// Set header and return JSON response
header('Content-Type: application/json');
echo json_encode($response, JSON_PRETTY_PRINT);

// Free the result and close the statement
mysqli_stmt_free_result($stmt);
mysqli_stmt_close($stmt);

// Close the database connection
mysqli_close($conn);
?>
