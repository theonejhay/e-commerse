<?php
include_once '../db_connection/db_con.php'; 

error_reporting(E_ALL);
ini_set('display_errors', 1);

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $input = json_decode(file_get_contents('php://input'), true);

    $order_id = $input['order_id'];
    $confirmation_status = isset($input['confirmation_status']) ? $input['confirmation_status'] : null;
    $confirm_request = isset($input['confirm_request']) ? $input['confirm_request'] : null;

    if (empty($order_id) || (!isset($confirmation_status) && !isset($confirm_request))) {
        http_response_code(400);
        echo json_encode(["message" => "Invalid input"]);
        exit();
    }

    if (!$conn) {
        http_response_code(500);
        echo json_encode(["message" => "Connection failed: " . mysqli_connect_error()]);
        exit();
    }

    $updates = [];
    $params = [];
    $paramTypes = '';

    if ($confirmation_status !== null) {
        $updates[] = 'confirmation_status = ?';
        $params[] = $confirmation_status;
        $paramTypes .= 's';
    }
    if ($confirm_request !== null) {
        $updates[] = 'confirm_request = ?';
        $params[] = $confirm_request;
        $paramTypes .= 's';
    }

    $params[] = $order_id;
    $paramTypes .= 'i';

    $query = 'UPDATE order_main SET ' . implode(', ', $updates) . ' WHERE order_id = ?';

    $stmt = mysqli_prepare($conn, $query);
    mysqli_stmt_bind_param($stmt, $paramTypes, ...$params);

    if (mysqli_stmt_execute($stmt)) {
        // Fetch order details
        $fetchOrderQuery = "SELECT c.contact_no, o.customer_id, o.pickup_date, o.pickup_time FROM customer c 
                            JOIN order_main o ON c.customer_id = o.customer_id 
                            WHERE o.order_id = ?";
        $orderStmt = mysqli_prepare($conn, $fetchOrderQuery);
        mysqli_stmt_bind_param($orderStmt, 'i', $order_id);
        mysqli_stmt_execute($orderStmt);
        mysqli_stmt_bind_result($orderStmt, $contact_no, $customer_id, $pickup_date, $pickup_time);
        mysqli_stmt_fetch($orderStmt);
        mysqli_stmt_close($orderStmt);

        if ($contact_no) {
            $pickup_time_12hr = date("g:i A", strtotime($pickup_time));
            $message = "";

            if ($confirmation_status === "confirmed") {
                $message = "Your order has been confirmed. Customer ID: $customer_id. Please pick up your order on $pickup_date at $pickup_time_12hr.";
            } elseif ($confirmation_status === "ready-to-claim") {
                $message = "Reminder: Your order is ready to be claimed. Please pick it up on $pickup_date at $pickup_time_12hr. Thank you!";
            } elseif ($confirmation_status === "cooking") {
                $message = "Your order is currently being prepared. We will notify you once it's ready for pickup.";
            }

            // Send SMS
            $apiKey = '8344481f0d234025d58df18a3365e7ed';
            $senderName = 'Maigis';
            $url = 'https://api.semaphore.co/api/v4/messages';

            $data = [
                'apikey' => $apiKey,
                'number' => $contact_no,
                'message' => $message,
                'sendername' => $senderName,
            ];

            $ch = curl_init($url);
            curl_setopt($ch, CURLOPT_POST, 1);
            curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($data));
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

            $sms_result = curl_exec($ch);
            curl_close($ch);

            if ($sms_result) {
                echo json_encode(["message" => "Order updated and SMS sent successfully"]);
            } else {
                echo json_encode(["message" => "Order updated, but SMS failed"]);
            }
        }
    } else {
        http_response_code(500);
        echo json_encode(["message" => "Failed to update order: " . mysqli_error($conn)]);
    }

    mysqli_stmt_close($stmt);
    mysqli_close($conn);
}
?>
