<?php
include_once '../db_connection/db_con.php'; 

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $description = $_POST['description'];
    $price = $_POST['price'];
    $category = $_POST['category'];
    $image = $_FILES['image'] ?? null; // Optional image

    if (!$conn) {
        http_response_code(500);
        echo json_encode(["message" => "Connection failed: " . mysqli_connect_error()]);
        exit();
    }

    // Start transaction to ensure product details are saved
    mysqli_begin_transaction($conn);

    try {
        // Insert the product without image first
        $query = "INSERT INTO product_maintenance (description, price, category) VALUES (?, ?, ?)";
        $stmt = mysqli_prepare($conn, $query);
        mysqli_stmt_bind_param($stmt, 'sds', $description, $price, $category);

        if (!mysqli_stmt_execute($stmt)) {
            throw new Exception("Failed to add product: " . mysqli_error($conn));
        }

        // Get the last inserted product ID
        $product_id = mysqli_insert_id($conn);
        
        // Check if an image was uploaded
        if ($image && $image['error'] === UPLOAD_ERR_OK) {
            $allowedTypes = ['image/jpeg', 'image/png', 'image/gif'];
            if (!in_array($image['type'], $allowedTypes)) {
                throw new Exception("Invalid image type");
            }

            $maxFileSize = 2 * 1024 * 1024; // 2MB
            if ($image['size'] > $maxFileSize) {
                throw new Exception("File size exceeds limit");
            }

            $imageData = file_get_contents($image['tmp_name']);
            if ($imageData === false) {
                throw new Exception("Failed to read image data");
            }

            // Update the product with image data
            $updateQuery = "UPDATE product_maintenance SET image = ? WHERE product_id = ?";
            $updateStmt = mysqli_prepare($conn, $updateQuery);
            mysqli_stmt_bind_param($updateStmt, 'bi', $null, $product_id);

            mysqli_stmt_send_long_data($updateStmt, 0, $imageData);
            if (!mysqli_stmt_execute($updateStmt)) {
                throw new Exception("Failed to update product image: " . mysqli_error($conn));
            }

            mysqli_stmt_close($updateStmt);
        }

        mysqli_commit($conn);
        echo json_encode(["message" => "Product added successfully", "product_id" => $product_id]);
    } catch (Exception $e) {
        mysqli_rollback($conn);
        http_response_code(500);
        echo json_encode(["message" => $e->getMessage()]);
    } finally {
        mysqli_stmt_close($stmt);
        mysqli_close($conn);
    }
} else {
    http_response_code(405);
    echo json_encode(["message" => "Method not allowed"]);  
}
?>
