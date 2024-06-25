<?php
include('../db/db.php');

// Check if bookingId is set and not empty
if(isset($_POST['bookingId']) && !empty($_POST['bookingId'])) {
    $bookingId = $_POST['bookingId'];

    // Update the status to 'Rejected' in the database
    $sql = "UPDATE bookings SET status = 'Rejected' WHERE booking_id = $bookingId";
    if ($conn->query($sql) === TRUE) {
        // Fetch user_id associated with the booking
        $userIdQuery = "SELECT user_id FROM bookings WHERE booking_id = $bookingId";
        $userIdResult = $conn->query($userIdQuery);
        if ($userIdResult->num_rows > 0) {
            $row = $userIdResult->fetch_assoc();
            $userId = $row['user_id'];

            // Check if the user exists in the users table
            $checkUserQuery = "SELECT * FROM users WHERE user_id = $userId";
            $userExists = $conn->query($checkUserQuery);
            if ($userExists->num_rows > 0) {
                // Insert a new row into the notifications table
                $notificationMessage = 'Your booking has been rejected. Please contact support for further assistance.';
                $insertNotificationQuery = "INSERT INTO notifications (user_id, message) VALUES ('$userId', '$notificationMessage')";
                if ($conn->query($insertNotificationQuery) === TRUE) {
                    // Notification saved successfully
                    $response['success'] = true;
                    $response['message'] = 'Booking rejected successfully and notification sent!';
                    echo json_encode($response);
                } else {
                    // Error saving notification
                    $response['success'] = false;
                    $response['message'] = 'Booking rejected successfully, but failed to save notification: ' . $conn->error;
                    echo json_encode($response);
                }
            } else {
                // User ID does not exist in the users table
                $response['success'] = false;
                $response['message'] = 'Error updating booking status: User ID does not exist';
                echo json_encode($response);
            }
        } else {
            // User ID not found in the bookings table
            $response['success'] = false;
            $response['message'] = 'Error updating booking status: User ID not found';
            echo json_encode($response);
        }
    } else {
        // Error updating booking status
        $response['success'] = false;
        $response['message'] = 'Error updating booking status: ' . $conn->error;
        echo json_encode($response);
    }
} else {
    // Return error message if bookingId is not provided
    $response['success'] = false;
    $response['message'] = 'Invalid request!';
    echo json_encode($response);
}

// Close database connection
$conn->close();
?>
