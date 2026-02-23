<?php
include_once '../db_connection/db_con.php';

if (!$conn) {
    http_response_code(500);
    echo json_encode(["message" => "Connection failed: " . mysqli_connect_error()]);
    exit();
}

// Define the SQL query to fetch categories
$query = "SELECT category_id, category_name FROM categories";
$result = mysqli_query($conn, $query);

// Check if the query was successful
if (!$result) {
    http_response_code(500);
    echo json_encode(["message" => "Database query failed: " . mysqli_error($conn)]);
    exit();
}

// Fetch categories
$categories = [];
while ($row = mysqli_fetch_assoc($result)) {
    $categories[] = $row;
}

// Close the database connection
mysqli_close($conn);

// Output categories as JSON
echo json_encode($categories);
