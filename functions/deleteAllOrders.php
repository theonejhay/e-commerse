<?php
require_once '../db_connection/db_con.php';

// SQL to delete all records from the table
$deleteSQL = "DELETE FROM order_main";

// SQL to reset the auto increment value
$resetAutoIncrementSQL = "ALTER TABLE order_main AUTO_INCREMENT = 1";

// Execute the delete query
if ($conn->query($deleteSQL) === TRUE) {
    echo "All records deleted successfully.";
} else {
    echo "Error deleting records: " . $conn->error;
}

// Execute the reset auto increment query
if ($conn->query($resetAutoIncrementSQL) === TRUE) {
    
} else {
    echo "Error resetting auto increment value: " . $conn->error;
}

// Close connection
$conn->close();
?>
