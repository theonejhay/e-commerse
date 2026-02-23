<?php
session_start(); // Start session to access OTP
require_once '../db_connection/db_con.php'; // Include database connection

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $postData = json_decode(file_get_contents('php://input'), true);
    $enteredOtp = $postData['otp_code'];

    // Check if OTP is set in session
    if (isset($_SESSION['otp']) && isset($_SESSION['otp_expiry'])) {
        $sessionOtp = $_SESSION['otp'];
        $otpExpiry = $_SESSION['otp_expiry'];

        // Check if the OTP has expired
        if (time() > $otpExpiry) {
            unset($_SESSION['otp']);
            unset($_SESSION['otp_expiry']);
            echo json_encode(['success' => false, 'message' => 'OTP has expired']);
        } else {
            // Compare the entered OTP with the session-stored OTP
            if ($enteredOtp == $sessionOtp) {
                unset($_SESSION['otp']);
                unset($_SESSION['otp_expiry']);

                // Retrieve user data from session
                if (isset($_SESSION['registration_data'])) {
                    $firstname = $_SESSION['registration_data']['firstname'];
                    $lastname = $_SESSION['registration_data']['lastname'];
                    $contact_no = $_SESSION['registration_data']['contact_no'];
                    $username = $_SESSION['registration_data']['username'];
                    $password = $_SESSION['registration_data']['password'];

                    // Prepare SQL statement to insert user into the customer table
                    $stmt = $conn->prepare("INSERT INTO customer (firstname, lastname, contact_no, username, password) VALUES (?, ?, ?, ?, ?)");
                    $stmt->bind_param("sssss", $firstname, $lastname, $contact_no, $username, $password);
                    $stmt->execute();

                    if ($stmt->affected_rows > 0) {
                        // Save customer ID to the session
                        $customer_id = $conn->insert_id;
                        $_SESSION['customer_id'] = $customer_id;

                        unset($_SESSION['registration_data']);
                        echo json_encode(['success' => true, 'message' => 'Registration successful!']);
                    } else {
                        echo json_encode(['success' => false, 'message' => 'Registration failed: ' . $stmt->error]);
                    }
                    $stmt->close();
                } else {
                    echo json_encode(['success' => false, 'message' => 'No registration data found.']);
                }
            } else {
                echo json_encode(['success' => false, 'message' => 'Invalid OTP']);
            }
        }
    } else {
        echo json_encode(['success' => false, 'message' => 'No OTP found or session expired']);
    }
} else {
    echo json_encode(['success' => false, 'message' => 'Invalid request']);
}
