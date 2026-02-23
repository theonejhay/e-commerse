<?php
include_once '../db_connection/db_con.php'; // Assumes MySQL connection file

error_reporting(E_ALL);
ini_set('display_errors', 1);

$input = json_decode(file_get_contents('php://input'), true);

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Get the customer_id from the input JSON
    $customer_id = $input['customer_id'] ?? null;

    // Input validation
    if (empty($customer_id)) {
        http_response_code(400);
        echo json_encode(["message" => "Invalid input"]);
        exit();
    }

    if (!$conn) {
        http_response_code(500);
        echo json_encode(["message" => "Connection failed: " . mysqli_connect_error()]);
        exit();
    }

    // Build the SQL delete query
    $query = 'DELETE FROM customer WHERE customer_id = ?';
    $stmt = mysqli_prepare($conn, $query);

    if (!$stmt) {
        http_response_code(500);
        echo json_encode(["message" => "Failed to prepare statement: " . mysqli_error($conn)]);
        exit();
    }

    mysqli_stmt_bind_param($stmt, 'i', $customer_id);

    if (mysqli_stmt_execute($stmt)) {
        if (mysqli_stmt_affected_rows($stmt) > 0) {
            http_response_code(200);
            echo json_encode(["message" => "Customer deleted successfully"]);
        } else {
            http_response_code(404);
            echo json_encode(["message" => "Customer not found"]);
        }
    } else {
        http_response_code(500);
        echo json_encode(["message" => "Failed to delete customer: " . mysqli_stmt_error($stmt)]);
    }

    mysqli_stmt_close($stmt);
    mysqli_close($conn);
} else {
    http_response_code(405);
    echo json_encode(["message" => "Method not allowed"]);
}
?>
