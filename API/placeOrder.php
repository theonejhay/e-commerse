<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

header('Content-Type: application/json');

include_once '../db_connection/db_con.php';

// Get the POST data
$data = json_decode(file_get_contents('php://input'), true);

// Log the incoming data (for debugging purposes)
error_log(print_r($data, true));

$customer_id = $data['customer_id'];
$cart = $data['cart'];

if (empty($customer_id) || empty($cart)) {
    echo json_encode(['success' => false, 'message' => 'Invalid customer ID or empty cart']);
    exit();
}

try {
    // Begin transaction
    $conn->begin_transaction();

    // Prepare the SQL query to insert each cart item into the saved_cart table
    $stmtInsert = $conn->prepare("
        INSERT INTO saved_cart (customer_id, product_id, description, quantity, price, image, created_at, updated_at)
        VALUES (?, ?, ?, ?, ?, ?, NOW(), NOW())
    ");

    // Prepare the SQL query to insert add-ons into a separate cart_addons table
    $stmtInsertAddon = $conn->prepare("
        INSERT INTO cart_addons (saved_cart_id, addon_type, addon_name, addon_price, quantity, created_at)
        VALUES (?, ?, ?, ?, ?, NOW())
    ");

    foreach ($cart as $product_id => $item) {
        // Insert product data into the saved_cart table
        $stmtInsert->bind_param("iisiis", $customer_id, $product_id, $item['description'], $item['quantity'], $item['price'], $item['image']);
        $stmtInsert->execute();

        // Get the saved_cart_id of the last inserted product
        $saved_cart_id = $stmtInsert->insert_id;

        // Insert sauce add-ons, if any
        if (!empty($item['addons']['sauces'])) {
            foreach ($item['addons']['sauces'] as $sauce) {
                $addon_type = 'sauce';
                $stmtInsertAddon->bind_param("issdi", $saved_cart_id, $addon_type, $sauce['name'], $sauce['price'], $sauce['quantity']); // Added quantity
                $stmtInsertAddon->execute();
            }
        }

        // Insert drink add-ons, if any
        if (!empty($item['addons']['drinks'])) {
            foreach ($item['addons']['drinks'] as $drink) {
                $addon_type = 'drink';
                $stmtInsertAddon->bind_param("issdi", $saved_cart_id, $addon_type, $drink['name'], $drink['price'], $drink['quantity']); // Added quantity
                $stmtInsertAddon->execute();
            }
        }
    }

    // After insertion, delete the items from the cart table
    $stmtDelete = $conn->prepare("
        DELETE FROM cart
        WHERE customer_id = ?
    ");
    $stmtDelete->bind_param("i", $customer_id);
    $stmtDelete->execute();

    // Commit transaction
    $conn->commit();

    echo json_encode(['success' => true]);

} catch (Exception $e) {
    // Rollback transaction in case of error
    $conn->rollback();
    echo json_encode(['success' => false, 'message' => 'Error placing order: ' . $e->getMessage()]);
}
?>
