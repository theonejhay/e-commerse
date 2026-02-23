<?php
session_start(); // Start the session to store user data temporarily
require_once '../db_connection/db_con.php';

function sendSMS($contactNo, $message) {
    // Semaphore API credentials (replace with your valid API key and sender name)
    $apiKey = '8344481f0d234025d58df18a3365e7ed';
    $senderName = 'Maigis'; // Your app or business name

    // API URL for sending the SMS
    $url = 'https://api.semaphore.co/api/v4/messages';

    // Prepare the request data for Semaphore
    $data = [
        'apikey' => $apiKey,
        'number' => $contactNo,
        'message' => $message,
        'sendername' => $senderName,
    ];

    // Initialize cURL and send the request to Semaphore
    $ch = curl_init($url);
    curl_setopt($ch, CURLOPT_POST, 1);
    curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($data));
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

    $response = curl_exec($ch);
    curl_close($ch);

    // Decode the response from Semaphore
    return json_decode($response, true);
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Retrieve form data
    $firstname = $_POST['firstname'];
    $lastname = $_POST['lastname'];
    $contact_no = $_POST['contact_no'];
    $username = $_POST['username'];
    $password = password_hash($_POST['password'], PASSWORD_DEFAULT); // Encrypt the password

    // Temporarily store user data in the session (instead of saving in the DB immediately)
    $_SESSION['registration_data'] = [
        'firstname' => $firstname,
        'lastname' => $lastname,
        'contact_no' => $contact_no,
        'username' => $username,
        'password' => $password
    ];

    // Generate OTP (6 digits) and store it in the session
    $otp = rand(100000, 999999);
    $_SESSION['otp'] = $otp;

    // Save OTP expiration time (e.g., 5 minutes from now)
    $_SESSION['otp_expiry'] = time() + 180;

    // Send OTP to the user's phone
    sendSMS($contact_no, "Your OTP is $otp");

    // Send success response back to the client (for showing the OTP input modal)
    echo json_encode(['success' => true, 'message' => 'OTP sent successfully!']);
}