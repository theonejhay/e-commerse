<?php
include_once '../db_connection/db_con.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $input = json_decode(file_get_contents('php://input'), true);
    $description = $input['description'] ?? '';
    $category = $input['category'] ?? '';

    if (!$conn) {
        http_response_code(500);
        echo json_encode(["message" => "Connection failed: " . mysqli_connect_error()]);
        exit();
    }

    // Base query with JOIN to fetch category_name and quantity
    $query = "
        SELECT pm.product_id, pm.description, pm.price, pm.category, pm.quantity, c.category_name, pm.image, pm.status
        FROM product_maintenance pm
        LEFT JOIN categories c ON pm.category = c.category_id
        WHERE 1=1
    ";

    // Add description filter if provided
    if ($description !== '') {
        $query .= " AND pm.description LIKE ?";
        $description_param = '%' . $description . '%';
    }

    // Add category filter if provided, with support for both category ID and category name
    if ($category !== '') {
        $query .= " AND (pm.category = ? OR c.category_name = ?)";
    }

    $stmt = mysqli_prepare($conn, $query);

    // Bind parameters based on availability
    if ($description !== '' && $category !== '') {
        mysqli_stmt_bind_param($stmt, 'sss', $description_param, $category, $category); // Three params
    } elseif ($description !== '') {
        mysqli_stmt_bind_param($stmt, 's', $description_param); // Only description
    } elseif ($category !== '') {
        mysqli_stmt_bind_param($stmt, 'ss', $category, $category); // Both category ID and name
    }

    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);

    if ($result) {
        $products = [];
        while ($row = mysqli_fetch_assoc($result)) {
            // Convert image to base64 if available
            if (!is_null($row['image'])) {
                $row['image'] = base64_encode($row['image']);
            }

            // Fetch average rating for each product
            $ratingQuery = "
                SELECT COALESCE(AVG(rating_value), 0) AS average_rating
                FROM ratings
                WHERE rating_description LIKE CONCAT(?, ' (%)')
            ";

            $ratingStmt = mysqli_prepare($conn, $ratingQuery);
            mysqli_stmt_bind_param($ratingStmt, 's', $row['description']);
            mysqli_stmt_execute($ratingStmt);
            $ratingResult = mysqli_stmt_get_result($ratingStmt);
            $averageRating = mysqli_fetch_assoc($ratingResult)['average_rating'];
            mysqli_stmt_close($ratingStmt);

            // Calculate rating percentage (out of 100)
            $ratingPercentage = ($averageRating / 5) * 100; // Assuming the scale is out of 5 stars

            // Add both average rating and rating percentage to the product
            $row['average_rating'] = number_format($averageRating, 2); // Format the average rating to 2 decimal places
            $row['rating_percentage'] = round($ratingPercentage); // Round percentage to nearest whole number

            $products[] = $row;

            // Check if quantity is zero and update the product_total table
            if ($row['quantity'] == 0) {
                $updateTotalQuery = "UPDATE product_total SET total = 0 WHERE product_id = ?";
                $updateStmt = $conn->prepare($updateTotalQuery);
                $updateStmt->bind_param("i", $row['product_id']);
                $updateStmt->execute();
                $updateStmt->close();
            }
        }

        $response = [
            'products' => $products
        ];

        http_response_code(200);
        echo json_encode($response, JSON_PRETTY_PRINT);
    } else {
        http_response_code(500);
        echo json_encode(["message" => "Failed to fetch products"]);
    }

    mysqli_close($conn);
} else {
    http_response_code(405);
    echo json_encode(["message" => "Method not allowed"]);
}
?>
