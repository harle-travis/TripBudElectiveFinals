<?php
include('../db/db.php');
session_start(); // Start the session

$response = array(); // Initialize an empty array to store the response

try {
    if ($_SERVER["REQUEST_METHOD"] == "POST") {
        // Retrieve form data
        $room_id = $_POST['room_id'];
        $checkin_date = $_POST['checkin_date'];
        $checkout_date = $_POST['checkout_date'];
        $checkin_time = $_POST['checkin_time'];
        $checkout_time = $_POST['checkout_time'];
        $num_adults = $_POST['num_adults'];
        $num_children = $_POST['num_children'];
        $total_price = $_POST['total_price'];
        $user_id = $_SESSION['user_id'];

        // Retrieve user_id from session (assuming it's stored in the session after login)

        // Check if the requested time slot is available
        $check_availability_query = "SELECT * FROM bookings WHERE room_id = '$room_id' AND status IN ('Pending', 'Approve') AND 
            ((checkin_date <= '$checkin_date' AND checkout_date >= '$checkin_date' AND 
            (checkin_time < '$checkout_time' AND checkout_time > '$checkin_time')) OR 
            (checkin_date < '$checkout_date' AND checkout_date >= '$checkout_date' AND 
            (checkin_time < '$checkout_time' AND checkout_time > '$checkin_time')))";
        $availability_result = mysqli_query($conn, $check_availability_query);
        
        if (!$availability_result) {
            throw new Exception("Error executing availability query: " . mysqli_error($conn));
        }
        
        if (mysqli_num_rows($availability_result) > 0) {
            // Conflict found, the requested time slot is not available
            $response['success'] = false;
            $response['message'] = "The requested time slot is not available. Please choose a different time.";
        } else {
            // No conflict, proceed with the booking
            $insert_booking_query = "INSERT INTO bookings (user_id, room_id, checkin_date, checkout_date, checkin_time, checkout_time, num_adults, num_children, total_price, status) 
                                    VALUES ('$user_id', '$room_id', '$checkin_date', '$checkout_date', '$checkin_time', '$checkout_time', '$num_adults', '$num_children', '$total_price', 'Pending')";
            $result_status = mysqli_query($conn, $insert_booking_query);
            
            if (!$result_status) {
                throw new Exception("Error inserting booking: " . mysqli_error($conn));
            }
            
            // Booking and status inserted successfully
            $response['success'] = true;
            $response['message'] = "Booking successfully submitted. Kindly wait for the admin to confirm your booking.";
        }
    } else {
        throw new Exception("Invalid request method: " . $_SERVER["REQUEST_METHOD"]);
    }
} catch (Exception $e) {
    // Handle exceptions by logging them
    error_log($e->getMessage());
    
    // Set error response
    $response['success'] = false;
    $response['message'] = "An error occurred. Please try again later.";
}

// Return the response as JSON
echo json_encode($response);
?>
