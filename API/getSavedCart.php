<?php
header('Content-Type: application/json');
include_once '../db_connection/db_con.php';

// Get the customer_id from the request
$customer_id = $_GET['customer_id'] ?? null;

if (empty($customer_id)) {
    echo json_encode(['success' => false, 'message' => 'Customer ID is missing']);
    exit();
}

try {
    // Fetch saved cart items from the database for the given customer_id
    $stmt = $conn->prepare("
        SELECT sc.id, sc.product_id, sc.description, sc.quantity, sc.price, sc.image, sc.created_at,
               ca.id as addon_id,  -- Select the addon ID
               ca.addon_type, ca.addon_name, ca.addon_price, ca.quantity as addon_quantity, ca.created_at as addon_created_at
        FROM saved_cart sc
        LEFT JOIN cart_addons ca ON sc.id = ca.saved_cart_id
        WHERE sc.customer_id = ?
    ");
    $stmt->bind_param("i", $customer_id);
    $stmt->execute();
    $result = $stmt->get_result();

    // Initialize an array to store cart items with addons
    $cartItems = [];

    // Process each row and group addons under the respective cart item
    while ($row = $result->fetch_assoc()) {
        $saved_cart_id = $row['id'];

        if (!isset($cartItems[$saved_cart_id])) {
            // Initialize cart item with its main details
            $cartItems[$saved_cart_id] = [
                'id' => $row['id'],
                'product_id' => $row['product_id'],
                'description' => $row['description'],
                'quantity' => $row['quantity'],
                'price' => $row['price'],
                'image' => $row['image'],
                'created_at' => $row['created_at'],
                'addons' => []
            ];
        }

        // Append addon details if they exist
        if ($row['addon_type'] !== null) {
            $cartItems[$saved_cart_id]['addons'][] = [
                'addon_id' => $row['addon_id'],  // Include addon ID
                'addon_type' => $row['addon_type'],
                'addon_name' => $row['addon_name'],
                'addon_price' => $row['addon_price'],
                'addon_quantity' => $row['addon_quantity'],
                'created_at' => $row['addon_created_at']
            ];
        }
    }

    // Reindex the array to return a JSON list
    $cartItems = array_values($cartItems);

    echo json_encode(['success' => true, 'cartItems' => $cartItems]);

} catch (Exception $e) {
    echo json_encode(['success' => false, 'message' => 'Error fetching cart data: ' . $e->getMessage()]);
}
