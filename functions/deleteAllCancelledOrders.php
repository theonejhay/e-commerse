<?php
require_once '../db_connection/db_con.php';

// SQL to delete all cancelled orders from the admincancelled_orders table
$deleteSQL = "DELETE FROM admincancelled_orders WHERE status = 'cancelled'";

// Execute the delete query
if ($conn->query($deleteSQL) === TRUE) {
    echo "All cancelled orders deleted successfully.";
} else {
    echo "Error deleting cancelled orders: " . $conn->error;
}

// Close connection
$conn->close();
?>
