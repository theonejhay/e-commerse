<?php
// Database connection
include '../db_connection/db_con.php';

if (!$conn) {
    die("Database connection failed: " . mysqli_connect_error());
}

// Get the JSON request body
$data = json_decode(file_get_contents('php://input'), true);
$order_id = $data['order_id'];

// Function to update claimed status
function updateClaimedStatus($conn, $order_id) {
    $queryStatus = "UPDATE claimed_order SET status = 'claimed' WHERE order_id = ?";
    $stmtStatus = $conn->prepare($queryStatus);

    if (!$stmtStatus) {
        die("Prepare failed for status UPDATE query: " . $conn->error);
    }

    $stmtStatus->bind_param("i", $order_id);
    if (!$stmtStatus->execute()) {
        die("Error updating claimed status: " . $stmtStatus->error);
    }
}

if ($order_id) {
    // Check if the order already exists in the claimed_order table
    $checkQuery = "SELECT COUNT(*) as count FROM claimed_order WHERE order_id = ?";
    $stmtCheck = $conn->prepare($checkQuery);
    $stmtCheck->bind_param("i", $order_id);
    $stmtCheck->execute();
    $result = $stmtCheck->get_result();
    $row = $result->fetch_assoc();

    if ($row['count'] > 0) {
        // Update the existing record instead of inserting a new one
        $queryUpdate = "UPDATE claimed_order 
                        SET customer_id = (SELECT customer_id FROM order_main WHERE order_id = ?),
                            description = (SELECT description FROM order_main WHERE order_id = ?),
                            total_amount = (SELECT total_amount FROM order_main WHERE order_id = ?),
                            total_orders = (SELECT total_orders FROM order_main WHERE order_id = ?),
                            confirmation_status = (SELECT confirmation_status FROM order_main WHERE order_id = ?),
                            pickup_date = (SELECT pickup_date FROM order_main WHERE order_id = ?),
                            pickup_time = (SELECT pickup_time FROM order_main WHERE order_id = ?)
                        WHERE order_id = ?";
        $stmtUpdate = $conn->prepare($queryUpdate);

        if (!$stmtUpdate) {
            die("Prepare failed for UPDATE query: " . $conn->error);
        }

        $stmtUpdate->bind_param("iiiiiiiii", $order_id, $order_id, $order_id, $order_id, $order_id, $order_id, $order_id, $order_id);
        if ($stmtUpdate->execute()) {
            // Update claimed status
            updateClaimedStatus($conn, $order_id);

            // Transfer addons
            $queryAddons = "INSERT INTO claimed_addons (order_id, addon_name, addon_type, addon_price, quantity, created_at)
                            SELECT order_id, addon_name, addon_type, addon_price, quantity, created_at
                            FROM ordered_addons WHERE order_id = ?";
            $stmtAddons = $conn->prepare($queryAddons);
            $stmtAddons->bind_param("i", $order_id);
            $stmtAddons->execute();

            // Remove from original tables
            $query2 = "DELETE FROM order_main WHERE order_id = ?";
            $stmt2 = $conn->prepare($query2);
            $stmt2->bind_param("i", $order_id);
            $stmt2->execute();

            $queryDeleteAddons = "DELETE FROM ordered_addons WHERE order_id = ?";
            $stmtDeleteAddons = $conn->prepare($queryDeleteAddons);
            $stmtDeleteAddons->bind_param("i", $order_id);
            $stmtDeleteAddons->execute();

            echo json_encode(["success" => true]);
        } else {
            echo json_encode(["success" => false, "error" => $conn->error]);
        }
    } else {
        // If the order doesn't exist in claimed_order, perform the insert
        $query1 = "INSERT INTO claimed_order (order_id, customer_id, description, total_amount, total_orders, confirmation_status, pickup_date, pickup_time)
                   SELECT order_id, customer_id, description, total_amount, total_orders, confirmation_status, pickup_date, pickup_time
                   FROM order_main WHERE order_id = ?";
        $stmt1 = $conn->prepare($query1);

        if (!$stmt1) {
            die("Prepare failed for INSERT query: " . $conn->error);
        }

        $stmt1->bind_param("i", $order_id);

        if ($stmt1->execute()) {
            // Update claimed status
            updateClaimedStatus($conn, $order_id);

            // Transfer addons
            $queryAddons = "INSERT INTO claimed_addons (order_id, addon_name, addon_type, addon_price, quantity, created_at)
                            SELECT order_id, addon_name, addon_type, addon_price, quantity, created_at
                            FROM ordered_addons WHERE order_id = ?";
            $stmtAddons = $conn->prepare($queryAddons);
            $stmtAddons->bind_param("i", $order_id);
            $stmtAddons->execute();

            // Remove from original tables
            $query2 = "DELETE FROM order_main WHERE order_id = ?";
            $stmt2 = $conn->prepare($query2);
            $stmt2->bind_param("i", $order_id);
            $stmt2->execute();

            $queryDeleteAddons = "DELETE FROM ordered_addons WHERE order_id = ?";
            $stmtDeleteAddons = $conn->prepare($queryDeleteAddons);
            $stmtDeleteAddons->bind_param("i", $order_id);
            $stmtDeleteAddons->execute();

            echo json_encode(["success" => true]);
        } else {
            echo json_encode(["success" => false, "error" => $conn->error]);
        }
    }
} else {
    echo json_encode(["success" => false, "message" => "Invalid order_id"]);
}
?>
