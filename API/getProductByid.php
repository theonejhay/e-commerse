<?php
include_once '../db_connection/db_con.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $input = json_decode(file_get_contents('php://input'), true);
    $ids = isset($input['ids']) ? $input['ids'] : '';

    if (!$conn) {
        http_response_code(500);
        echo json_encode(["message" => "Connection failed: " . mysqli_connect_error()]);
        exit();
    }

    // Create a string for the IN clause
    $idList = implode(',', array_map('intval', explode(',', $ids)));

    // Query to get the products by IDs
    $query = "SELECT * FROM product_maintenance WHERE product_id IN ($idList)";
    $result = mysqli_query($conn, $query);

    if ($result) {
        $products = [];
        while ($row = mysqli_fetch_assoc($result)) {
            if (!is_null($row['image'])) {
                $row['image'] = base64_encode($row['image']);
            }
            $products[] = $row;
        }

        http_response_code(200);
        echo json_encode(['products' => $products], JSON_PRETTY_PRINT);
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
