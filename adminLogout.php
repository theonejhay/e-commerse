<?php
session_start();

// Unset only the admin-specific session variables
unset($_SESSION['jwt']);
// Add other admin-related session variables here if necessary

// Optionally, you can check if no more session variables exist and then destroy the session
if (empty($_SESSION)) {
    session_destroy();
}

// Redirect to the appropriate login or home page
header("Location: index.php");
exit();
?>
