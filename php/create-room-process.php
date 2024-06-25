<?php
// Include the database connection
require_once('../db/db.php');

// Initialize response array
$response = array();

// Check if the form is submitted
if ($_SERVER["REQUEST_METHOD"] === "POST") {
    // Check if a file is uploaded
    if (isset($_FILES['image_path']) && $_FILES['image_path']['error'] === UPLOAD_ERR_OK) {
        // Define upload directory
        $upload_dir = '../Rooms/';

        // Generate a unique filename
        $image_filename = uniqid('room_') . '_' . $_FILES['image_path']['name'];

        // Move uploaded file to the upload directory
        if (move_uploaded_file($_FILES['image_path']['tmp_name'], $upload_dir . $image_filename)) {
            // Prepare and bind SQL statement
            $stmt = $conn->prepare("INSERT INTO rooms (room_name, price, room_size, num_rooms, num_bathrooms, amenities, image_path) VALUES (?, ?, ?, ?, ?, ?, ?)");
            $stmt->bind_param("sssiiss", $room_name, $price, $room_size, $num_rooms, $num_bathrooms, $amenities, $image_path);

            // Set parameters
            $room_name = $_POST['room_name'];
            $price = $_POST['price'];
            $room_size = $_POST['room_size'];
            $num_rooms = $_POST['num_rooms'];
            $num_bathrooms = $_POST['num_bathrooms'];
            $amenities = implode(', ', $_POST['amenities']); // Convert array of amenities into comma-separated string
            $image_path = $upload_dir . $image_filename; // Store the image location

            // Execute the statement
            if ($stmt->execute()) {
                // Insertion successful
                $response['success'] = true;
                $response['message'] = 'Room successfully added.';
            } else {
                // Insertion failed
                $response['success'] = false;
                $response['message'] = 'Error: ' . $conn->error;
            }

            // Close statement
            $stmt->close();
        } else {
            // If file upload failed
            $response['success'] = false;
            $response['message'] = 'Failed to move uploaded file';
        }
    } else {
        // If no file is uploaded
        $response['success'] = false;
        $response['message'] = 'No file uploaded';
    }
} else {
    // If form is not submitted via POST, set error message
    $response['success'] = false;
    $response['message'] = 'Form submission method not recognized';
}

// Close connection
$conn->close();

// Send JSON response
header('Content-Type: application/json');
echo json_encode($response);
?>
