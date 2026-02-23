<?php
session_start();
require_once '../db_connection/db_con.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $contact_no = $_POST['contact_no'];

    // Prepare and execute SQL statement to check for existing contact number
    $stmt = $conn->prepare("SELECT COUNT(*) FROM customer WHERE contact_no = ?");
    $stmt->bind_param("s", $contact_no);
    $stmt->execute();
    $stmt->bind_result($count);
    $stmt->fetch();
    $stmt->close();
    $conn->close();

    // Return JSON response
    echo json_encode(['exists' => $count > 0]);
}
?>
