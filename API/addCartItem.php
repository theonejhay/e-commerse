<?php
include_once '../db_connection/db_con.php'; // Assumes MySQL connection file

error_reporting(E_ALL);
ini_set('display_errors', 1);

header('Content-Type: application/json');

// Get JSON input data
$input = json_decode(file_get_contents('php://input'), true);

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Get cart_item_id and customer_id from JSON input
    $cart_item_id = $input['cart_item_id'] ?? null;
    $customer_id = $input['customer_id'] ?? null;

    // Input validation
    if (empty($cart_item_id) || empty($customer_id)) {
        http_response_code(400);
        echo json_encode(["message" => "Invalid input"]);
        exit();
    }

    if (!$conn) {
        http_response_code(500);
        echo json_encode(["message" => "Connection failed: " . mysqli_connect_error()]);
        exit();
    }

    // Retrieve the current quantity of the item
    $query = 'SELECT quantity FROM saved_cart WHERE id = ? AND customer_id = ?';
    $stmt = mysqli_prepare($conn, $query);

    if (!$stmt) {
        http_response_code(500);
        echo json_encode(["message" => "Failed to prepare statement: " . mysqli_error($conn)]);
        exit();
    }

    mysqli_stmt_bind_param($stmt, 'ii', $cart_item_id, $customer_id);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);
    $item = mysqli_fetch_assoc($result);
    mysqli_stmt_close($stmt);

    if ($item) {
        // Update query to increase quantity by 1
        $updateQuery = 'UPDATE saved_cart SET quantity = quantity + 1 WHERE id = ? AND customer_id = ?';
        $stmt = mysqli_prepare($conn, $updateQuery);
        mysqli_stmt_bind_param($stmt, 'ii', $cart_item_id, $customer_id);

        if (mysqli_stmt_execute($stmt)) {
            http_response_code(200);
            echo json_encode(["message" => "Item quantity increased by 1"]);
        } else {
            http_response_code(500);
            echo json_encode(["message" => "Failed to update item quantity: " . mysqli_stmt_error($stmt)]);
        }
        mysqli_stmt_close($stmt);
    } else {
        http_response_code(404);
        echo json_encode(["message" => "Item not found"]);
    }

    mysqli_close($conn);
} else {
    http_response_code(405);
    echo json_encode(["message" => "Method not allowed"]);
}
