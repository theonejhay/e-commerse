<?php
include_once '../db_connection/db_con.php';

if (!$conn) {
    die(json_encode(array('error' => 'Connection failed: ' . mysqli_connect_error())));
}

// Check if the product_id parameter is provided
$product_id = isset($_GET['product_id']) ? intval($_GET['product_id']) : null;

if ($product_id) {
    // Query to retrieve the specific product by product_id
    $query = 'SELECT * FROM product_maintenance WHERE product_id = ?';
    $stmt = mysqli_prepare($conn, $query);
    mysqli_stmt_bind_param($stmt, 'i', $product_id);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);
} else {
    // Query to retrieve all records from the product_maintenance table
    $query = 'SELECT * FROM product_maintenance';
    $result = mysqli_query($conn, $query);
}

if (!$result) {
    die(json_encode(array('error' => 'Query failed: ' . mysqli_error($conn))));
}

// Fetch records and store them in an array
$products = array();
while ($row = mysqli_fetch_assoc($result)) {
    // Check if the image column exists and process it
    if (isset($row['image'])) {
        $row['image'] = base64_encode($row['image']);
    }
    $products[] = $row;
}

// Encode the array as JSON and display it
header('Content-Type: application/json');
echo json_encode($products, JSON_PRETTY_PRINT);

// Free the result and close the connection
if ($product_id) {
    mysqli_stmt_free_result($stmt);
    mysqli_stmt_close($stmt);
} else {
    mysqli_free_result($result);
}
mysqli_close($conn);
?>
