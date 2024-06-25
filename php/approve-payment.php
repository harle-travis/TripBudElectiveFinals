<?php
// Include the database connection file
include '../db/db.php';

// Check if paymentId is set and not empty
if(isset($_POST['paymentId']) && !empty($_POST['paymentId'])) {
    // Sanitize the input
    $paymentId = mysqli_real_escape_string($conn, $_POST['paymentId']);

    // Update the payment status to 'Approved' in the database
    $sql = "UPDATE payments SET payment_status = 'Approved' WHERE payment_id = $paymentId";

    if ($conn->query($sql) === TRUE) {
        // Payment approved successfully
        
        // Fetch user_id associated with the payment
        $userIdQuery = "SELECT user_id FROM payments WHERE payment_id = $paymentId";
        $userIdResult = $conn->query($userIdQuery);
        if ($userIdResult->num_rows > 0) {
            $row = $userIdResult->fetch_assoc();
            $userId = $row['user_id'];

            // Insert a new row into the notifications table
            $notificationMessage = 'Your payment has been approved. Thank you for your payment!';
            $insertNotificationQuery = "INSERT INTO notifications (user_id, message) VALUES ('$userId', '$notificationMessage')";
            if ($conn->query($insertNotificationQuery) === TRUE) {
                // Notification saved successfully
                $response = array(
                    'status' => 'success',
                    'message' => 'Payment approved successfully and notification sent'
                );
            } else {
                // Error saving notification
                $response = array(
                    'status' => 'error',
                    'message' => 'Payment approved successfully, but failed to save notification: ' . $conn->error
                );
            }
        } else {
            // User ID not found in the payments table
            $response = array(
                'status' => 'error',
                'message' => 'Error updating payment status: User ID not found'
            );
        }
    } else {
        // Error updating payment status
        $response = array(
            'status' => 'error',
            'message' => 'Error approving payment: ' . $conn->error
        );
    }

    // Close database connection
    $conn->close();

    // Return the response in JSON format
    echo json_encode($response);
} else {
    // Payment ID not provided
    $response = array(
        'status' => 'error',
        'message' => 'Payment ID not provided'
    );
    // Return the response in JSON format
    echo json_encode($response);
}
?>
