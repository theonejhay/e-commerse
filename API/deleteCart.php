<?php
include '../db_connection/db_con.php';

if ($_SERVER['REQUEST_METHOD'] == 'DELETE') {
    parse_str(file_get_contents("php://input"), $_DELETE);

    // Check if 'id' is set
    if (isset($_DELETE['id'])) {
        $order_id = $_DELETE['id'];  // This should correspond to the id of the order in saved_cart

        // Start a transaction
        mysqli_begin_transaction($conn);
        try {
            // Delete from saved_cart table
            $deleteCartQuery = "DELETE FROM saved_cart WHERE id = ?";
            $stmt = $conn->prepare($deleteCartQuery);
            $stmt->bind_param("i", $order_id);
            $stmt->execute();

            // Check if the deletion was successful
            if ($stmt->affected_rows > 0) {
                // Commit transaction
                mysqli_commit($conn);
                echo json_encode(['success' => true, 'message' => 'Cart order deleted successfully.']);
            } else {
                // If no rows were affected, that means the ID was not found
                echo json_encode(['success' => false, 'message' => 'No cart order found with the provided ID.']);
            }
        } catch (Exception $e) {
            mysqli_rollback($conn);
            echo json_encode(['success' => false, 'message' => 'Failed to delete cart order: ' . $e->getMessage()]);
        }
    } else {
        echo json_encode(['success' => false, 'message' => 'Order ID is not defined.']);
    }
} else {
    echo json_encode(['success' => false, 'message' => 'Invalid request method.']);
}
?>
