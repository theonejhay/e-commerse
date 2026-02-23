<?php
session_start();
include_once '../db_connection/db_con.php';

// Enable error reporting for debugging purposes (remove in production)
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

// Ensure the content type is JSON
header('Content-Type: application/json');

// Check database connection
if (!$conn) {
    echo json_encode(array('error' => 'Connection failed: ' . mysqli_connect_error()));
    exit();
}

// Check if customer_id is set in session
if (!isset($_SESSION['customer_id'])) {
    echo json_encode(array('error' => 'Customer ID not found in session.'));
    exit();
}

// Get customer_id from session
$customer_id = $_SESSION['customer_id'];

// Get the raw POST data
$post_data = file_get_contents('php://input');
$request = json_decode($post_data, true);

// Check if request is valid JSON
if (!$request) {
    echo json_encode(array('error' => 'Invalid JSON input.'));
    exit();
}

// Extract data from the request
$username = isset($request['username']) ? trim($request['username']) : null;
$password = isset($request['password']) ? trim($request['password']) : null;
$contact_no = isset($request['contact_no']) ? trim($request['contact_no']) : null;
$profile_image = isset($request['profile_image']) && is_array($request['profile_image']) ? $request['profile_image'] : null;

// Initialize the query and parameters for optional updates
$query = "UPDATE customer SET ";
$params = [];
$types = "";

// Only include username in the update query if it's provided
if ($username) {
    $query .= "username = ?, ";
    $params[] = $username;
    $types .= "s";
}

// Only include password in the update query if it's provided
if ($password) {
    $password_hashed = password_hash($password, PASSWORD_DEFAULT);  // Hash the password
    $query .= "password = ?, ";
    $params[] = $password_hashed;
    $types .= "s";
}

// Only include contact_no in the update query if it's provided
if ($contact_no) {
    $query .= "contact_no = ?, ";
    $params[] = $contact_no;
    $types .= "s";
}

// If a profile image is uploaded, include it in the update query
if ($profile_image) {
    // Validate that profile_image data exists
    if (!isset($profile_image['data']) || !preg_match('/^[a-zA-Z0-9\/\r\n+]*={0,2}$/', $profile_image['data'])) {
        echo json_encode(array('error' => 'Invalid image format.'));
        exit();
    }

    // Decode the image data from base64
    $image_data = base64_decode($profile_image['data']);
    if ($image_data === false) {
        echo json_encode(array('error' => 'Failed to decode the image.'));
        exit();
    }

    // Check the file size (5MB limit)
    if (strlen($image_data) > 5 * 1024 * 1024) { // 5MB = 5 * 1024 * 1024 bytes
        echo json_encode(array('error' => 'Image size exceeds the 5MB limit.'));
        exit();
    }

    // If valid, add the profile image to the query
    $query .= "profile_image = ? ";
    $params[] = $image_data;
    $types .= "b";
}

// Remove the trailing comma and space from the query
$query = rtrim($query, ", ");

// Add the WHERE condition for customer_id
$query .= " WHERE customer_id = ?";
$params[] = $customer_id;
$types .= "i";

// Prepare the statement
$stmt = mysqli_prepare($conn, $query);

if (!$stmt) {
    echo json_encode(array('error' => 'Error preparing statement: ' . mysqli_error($conn)));
    exit();
}

// Dynamically bind parameters to the statement
$bind_names[] = $types;
for ($i = 0; $i < count($params); $i++) {
    $bind_name = 'bind' . $i;
    $$bind_name = $params[$i];
    $bind_names[] = &$$bind_name;
}

call_user_func_array(array($stmt, 'bind_param'), $bind_names);

// Send the blob data separately if profile image is being updated
if ($profile_image) {
    mysqli_stmt_send_long_data($stmt, array_search($image_data, $params), $image_data);
}

// Execute the statement and handle errors
if (mysqli_stmt_execute($stmt)) {
    echo json_encode(array('success' => 'Profile updated successfully.'));
} else {
    echo json_encode(array('error' => 'Error updating profile: ' . mysqli_stmt_error($stmt)));
}

// Close the statement and connection
mysqli_stmt_close($stmt);
mysqli_close($conn);
?>
