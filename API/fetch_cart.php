<?php
session_start(); // Start the session

include '../db_connection/db_con.php'; // Database connection

header('Content-Type: application/json'); // Set JSON header

// Check if customer_id is set in the session
if (!isset($_SESSION['customer_id'])) {
    echo json_encode([]); // Return an empty array if no customer_id is set
    exit; // Stop further execution
}

// Get customer_id from the session
$customer_id = $_SESSION['customer_id'];

// SQL to fetch saved cart items along with addons and their quantities
$sql = "SELECT sc.id AS saved_cart_id, sc.customer_id, sc.product_id, sc.description, sc.quantity, sc.price, sc.image,
               ca.id AS addon_id, ca.addon_type, ca.addon_name, ca.addon_price, ca.quantity AS addon_quantity
        FROM saved_cart sc 
        LEFT JOIN cart_addons ca ON sc.id = ca.saved_cart_id 
        WHERE sc.customer_id = ?";

$stmt = $conn->prepare($sql);

if ($stmt === false) {
    error_log("SQL Error: " . $conn->error);  // Log error for debugging
    echo json_encode(["error" => "Database query failed."]);
    exit;
}

$stmt->bind_param("i", $customer_id);
$stmt->execute();
$result = $stmt->get_result();

$cart_items = [];

// Collect cart items and their addons
while ($row = $result->fetch_assoc()) {
    // Check if the cart item already exists in the array
    if (!isset($cart_items[$row['saved_cart_id']])) {
        // Initialize the cart item if it doesn't exist
        $cart_items[$row['saved_cart_id']] = [
            'id' => $row['saved_cart_id'],
            'customer_id' => $row['customer_id'],
            'product_id' => $row['product_id'],
            'description' => $row['description'],
            'quantity' => $row['quantity'],
            'price' => $row['price'],
            'image' => $row['image'], // Assuming image needs to be included
            'addons' => [] // Initialize addons array
        ];
    }

    // Add addon information if exists
    if ($row['addon_id']) {
        $cart_items[$row['saved_cart_id']]['addons'][] = [
            'addon_id' => $row['addon_id'],
            'addon_type' => $row['addon_type'],
            'addon_name' => $row['addon_name'],
            'addon_price' => $row['addon_price'],
            'addon_quantity' => $row['addon_quantity'] // Include the addon quantity
        ];
    }
}

// Reset array keys to numeric
$cart_items = array_values($cart_items);

// Return the cart items as JSON
echo json_encode($cart_items);

$stmt->close();
$conn->close();
?>
