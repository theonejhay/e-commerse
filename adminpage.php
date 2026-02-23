<?php 
require_once 'functions/admin_config.php'; 

// Check if there's an error message from the admin_config.php script
if (isset($error_message)) {
    echo "<div style='color:red; text-align:center; font-size:18px;'>";
    echo "<p>$error_message</p>";
    echo "<p><a href='admin_login.php'>Go back to the login page</a></p>";
    echo "</div>";
}
?>
