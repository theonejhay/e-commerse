<?php
session_start();

// Include database connection file and JWT library
require_once __DIR__ . '/../db_connection/db_con.php'; // Corrected path for the database connection
require_once __DIR__ . '/../vendor/autoload.php'; // Corrected path for the JWT autoload file

use \Firebase\JWT\JWT;

$secretKey = 'online-ordering-123'; // Use the same secret key

// Check if the form is submitted
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Retrieve form input
    $admin_username = $_POST['username'];
    $admin_password = $_POST['password'];
    $remember_me = isset($_POST['remember_me']);

    // Prepare a query to check the username in admin_access table
    $query = "SELECT * FROM admin_access WHERE username = ?";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("s", $admin_username);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result && $result->num_rows == 1) {
        // Fetch the user record
        $user = $result->fetch_assoc();

        // Check the role and handle password verification accordingly
        if ($user['role'] === 'admin') {
            // Admin password is stored in plain text (e.g., admin123)
            if ($admin_password === $user['password']) {
                // Generate JWT token for admin
                $issuedAt = time();
                $expirationTime = $issuedAt + 3600; // JWT valid for 1 hour
                $payload = array(
                    'iat' => $issuedAt,
                    'exp' => $expirationTime,
                    'admin_username' => $admin_username,
                    'role' => 'admin'
                );

                $jwt = JWT::encode($payload, $secretKey, 'HS256');

                // Set session variables for admin
                $_SESSION['jwt'] = $jwt;

                // Set cookie
                setcookie('jwt', $jwt, $expirationTime, "/", "", true, true);

                // Redirect to the admin dashboard
                header("Location: admin.php");
                exit();
            } else {
                $error_message = "Invalid username or password.";
            }
        } elseif ($user['role'] === 'staff') {
            // For staff, use password_verify
            if (password_verify($admin_password, $user['password'])) {
                // Generate JWT token for staff
                $issuedAt = time();
                $expirationTime = $issuedAt + 3600; // JWT valid for 1 hour
                $payload = array(
                    'iat' => $issuedAt,
                    'exp' => $expirationTime,
                    'staff_username' => $admin_username,
                    'role' => 'staff'
                );

                $jwt = JWT::encode($payload, $secretKey, 'HS256');

                // Set session variables for staff
                $_SESSION['staff_jwt'] = $jwt;

                // Set cookie
                setcookie('staff_jwt', $jwt, $expirationTime, "/", "", true, true);

                // Redirect to the staff dashboard
                header("Location: staffDashBoard.php");
                exit();
            } else {
                $error_message = "Invalid username or password.";
            }
        }
    } else {
        // Prepare a query to check the username in customer table
        $query = "SELECT * FROM customer WHERE username = ?";
        $stmt = $conn->prepare($query);
        $stmt->bind_param("s", $admin_username);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result && $result->num_rows == 1) {
            // Fetch the customer record
            $customer = $result->fetch_assoc();

            // Verify the password
            if (password_verify($admin_password, $customer['password'])) {
                // JWT expiration based on 'Remember Me'
                $issuedAt = time();
                $expirationTime = $remember_me ? ($issuedAt + (86400 * 30)) : ($issuedAt + 3600); // 30 days or 1 hour

                // Payload for JWT token
                $payload = array(
                    'iat' => $issuedAt,
                    'exp' => $expirationTime,
                    'username' => $admin_username,
                    'customer_id' => $customer['customer_id'] // Include customer_id in payload
                );

                // Generate the JWT token
                $jwt = JWT::encode($payload, $secretKey, 'HS256');

                // Set session variables for customer
                $_SESSION['customer_jwt'] = $jwt;
                $_SESSION['customer_id'] = $customer['customer_id']; // Store customer_id in session

                // Set the cookie to store the JWT token
                setcookie('customer_jwt', $jwt, $expirationTime, "/", "", true, true); // Cookie will expire with JWT

                // Redirect to the homepage
                header("Location: index.php");
                exit();
            } else {
                // Invalid password for customer
                $error_message = "Invalid username or password.";
            }
        } else {
            // Invalid username
            $error_message = "Invalid username or password.";
        }
    }

    // Close the database connection
    $stmt->close();
    $conn->close();
}
?>
