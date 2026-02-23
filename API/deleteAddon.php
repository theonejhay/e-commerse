<?php
include '../db_connection/db_con.php';

if ($_SERVER['REQUEST_METHOD'] == 'DELETE') {
    parse_str(file_get_contents("php://input"), $_DELETE);

    // Check if 'id' is set
    if (isset($_DELETE['id'])) {
        $addon_id = $_DELETE['id'];  // This corresponds to the id of the addon in cart_addons

        // Start a transaction
        mysqli_begin_transaction($conn);
        try {
            // Delete from cart_addons table
            $deleteAddonQuery = "DELETE FROM cart_addons WHERE id = ?";
            $stmt = $conn->prepare($deleteAddonQuery);
            $stmt->bind_param("i", $addon_id);
            $stmt->execute();

            // Check if the deletion was successful
            if ($stmt->affected_rows > 0) {
                // Commit transaction
                mysqli_commit($conn);
                echo json_encode(['success' => true, 'message' => 'Addon deleted successfully.']);
            } else {
                // If no rows were affected, that means the ID was not found
                echo json_encode(['success' => false, 'message' => 'No addon found with the provided ID.']);
            }
        } catch (Exception $e) {
            mysqli_rollback($conn);
            echo json_encode(['success' => false, 'message' => 'Failed to delete addon: ' . $e->getMessage()]);
        }
    } else {
        echo json_encode(['success' => false, 'message' => 'Addon ID is not defined.']);
    }
} else {
    echo json_encode(['success' => false, 'message' => 'Invalid request method.']);
}
?>
