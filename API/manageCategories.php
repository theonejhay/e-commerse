<?php
include_once '../db_connection/db_con.php'; 

// Handle add, update, delete, and fetch requests
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = $_POST['action'];

    // Add new category
    if ($action === 'add') {
        $category_name = $_POST['category_name'];
        
        if (!$conn) {
            echo json_encode(["status" => "error", "message" => "Connection failed: " . mysqli_connect_error()]);
            exit();
        }

        // Insert category
        $query = "INSERT INTO categories (category_name) VALUES (?)";
        $stmt = mysqli_prepare($conn, $query);
        mysqli_stmt_bind_param($stmt, 's', $category_name);
        
        if (mysqli_stmt_execute($stmt)) {
            echo json_encode(["status" => "success", "message" => "Category added successfully"]);
        } else {
            echo json_encode(["status" => "error", "message" => "Failed to add category: " . mysqli_error($conn)]);
        }

        mysqli_stmt_close($stmt);
    }

    // Update category
    if ($action === 'update') {
        $category_id = $_POST['category_id'];
        $category_name = $_POST['category_name'];
        
        if (!$conn) {
            echo json_encode(["status" => "error", "message" => "Connection failed: " . mysqli_connect_error()]);
            exit();
        }

        // Update category
        $query = "UPDATE categories SET category_name = ? WHERE category_id = ?";
        $stmt = mysqli_prepare($conn, $query);
        mysqli_stmt_bind_param($stmt, 'si', $category_name, $category_id);

        if (mysqli_stmt_execute($stmt)) {
            echo json_encode(["status" => "success", "message" => "Category updated successfully"]);
        } else {
            echo json_encode(["status" => "error", "message" => "Failed to update category: " . mysqli_error($conn)]);
        }

        mysqli_stmt_close($stmt);
    }

    // Delete category
    if ($action === 'delete') {
        $category_id = $_POST['category_id'];
        
        if (!$conn) {
            echo json_encode(["status" => "error", "message" => "Connection failed: " . mysqli_connect_error()]);
            exit();
        }

        // Delete category
        $query = "DELETE FROM categories WHERE category_id = ?";
        $stmt = mysqli_prepare($conn, $query);
        mysqli_stmt_bind_param($stmt, 'i', $category_id);

        if (mysqli_stmt_execute($stmt)) {
            echo json_encode(["status" => "success", "message" => "Category deleted successfully"]);
        } else {
            echo json_encode(["status" => "error", "message" => "Failed to delete category: " . mysqli_error($conn)]);
        }

        mysqli_stmt_close($stmt);
    }

    mysqli_close($conn);
} elseif ($_SERVER['REQUEST_METHOD'] === 'GET') {
    if (!$conn) {
        echo json_encode(["status" => "error", "message" => "Connection failed: " . mysqli_connect_error()]);
        exit();
    }

    // Fetch categories
    $query = "SELECT * FROM categories";
    $result = mysqli_query($conn, $query);

    if ($result) {
        $categories = [];
        while ($row = mysqli_fetch_assoc($result)) {
            $categories[] = $row;
        }

        echo json_encode(["categories" => $categories]);
    } else {
        echo json_encode(["status" => "error", "message" => "Failed to fetch categories: " . mysqli_error($conn)]);
    }

    mysqli_close($conn);
} else {
    echo json_encode(["status" => "error", "message" => "Invalid request method"]);
}
?>
