<?php
require_once '../db_connection/db_con.php';

// Fetch categories from the categories table
$sql = "SELECT category_name FROM categories";
$result = $conn->query($sql);

// Initialize an array to store category names
$categories = [];

if ($result->num_rows > 0) {
    while($row = $result->fetch_assoc()) {
        $categories[] = $row["category_name"];
    }
} else {
    $categories = []; // No categories found
}

$conn->close();

// Return categories as JSON
echo json_encode($categories);
?>
