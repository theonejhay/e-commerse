<?php
include '../db_connection/db_con.php'; // Include your database connection

header('Content-Type: application/json'); // Ensure JSON response

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Get the data from the request
    $data = json_decode(file_get_contents('php://input'), true);
    
    if (!isset($data['product_id']) || !isset($data['quantity'])) {
        echo json_encode(['success' => false, 'message' => 'Invalid input. Product ID and quantity required.']);
        exit;
    }

    $product_id = $data['product_id'];
    $quantity = $data['quantity'];

    // Prepare and execute the query
    $query = "UPDATE product_maintainance SET quantity = quantity + ? WHERE product_id = ?";
    $stmt = $conn->prepare($query);

    if (!$stmt) {
        echo json_encode(['success' => false, 'message' => 'Failed to prepare statement']);
        exit;
    }

    $stmt->bind_param('ii', $quantity, $product_id);

    if ($stmt->execute()) {
        if ($stmt->affected_rows > 0) {
            echo json_encode(['success' => true, 'message' => "Product quantity updated", 'new_quantity' => $stmt->affected_rows]);
        } else {
            echo json_encode(['success' => false, 'message' => 'No rows affected. Product ID may not exist.']);
        }
    } else {
        echo json_encode(['success' => false, 'message' => 'Failed to restore product quantity']);
    }

    // Close the statement and connection
    $stmt->close();
    $conn->close();
} else {
    echo json_encode(['success' => false, 'message' => 'Invalid request method']);
}
?>
