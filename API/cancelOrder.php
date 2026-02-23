<?php
include_once '../db_connection/db_con.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $inputData = json_decode(file_get_contents('php://input'), true);
    $order_id = isset($inputData['order_id']) ? intval($inputData['order_id']) : null;
    $remarks = isset($inputData['remarks']) ? $inputData['remarks'] : '';
    
    if (empty($order_id)) {
        http_response_code(400);
        echo json_encode(["message" => "Invalid input: order_id is required"]);
        exit();
    }

    if (!$conn) {
        http_response_code(500);
        echo json_encode(["message" => "Connection failed: " . mysqli_connect_error()]);
        exit();
    }

    mysqli_begin_transaction($conn);

    try {
        // Fetch order and customer details
        $fetchOrderDetailsQuery = "SELECT 
        o.customer_id, o.description, o.total_amount, 
        o.total_orders, o.confirmation_status, o.pickup_date, 
        o.pickup_time, c.username, c.contact_no
        FROM 
        order_main o
        JOIN 
        customer c ON o.customer_id = c.customer_id
        WHERE 
        o.order_id = ?";
        
        $fetchOrderStmt = mysqli_prepare($conn, $fetchOrderDetailsQuery);
        if (!$fetchOrderStmt) {
            throw new Exception("Failed to prepare fetch order details statement: " . mysqli_error($conn));
        }
        mysqli_stmt_bind_param($fetchOrderStmt, 'i', $order_id);
        mysqli_stmt_execute($fetchOrderStmt);
        mysqli_stmt_bind_result($fetchOrderStmt, $customer_id, $description, $total_amount, $total_orders, $confirmation_status, $pickup_date, $pickup_time, $username, $contact_no);

        if (!mysqli_stmt_fetch($fetchOrderStmt)) {
            throw new Exception("Order not found for given order_id");
        }
        mysqli_stmt_close($fetchOrderStmt);

        // Move addons to cancelled_addons table
        $fetchAddonsQuery = "SELECT addon_name, addon_type, addon_price, quantity, created_at 
                             FROM ordered_addons 
                             WHERE order_id = ?";
        $fetchAddonsStmt = mysqli_prepare($conn, $fetchAddonsQuery);
        if (!$fetchAddonsStmt) {
            throw new Exception("Failed to prepare fetch addons statement: " . mysqli_error($conn));
        }
        mysqli_stmt_bind_param($fetchAddonsStmt, 'i', $order_id);
        mysqli_stmt_execute($fetchAddonsStmt);
        mysqli_stmt_store_result($fetchAddonsStmt);

        if (mysqli_stmt_num_rows($fetchAddonsStmt) > 0) {
            mysqli_stmt_bind_result($fetchAddonsStmt, $addon_name, $addon_type, $addon_price, $quantity, $created_at);

            $insertCancelledAddonQuery = "INSERT INTO cancelled_addons 
                                         (order_id, addon_name, addon_type, addon_price, quantity, created_at) 
                                         VALUES (?, ?, ?, ?, ?, ?)";
            $insertCancelledAddonStmt = mysqli_prepare($conn, $insertCancelledAddonQuery);
            if (!$insertCancelledAddonStmt) {
                throw new Exception("Failed to prepare insert cancelled addons statement: " . mysqli_error($conn));
            }

            while (mysqli_stmt_fetch($fetchAddonsStmt)) {
                mysqli_stmt_bind_param($insertCancelledAddonStmt, 'issdis', $order_id, $addon_name, $addon_type, $addon_price, $quantity, $created_at);
                mysqli_stmt_execute($insertCancelledAddonStmt);
            }
            mysqli_stmt_close($insertCancelledAddonStmt);
        }

        mysqli_stmt_close($fetchAddonsStmt);

        // Delete addons from ordered_addons table
        $deleteAddonsQuery = "DELETE FROM ordered_addons WHERE order_id = ?";
        $deleteAddonsStmt = mysqli_prepare($conn, $deleteAddonsQuery);
        if (!$deleteAddonsStmt) {
            throw new Exception("Failed to prepare delete addons statement: " . mysqli_error($conn));
        }
        mysqli_stmt_bind_param($deleteAddonsStmt, 'i', $order_id);
        mysqli_stmt_execute($deleteAddonsStmt);
        mysqli_stmt_close($deleteAddonsStmt);

        // Delete associated records
        $deleteItemsQuery = "DELETE FROM order_items WHERE order_id = ?";
        $deleteItemsStmt = mysqli_prepare($conn, $deleteItemsQuery);
        if (!$deleteItemsStmt) {
            throw new Exception("Failed to prepare delete items statement: " . mysqli_error($conn));
        }
        mysqli_stmt_bind_param($deleteItemsStmt, 'i', $order_id);
        mysqli_stmt_execute($deleteItemsStmt);
        mysqli_stmt_close($deleteItemsStmt);

        $deleteOrderQuery = "DELETE FROM order_main WHERE order_id = ?";
        $deleteOrderStmt = mysqli_prepare($conn, $deleteOrderQuery);
        if (!$deleteOrderStmt) {
            throw new Exception("Failed to prepare delete order statement: " . mysqli_error($conn));
        }
        mysqli_stmt_bind_param($deleteOrderStmt, 'i', $order_id);
        mysqli_stmt_execute($deleteOrderStmt);

        if (mysqli_stmt_affected_rows($deleteOrderStmt) > 0) {
            $status = "Cancelled";

            // Insert into admincancelled_orders, including confirmation_status
            $insertCancelledOrderQuery = "INSERT INTO admincancelled_orders 
                                (order_id, customer_id, username, contact_no, description, 
                                 total_amount, total_orders, confirmation_status, 
                                 pickup_date, pickup_time, remarks, status) 
                              VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";
            $insertCancelledStmt = mysqli_prepare($conn, $insertCancelledOrderQuery);
            if (!$insertCancelledStmt) {
                throw new Exception("Failed to prepare insert statement: " . mysqli_error($conn));
            }
            mysqli_stmt_bind_param($insertCancelledStmt, 'iisssdiissss', $order_id, $customer_id, $username, $contact_no, $description, $total_amount, $total_orders, $confirmation_status, $pickup_date, $pickup_time, $remarks, $status);
            mysqli_stmt_execute($insertCancelledStmt);
            mysqli_stmt_close($insertCancelledStmt);

            // Committing the transaction
            mysqli_commit($conn);

            echo json_encode(["message" => "Order Cancelled successfully"]);
        } else {
            mysqli_rollback($conn);
            http_response_code(404);
            echo json_encode(["message" => "Order not found"]);
        }

        mysqli_stmt_close($deleteOrderStmt);
    } catch (Exception $e) {
        mysqli_rollback($conn);
        http_response_code(500);
        echo json_encode(["message" => $e->getMessage()]);
    } finally {
        mysqli_close($conn);
    }
} else {
    http_response_code(405);
    echo json_encode(["message" => "Method not allowed"]);
}
?>
