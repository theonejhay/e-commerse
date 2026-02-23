<?php
session_start();

// Unset only the customer-specific session variables
unset($_SESSION['customer_jwt']);
unset($_SESSION['customer_id']);

// Optionally, you can add a check to destroy the session if no other session variables exist
if (empty($_SESSION)) {
    session_destroy();
}

// Redirect to the appropriate login or home page
header("Location: index.php");
exit();
?>
