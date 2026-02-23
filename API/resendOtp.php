<?php
session_start();
require_once '../db_connection/db_con.php';

function sendSMS($contactNo, $message) {
    $apiKey = '8344481f0d234025d58df18a3365e7ed'; // Replace with your valid API key
    $senderName = 'Maigis';
    $url = 'https://api.semaphore.co/api/v4/messages';

    $data = [
        'apikey' => $apiKey,
        'number' => $contactNo,
        'message' => $message,
        'sendername' => $senderName,
    ];

    $ch = curl_init($url);
    curl_setopt($ch, CURLOPT_POST, 1);
    curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($data));
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

    $response = curl_exec($ch);
    curl_close($ch);

    return json_decode($response, true);  // Return decoded response
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    if (!isset($_SESSION['registration_data']['contact_no'])) {
        echo json_encode(['success' => false, 'message' => 'Contact number not found.']);
        exit;
    }

    $contact_no = $_SESSION['registration_data']['contact_no'];

    // Regenerate OTP and save it in the session
    $otp = rand(100000, 999999);
    $_SESSION['otp'] = $otp;
    $_SESSION['otp_expiry'] = time() + 180; // Extend expiry by 3 minutes

    // Send the new OTP
    $result = sendSMS($contact_no, "Your new OTP is $otp");

    // Check if the response is valid (not empty or error)
    if (!empty($result)) {
        echo json_encode(['success' => true, 'message' => 'New OTP sent successfully!']);
    } else {
        echo json_encode(['success' => false, 'message' => 'Failed to send OTP. Please try again.']);
    }
}
?>
