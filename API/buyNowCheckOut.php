<?php
include '../db_connection/db_con.php';

// Retrieve and decode the JSON data from the request
$data = json_decode(file_get_contents('php://input'), true);

// Log the incoming data for debugging
error_log("Incoming data: " . print_r($data, true));

$customer_id = isset($data['customer_id']) ? $data['customer_id'] : null;
$pickup_date = isset($data['pickupDate']) ? $data['pickupDate'] : null;
$pickup_time = isset($data['pickupTime']) ? $data['pickupTime'] : null;
$addons = isset($data['addons']) ? $data['addons'] : []; // Retrieve nested add-ons by product

// Validate required fields
if (empty($customer_id) || empty($pickup_date) || empty($pickup_time)) {
    echo json_encode(['success' => false, 'message' => 'Customer ID, pickup date, and pickup time are required.']);
    exit;
}

// Fetch cart items for the customer
$sql_fetch_cart = "SELECT product_id, price, quantity, description FROM cart WHERE customer_id = ?";
$stmt_fetch_cart = $conn->prepare($sql_fetch_cart);
$stmt_fetch_cart->bind_param("i", $customer_id);
$stmt_fetch_cart->execute();
$result = $stmt_fetch_cart->get_result();

$cartItems = [];
while ($row = $result->fetch_assoc()) {
    $cartItems[] = $row;
}
$stmt_fetch_cart->close();

// Check if the cart is empty
if (empty($cartItems)) {
    echo json_encode(['success' => false, 'message' => 'Cart is empty.']);
    exit;
}

$total_amount = 0;
$total_orders = 0;
$description = '';

// Calculate the total amount and create order description
foreach ($cartItems as $item) {
    $total_amount += $item['price'] * $item['quantity'];
    $description .= $item['description'] . ' (x' . $item['quantity'] . '), ';
    $total_orders++;
}

$description = rtrim($description, ', '); // Remove the last comma

// Insert the order into order_main
$sql_order_main = "INSERT INTO order_main (customer_id, description, total_amount, total_orders, confirmation_status, pickup_date, pickup_time)
                   VALUES (?, ?, ?, ?, 'Pending', ?, ?)";
$stmt_order_main = $conn->prepare($sql_order_main);

if ($stmt_order_main) {
    $stmt_order_main->bind_param("isdiss", $customer_id, $description, $total_amount, $total_orders, $pickup_date, $pickup_time);

    if ($stmt_order_main->execute()) {
        // Get the new order ID
        $order_id = $stmt_order_main->insert_id;

        if (!empty($addons)) {
            $sql_insert_addons = "INSERT INTO ordered_addons (order_id, addon_name, addon_type, addon_price, quantity) 
                                  VALUES (?, ?, ?, ?, ?)";
            $stmt_insert_addons = $conn->prepare($sql_insert_addons);
        
            foreach ($addons as $product_id => $addonList) { // Loop through products
                foreach ($addonList as $addonName => $addon) { // Loop through add-ons for each product
                    $addon_name = $addon['name'];
                    $addon_type = $addon['type'];
                    $addon_price = $addon['price'];
                    $addon_quantity = $addon['quantity'];
        
                    // Bind and execute the statement for each add-on
                    $stmt_insert_addons->bind_param("issdi", $order_id, $addon_name, $addon_type, $addon_price, $addon_quantity);
                    $stmt_insert_addons->execute();
                }
            }
        
            $stmt_insert_addons->close();
        }
        

        // Delete items from cart for this customer
        $sql_delete_cart = "DELETE FROM cart WHERE customer_id = ?";
        $stmt_delete_cart = $conn->prepare($sql_delete_cart);
        $stmt_delete_cart->bind_param("i", $customer_id);
        $stmt_delete_cart->execute();
        $stmt_delete_cart->close();

        echo json_encode(['success' => true, 'message' => 'Order confirmed successfully.']);
    } else {
        echo json_encode(['success' => false, 'message' => 'Error executing order insertion: ' . $stmt_order_main->error]);
    }

    $stmt_order_main->close();
} else {
    echo json_encode(['success' => false, 'message' => 'SQL prepare failed for order_main: ' . $conn->error]);
}

$conn->close();
?>
