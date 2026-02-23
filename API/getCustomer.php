<?php
include_once '../db_connection/db_con.php';

if (!$conn) {
    die(json_encode(array('error' => 'Connection failed: ' . mysqli_connect_error())));
}

// Get the raw POST data
$post_data = file_get_contents('php://input');
$request = json_decode($post_data, true);

// Extract parameters
$customer_id = isset($request['customer_id']) ? intval($request['customer_id']) : null;
$search_query = isset($request['search']) ? $request['search'] : null;
$page = isset($request['page']) ? intval($request['page']) : 1; // Default to page 1
$limit = isset($request['limit']) ? intval($request['limit']) : 10; // Default limit
$offset = ($page - 1) * $limit; // Calculate offset

if ($customer_id) {
    // Query to retrieve the specific customer by customer_id
    $query = 'SELECT customer_id, firstname, lastname, contact_no, username, password, profile_image FROM customer WHERE customer_id = ?';
    $stmt = mysqli_prepare($conn, $query);
    mysqli_stmt_bind_param($stmt, 'i', $customer_id);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);
    
    // Fetch the customer data
    $customer = mysqli_fetch_assoc($result);
    if ($customer) {
        if ($customer['profile_image']) {
            $customer['profile_image'] = base64_encode($customer['profile_image']);
        }
        echo json_encode($customer);
    } else {
        echo json_encode(array('error' => 'Customer not found.'));
    }

} else {
    // Query to retrieve customers based on search query with pagination
    if ($search_query) {
        $query = 'SELECT customer_id, firstname, lastname, contact_no, username,password, profile_image FROM customer WHERE firstname LIKE ? OR lastname LIKE ? OR username LIKE ? LIMIT ? OFFSET ?';
        $stmt = mysqli_prepare($conn, $query);
        $search_param = '%' . $search_query . '%';
        mysqli_stmt_bind_param($stmt, 'ssiii', $search_param, $search_param, $search_param, $limit, $offset);
    } else {
        // Query to retrieve all records from the customer table with pagination
        $query = 'SELECT customer_id, firstname, lastname, contact_no, username, password, profile_image FROM customer LIMIT ? OFFSET ?';
        $stmt = mysqli_prepare($conn, $query);
        mysqli_stmt_bind_param($stmt, 'ii', $limit, $offset);
    }
    
    // Execute the prepared statement
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);

    if (!$result) {
        die(json_encode(array('error' => 'Query failed: ' . mysqli_error($conn))));
    }

    // Fetch records and store them in an array
    $customers = array();
    while ($row = mysqli_fetch_assoc($result)) {
        if ($row['profile_image']) {
            $row['profile_image'] = base64_encode($row['profile_image']);
        }
        $customers[] = $row;
    }

    // Get the total number of customers for pagination
    if ($search_query) {
        $totalQuery = 'SELECT COUNT(*) as total FROM customer WHERE firstname LIKE ? OR lastname LIKE ? OR username LIKE ?';
        $totalStmt = mysqli_prepare($conn, $totalQuery);
        mysqli_stmt_bind_param($totalStmt, 'sss', $search_param, $search_param, $search_param);
        mysqli_stmt_execute($totalStmt);
        $totalResult = mysqli_stmt_get_result($totalStmt);
        $total = mysqli_fetch_assoc($totalResult)['total'];
    } else {
        $totalQuery = 'SELECT COUNT(*) as total FROM customer';
        $totalResult = mysqli_query($conn, $totalQuery);
        $total = mysqli_fetch_assoc($totalResult)['total'];
    }

    // Prepare the response with customers and total count
    $response = array(
        'customers' => $customers,
        'total' => $total,
        'page' => $page,
        'limit' => $limit
    );

    // Set header and return JSON response
    header('Content-Type: application/json');
    echo json_encode($response, JSON_PRETTY_PRINT);

    // Free the result
    mysqli_stmt_free_result($stmt);
    mysqli_stmt_close($stmt);
}

// Close the database connection
mysqli_close($conn);
?>
