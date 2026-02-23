<?php
// Enable error reporting for debugging (remove this in production)
ini_set('display_errors', 1);
error_reporting(E_ALL);

include_once '../db_connection/db_con.php';

header('Content-Type: application/json'); // Ensure you're returning JSON

// Get the request body and decode it as JSON
$input = json_decode(file_get_contents('php://input'), true);

if (isset($input['customer_id']) && isset($input['products'])) {
    $customerId = $input['customer_id'];
    $products = $input['products'];

    // Begin transaction to ensure atomicity
    $conn->begin_transaction();

    try {
        // Iterate over products and restore their quantities in the database
        foreach ($products as $product) {
            if (!isset($product['product_id']) || !isset($product['quantity'])) {
                throw new Exception('Product data missing.');
            }

            $productId = $product['product_id'];
            $quantityToRestore = $product['quantity'];

            // Query to get the current stock of the product
            $query = "SELECT quantity FROM product_maintainance WHERE product_id = ?";
            $stmt = $conn->prepare($query);
            $stmt->bind_param("i", $productId);
            $stmt->execute();
            $result = $stmt->get_result();

            if ($row = $result->fetch_assoc()) {
                $currentQuantity = $row['quantity'];

                // Restore the product's quantity in the database
                $newQuantity = $currentQuantity + $quantityToRestore;
                $updateQuery = "UPDATE product_maintainance SET quantity = ? WHERE product_id = ?";
                $updateStmt = $conn->prepare($updateQuery);
                $updateStmt->bind_param("ii", $newQuantity, $productId);
                $updateStmt->execute();

                // Check if the update succeeded
                if ($updateStmt->affected_rows === 0) {
                    throw new Exception('Failed to update product quantity for product ID ' . $productId);
                }
            } else {
                throw new Exception('Product not found with ID ' . $productId);
            }
        }

        // Commit the transaction if all updates succeed
        $conn->commit();
        echo json_encode(['success' => true, 'message' => 'Order canceled successfully.']);

    } catch (Exception $e) {
        // Rollback in case of error
        $conn->rollback();
        echo json_encode(['success' => false, 'message' => 'Cancellation failed: ' . $e->getMessage()]);
    }

} else {
    echo json_encode(['success' => false, 'message' => 'Invalid request.']);
}

$conn->close();
?>
