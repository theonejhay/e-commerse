<?php
session_start();
include_once '../db_connection/db_con.php';

if (!$conn) {
    die(json_encode(array('error' => 'Connection failed: ' . mysqli_connect_error())));
}

// Check if customer_id is set in session
if (!isset($_SESSION['customer_id'])) {
    echo json_encode(array('error' => 'Customer ID not found in session.'));
    exit();
}

// Get customer_id from session
$customer_id = $_SESSION['customer_id'];

// Query to retrieve the username, password, and profile_image based on customer_id
$query = 'SELECT username, password, contact_no, profile_image FROM customer WHERE customer_id = ?';
$stmt = mysqli_prepare($conn, $query);
mysqli_stmt_bind_param($stmt, 'i', $customer_id);
mysqli_stmt_execute($stmt);
$result = mysqli_stmt_get_result($stmt);

// Fetch the customer data
$customer = mysqli_fetch_assoc($result);
if ($customer) {
    // Check if there is a profile image and convert it to base64
    if (!empty($customer['profile_image'])) {
        $customer['profile_image'] = base64_encode($customer['profile_image']);
    }
    
    // Return the customer data as JSON
    echo json_encode($customer);
} else {
    // Return an error if the customer is not found
    echo json_encode(array('error' => 'Customer not found.'));
}

// Free the result and close the statement
mysqli_stmt_free_result($stmt);
mysqli_stmt_close($stmt);

// Close the database connection
mysqli_close($conn);
?>
