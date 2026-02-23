<?php
include_once '../db_connection/db_con.php'; 

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $input = json_decode(file_get_contents('php://input'), true);

    $product_id = $input['product_id'];
    $description = $input['description'];
    $price = $input['price'];
    $quantity = isset($input['quantity']) ? $input['quantity'] : null; // Quantity (optional)
    $category = $input['category'];
    $size = isset($input['size']) ? $input['size'] : null; // Size (optional)
    $status = isset($input['status']) ? $input['status'] : null; // Status (optional)
    $image = isset($input['image']) ? $input['image'] : null; // Image (optional)

    if (!$conn) {
        http_response_code(500);
        echo json_encode(["message" => "Connection failed: " . mysqli_connect_error()]);
        exit();
    }

    // Build the query dynamically based on provided fields
    $fields = [];
    $values = [];

    // Add mandatory fields
    $fields[] = "description = ?";
    $values[] = $description;

    $fields[] = "price = ?";
    $values[] = $price;

    $fields[] = "category = ?";
    $values[] = $category;

    // Add optional fields only if they are provided
    if ($quantity !== null) {
        $fields[] = "quantity = ?";
        $values[] = $quantity;
    }

    if ($status !== null) {
        $fields[] = "status = ?";
        $values[] = $status;
    }

    // Append the product_id condition
    $values[] = $product_id;

    // Construct the query
    $query = "UPDATE product_maintenance SET " . implode(", ", $fields) . " WHERE product_id = ?";
    $stmt = mysqli_prepare($conn, $query);

    // Dynamically bind parameters
    $param_types = str_repeat("s", count($values) - 1) . "i"; // All values are strings except product_id (integer)
    mysqli_stmt_bind_param($stmt, $param_types, ...$values);

    try {
        if (mysqli_stmt_execute($stmt)) {
            // Fetch updated price and quantity
            $fetch_query = "SELECT price, quantity FROM product_maintenance WHERE product_id = ?";
            $fetch_stmt = $conn->prepare($fetch_query);
            $fetch_stmt->bind_param("i", $product_id);
            $fetch_stmt->execute();
            $result = $fetch_stmt->get_result();
            $product = $result->fetch_assoc();

            if ($product) {
                $updated_price = $product['price'];
                $updated_quantity = $product['quantity'];
                $total = $updated_price * $updated_quantity;

                // Check if the product exists in product_total
                $check_query = "SELECT product_id FROM product_total WHERE product_id = ?";
                $check_stmt = $conn->prepare($check_query);
                $check_stmt->bind_param("i", $product_id);
                $check_stmt->execute();
                $check_result = $check_stmt->get_result();

                if ($check_result->num_rows > 0) {
                    // Update the product_total table
                    $update_total_query = "UPDATE product_total SET description = ?, category = ?, price = ?, quantity = ?, size = ?, status = ?, total = ? WHERE product_id = ?";
                    $update_total_stmt = $conn->prepare($update_total_query);
                    $update_total_stmt->bind_param("ssdisdii", $description, $category, $price, $quantity, $size, $status, $total, $product_id);
                    $update_total_stmt->execute();
                } else {
                    // Insert into the product_total table
                    $insert_total_query = "INSERT INTO product_total (product_id, description, category, price, quantity, size, image, initial_total_profit, status, total) VALUES (?, ?, ?, ?, ?, ?, ?, 0.00, ?, ?)";
                    $insert_total_stmt = $conn->prepare($insert_total_query);
                    $insert_total_stmt->bind_param("issdisssd", $product_id, $description, $category, $price, $quantity, $size, $image, $status, $total);
                    $insert_total_stmt->execute();
                }

                // Insert the total sum into the financial_report table
                $financial_report_query = "INSERT INTO financial_report (total_sum, report_date) SELECT SUM(total), CURRENT_DATE FROM product_total";
                $financial_stmt = $conn->prepare($financial_report_query);
                $financial_stmt->execute();
            }

            http_response_code(200);
            echo json_encode(["message" => "Product updated successfully"]);
        } else {
            throw new Exception("Failed to update product: " . mysqli_error($conn));
        }
    } catch (Exception $e) {
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
