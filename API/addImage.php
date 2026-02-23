<?php
include_once '../db_connection/db_con.php'; 

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $product_id = $_POST['product_id'];
    $image = $_FILES['image'];

    if (!$conn) {
        http_response_code(500);
        echo json_encode(["message" => "Connection failed: " . mysqli_connect_error()]);
        exit();
    }

    if (empty($product_id)) {
        http_response_code(400);
        echo json_encode(["message" => "Product ID is required"]);
        exit();
    }

    if ($image['error'] !== UPLOAD_ERR_OK) {
        http_response_code(400);
        echo json_encode(["message" => "Image upload error: " . $image['error']]);
        exit();
    }

    // File type validation
    $allowedTypes = ['image/jpeg', 'image/png', 'image/gif'];
    if (!in_array($image['type'], $allowedTypes)) {
        http_response_code(400);
        echo json_encode(["message" => "Invalid image type"]);
        exit();
    }

    // File size validation
    $maxFileSize = 2 * 1024 * 1024; // 2MB
    if ($image['size'] > $maxFileSize) {
        http_response_code(400);
        echo json_encode(["message" => "File size exceeds limit"]);
        exit();
    }

    // Read image file content
    $imageData = file_get_contents($image['tmp_name']);
    if ($imageData === false) {
        http_response_code(500);
        echo json_encode(["message" => "Failed to read image data"]);
        exit();
    }

    // Check if the product exists
    $checkQuery = "SELECT product_id FROM product_maintenance WHERE product_id = ?";
    $checkStmt = mysqli_prepare($conn, $checkQuery);
    mysqli_stmt_bind_param($checkStmt, 'i', $product_id);
    mysqli_stmt_execute($checkStmt);
    mysqli_stmt_store_result($checkStmt);

    if (mysqli_stmt_num_rows($checkStmt) > 0) {
        // Update the existing product's image
        $updateQuery = "UPDATE product_maintenance SET image = ? WHERE product_id = ?";
        $updateStmt = mysqli_prepare($conn, $updateQuery);
        mysqli_stmt_bind_param($updateStmt, 'bi', $null, $product_id);

        mysqli_stmt_send_long_data($updateStmt, 0, $imageData);
        if (mysqli_stmt_execute($updateStmt)) {
            http_response_code(200);
            echo json_encode(["message" => "Product image updated successfully"]);
        } else {
            http_response_code(500);
            echo json_encode(["message" => "Failed to update product image: " . mysqli_error($conn)]);
        }

        mysqli_stmt_close($updateStmt);
    } else {
        http_response_code(400);
        echo json_encode(["message" => "Product with the given ID does not exist"]);
    }

    mysqli_stmt_close($checkStmt);
    mysqli_close($conn);
} else {
    http_response_code(405);
    echo json_encode(["message" => "Method not allowed"]);
}
?>
