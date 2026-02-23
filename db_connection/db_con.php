<?php
$host = 'localhost';
$dbname = 'online_ordering';
$username = 'root';
$password = '';

$conn = new mysqli($host, $username, $password, $dbname);

if ($conn->connect_error) {
    die("Failed Connection: " . $conn->connect_error);
} else {
    //echo "Successfully Connected";
}
?>
