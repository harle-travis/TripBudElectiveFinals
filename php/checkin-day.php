<?php
include('../db/db.php');

// Check if bookingId is set and not empty
if(isset($_POST['bookingId']) && !empty($_POST['bookingId'])) {
    $bookingId = $_POST['bookingId'];

    // Update the status to 'Check-in' in the database
    $sql = "UPDATE bookings SET status = 'Ongoing' WHERE booking_id = $bookingId";
    if ($conn->query($sql) === TRUE) {
        // Return success message
        $response['success'] = true;
        $response['message'] = 'Check-in successful. Enjoy your stay!';
    } else {
        // Return error message
        $response['success'] = false;
        $response['message'] = 'Error updating booking status: ' . $conn->error;
    }
} else {
    // Return error message if bookingId is not provided
    $response['success'] = false;
    $response['message'] = 'Invalid request!';
}

// Close database connection
$conn->close();

// Output JSON response
echo json_encode($response);
?>
