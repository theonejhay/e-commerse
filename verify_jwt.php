<?php
require __DIR__ . '/vendor/autoload.php';
use \Firebase\JWT\JWT;
use \Firebase\JWT\Key;

$secretKey = 'online-ordering-123'; // Use the same secret key

session_start();

if (!isset($_SESSION['jwt'])) {
    header("Location: homepage.php");
    exit();
}

$jwt = $_SESSION['jwt'];

try {
    $decoded = JWT::decode($jwt, new Key($secretKey, 'HS256'));
    // JWT is valid, proceed with the request
} catch (Exception $e) {
    // JWT is invalid
    header("Location: homepage.php");
    exit();
}
?>
