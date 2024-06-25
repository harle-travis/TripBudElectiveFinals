<?php
// Include the database connection file
include('../db/db.php');

// Initialize response array
$response = array();

// Handle AJAX request
if ($_SERVER["REQUEST_METHOD"] === "POST") {
    // Validate and sanitize form data
    $bookingId = isset($_POST['booking_id']) ? $_POST['booking_id'] : null;

    // Check if booking ID is provided
    if ($bookingId !== null) {
        // Perform database update to cancel the booking
        $sql = "UPDATE bookings SET status = 'Cancelled' WHERE booking_id = ?";
        $stmt = $conn->prepare($sql);

        if ($stmt === false) {
            // Error handling for prepare failure
            $response = array("status" => "error", "message" => $conn->error);
        } else {
            $stmt->bind_param("i", $bookingId);

            if ($stmt->execute()) {
                // Booking cancellation successful
                $response = array("status" => "success");
            } else {
                // Error handling for execute failure
                $response = array("status" => "error", "message" => $stmt->error);
            }

            // Close statement
            $stmt->close();
        }
    } else {
        // Booking ID not provided
        $response = array("status" => "error", "message" => "Booking ID not provided.");
    }
} else {
    // Handle non-AJAX request
    $response = array("status" => "error", "message" => "Invalid request!");
}

// Return JSON response
header('Content-Type: application/json');
echo json_encode($response);
exit;
?>
