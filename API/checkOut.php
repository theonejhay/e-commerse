<?php
include '../db_connection/db_con.php';

// Decode the JSON data from the request
$data = json_decode(file_get_contents('php://input'), true);

$customer_id = $data['customer_id'];
$pickup_date = $data['pickupDate'];
$pickup_time = date("H:i:s", strtotime($data['pickupTime']));
$selectedProductIds = $data['selectedProductIds'];
$selectedAddonIds = $data['selectedAddonIds'];

// Verify required fields
if (empty($customer_id) || empty($pickup_date) || empty($pickup_time)) {
    echo json_encode(['success' => false, 'message' => 'All fields are required.']);
    exit;
}

$total_amount = 0;
$total_orders = 0;
$description = '';

// Fetch checked items from saved_cart
if (!empty($selectedProductIds)) {
    $sql_cart = "SELECT * FROM saved_cart WHERE customer_id = ? AND id IN (" . implode(',', array_fill(0, count($selectedProductIds), '?')) . ")";
    $stmt_cart = $conn->prepare($sql_cart);

    if (!$stmt_cart) {
        die("Prepare failed for cart: " . $conn->error);
    }

    $stmt_cart->bind_param(str_repeat("i", count($selectedProductIds) + 1), $customer_id, ...$selectedProductIds);
    $stmt_cart->execute();
    $result_cart = $stmt_cart->get_result();

    while ($row = $result_cart->fetch_assoc()) {
        $total_amount += $row['price'] * $row['quantity'];
        $description .= $row['description'] . ' (x' . $row['quantity'] . '), ';
        $total_orders++;

        $saved_cart_id = $row['id'];

        // Fetch addons for the selected products
        if (!empty($selectedAddonIds)) {
            $sql_addons = "SELECT * FROM cart_addons WHERE saved_cart_id = ? AND id IN (" . implode(',', array_fill(0, count($selectedAddonIds), '?')) . ")";
            $stmt_addons = $conn->prepare($sql_addons);

            if (!$stmt_addons) {
                die("Prepare failed for addons: " . $conn->error);
            }

            $stmt_addons->bind_param(str_repeat("i", count($selectedAddonIds) + 1), $saved_cart_id, ...$selectedAddonIds);
            $stmt_addons->execute();
            $result_addons = $stmt_addons->get_result();

            while ($addon = $result_addons->fetch_assoc()) {
                $total_amount += $addon['addon_price'] * $addon['quantity'];
                $description .= $addon['addon_name'] . ' (' . $addon['addon_type'] . '), ';
            }
            $stmt_addons->close();
        }
    }
    $stmt_cart->close();
}

// Insert the order into order_main if there are selected items
if ($total_orders > 0) {
    $description = rtrim($description, ', ');
    $sql_order_main = "INSERT INTO order_main (customer_id, description, total_amount, total_orders, confirmation_status,  pickup_date, pickup_time)
                       VALUES (?, ?, ?, ?, 'Pending',  ?, ?)";
    $stmt_order_main = $conn->prepare($sql_order_main);

    if (!$stmt_order_main) {
        die("Prepare failed for order_main: " . $conn->error);
    }

    $stmt_order_main->bind_param("isdiss", $customer_id, $description, $total_amount, $total_orders, $pickup_date, $pickup_time);

    if ($stmt_order_main->execute()) {
        $order_id = $stmt_order_main->insert_id;

        // Transfer checked addons to ordered_addons
        if (!empty($selectedProductIds)) {
            $sql_transfer_addons = "INSERT INTO ordered_addons (order_id, addon_name, addon_type, addon_price, quantity)
                                    SELECT ?, addon_name, addon_type, addon_price, quantity
                                    FROM cart_addons
                                    WHERE saved_cart_id IN (" . implode(',', array_fill(0, count($selectedProductIds), '?')) . ")";
            $stmt_transfer_addons = $conn->prepare($sql_transfer_addons);

            if (!$stmt_transfer_addons) {
                die("Prepare failed for transfer_addons: " . $conn->error);
            }

            $stmt_transfer_addons->bind_param("i" . str_repeat("i", count($selectedProductIds)), $order_id, ...$selectedProductIds);
            $stmt_transfer_addons->execute();
            $stmt_transfer_addons->close();

            // Delete the transferred addons from cart_addons
            $sql_delete_addons = "DELETE FROM cart_addons WHERE saved_cart_id IN (" . implode(',', array_fill(0, count($selectedProductIds), '?')) . ")";
            $stmt_delete_addons = $conn->prepare($sql_delete_addons);

            if (!$stmt_delete_addons) {
                die("Prepare failed for delete_addons: " . $conn->error);
            }

            $stmt_delete_addons->bind_param(str_repeat("i", count($selectedProductIds)), ...$selectedProductIds);
            $stmt_delete_addons->execute();
            $stmt_delete_addons->close();
        }

        // Delete the checked items from saved_cart
        $sql_delete_cart = "DELETE FROM saved_cart WHERE customer_id = ? AND id IN (" . implode(',', array_fill(0, count($selectedProductIds), '?')) . ")";
        $stmt_delete_cart = $conn->prepare($sql_delete_cart);

        if (!$stmt_delete_cart) {
            die("Prepare failed for delete_cart: " . $conn->error);
        }

        $stmt_delete_cart->bind_param(str_repeat("i", count($selectedProductIds) + 1), $customer_id, ...$selectedProductIds);
        $stmt_delete_cart->execute();
        $stmt_delete_cart->close();

        echo json_encode(['success' => true, 'message' => 'Order confirmed and data transferred successfully.']);
    } else {
        echo json_encode(['success' => false, 'message' => 'Order insertion failed.']);
    }

    $stmt_order_main->close();
} else {
    echo json_encode(['success' => false, 'message' => 'No selected items to order.']);
}

$conn->close();
?>
