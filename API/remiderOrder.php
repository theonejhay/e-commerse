<?php
include_once '../db_connection/db_con.php';

header('Content-Type: application/json');

// Verify connection
if (!$conn) {
    die(json_encode(array('error' => 'Connection failed: ' . mysqli_connect_error())));
}

// Extract customer_id from session
session_start();
$customer_id = $_SESSION['customer_id'] ?? null;

if (!$customer_id) {
    echo json_encode(array('error' => 'User not logged in.'));
    exit;
}

// Query to fetch all orders with relevant statuses and their add-ons for the logged-in customer
$query = "
    SELECT 
        co.order_id, co.description AS product_description, co.confirmation_status, 
        co.pickup_date, co.pickup_time, 
        oa.addon_type, oa.addon_name, oa.addon_price, oa.quantity AS addon_quantity
    FROM order_main co
    LEFT JOIN ordered_addons oa ON co.order_id = oa.order_id
    WHERE co.customer_id = ? 
      AND co.confirmation_status IN ('Pending', 'Confirmed', 'Cooking', 'ready-to-claim')
    ORDER BY co.pickup_date DESC, co.pickup_time DESC
";

$stmt = mysqli_prepare($conn, $query);
mysqli_stmt_bind_param($stmt, 'i', $customer_id);
mysqli_stmt_execute($stmt);
$result = mysqli_stmt_get_result($stmt);

$orders = array();

while ($row = mysqli_fetch_assoc($result)) {
    // If the order doesn't exist in the array, add it
    if (!isset($orders[$row['order_id']])) {
        $orders[$row['order_id']] = array(
            'order_id' => $row['order_id'],
            'product_description' => $row['product_description'],
            'confirmation_status' => $row['confirmation_status'],
            'pickup_date' => $row['pickup_date'],
            'pickup_time' => $row['pickup_time'],
            'addons' => array(),
            'total_addon_price' => 0, // Initialize the total add-on price
        );
    }

    // If add-ons exist, add them to the order
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
}

// Reset array keys to sequential
$orders = array_values($orders);

// Prepare the response with orders
if (count($orders) > 0) {
    echo json_encode($orders, JSON_PRETTY_PRINT);
} else {
    echo json_encode(array('message' => 'No orders found.'));
}

// Close resources
mysqli_stmt_close($stmt);
mysqli_close($conn);
?>
