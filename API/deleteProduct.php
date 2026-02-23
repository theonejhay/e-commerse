<?php
include_once '../db_connection/db_con.php';

if ($_SERVER['REQUEST_METHOD'] === 'DELETE') {
    // Try to get the product_id from the query string first
    $product_id = isset($_GET['product_id']) ? intval($_GET['product_id']) : null;

    // If product_id is not found in the query string, try to get it from the body
    if (empty($product_id)) {
        parse_str(file_get_contents("php://input"), $_DELETE);
        $product_id = isset($_DELETE['product_id']) ? intval($_DELETE['product_id']) : null;
    }

    // Input validation
    if (empty($product_id)) {
        http_response_code(400);
        echo json_encode(["message" => "Invalid input: product_id is required"]);
        exit();
    }

    if (!$conn) {
        http_response_code(500);
        echo json_encode(["message" => "Connection failed: " . mysqli_connect_error()]);
        exit();
    }

    // Build the SQL delete query
    $query = "DELETE FROM product_maintenance WHERE product_id = ?";
    $stmt = mysqli_prepare($conn, $query);
    mysqli_stmt_bind_param($stmt, 'i', $product_id);

    if (mysqli_stmt_execute($stmt)) {
        // Check if any rows were affected
        if (mysqli_stmt_affected_rows($stmt) > 0) {
            http_response_code(200);
            echo json_encode(["message" => "Product deleted successfully"]);
        } else {
            http_response_code(404);
            echo json_encode(["message" => "Product not found"]);
        }
    } else {
        http_response_code(500);
        echo json_encode(["message" => "Failed to delete product"]);
    }

    mysqli_stmt_close($stmt);
    mysqli_close($conn);
} else {
    http_response_code(405);
    echo json_encode(["message" => "Method not allowed"]);
}
?>
