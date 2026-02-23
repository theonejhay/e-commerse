<?php
require __DIR__ . '/vendor/autoload.php';
use \Firebase\JWT\JWT;

// Define the key for signing the JWT
$secretKey = 'online-ordering-123';

// Define the payload
$payload = [
    'iss' => 'online_ordering', // Issuer
    'iat' => time(),               // Issued At
    'nbf' => time(),               // Not Before
    'exp' => time() + (60*60*24*365),    // Expiration Time
];

// Encode the payload to create the JWT
$jwt = JWT::encode($payload, $secretKey, 'HS256');

// Output the JWT
echo json_encode([
    'token' => $jwt
]);
