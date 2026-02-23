<?php
session_start(); // Start session to store OTP temporarily

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Get the incoming request data
    $postData = json_decode(file_get_contents('php://input'), true);
    $contactNo = $postData['contact_no'];

    // Generate a 6-digit OTP
    $otp = rand(100000, 999999);

    // Store the OTP and its expiry in the session (e.g., 5 minutes expiry)
    $_SESSION['otp'] = $otp;
    $_SESSION['otp_expiry'] = time() + 300; // OTP will expire in 5 minutes (300 seconds)

    // Semaphore API credentials (replace with your valid API key and sender name)
    $apiKey = '8344481f0d234025d58df18a3365e7ed'; 
    $message = 'Your OTP code is ' . $otp;
    $senderName = 'Maigis'; // Your app or business name

    // API URL for sending the OTP
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
    $result = json_decode($response, true);

    // Handle the response from Semaphore
    if (isset($result['status']) && $result['status'] == 'Queued') {
        // OTP sent successfully
        echo json_encode(['success' => true, 'message' => 'OTP sent successfully.']);
    } else {
        // Failed to send OTP
        $errorMessage = isset($result['error']) ? $result['error'] : 'Unknown error occurred.';
        echo json_encode(['success' => false, 'message' => $errorMessage]);
    }
}
?>
