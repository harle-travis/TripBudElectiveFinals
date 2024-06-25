<?php
// Include database connection
include('../db/db.php');

// Check if form is submitted
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Validate room ID
    $room_id = $_POST['room_id'];
    if (empty($room_id) || !is_numeric($room_id)) {
        // Redirect with error message if room ID is invalid
        header("Location: edit-room.php?room_id={$room_id}&error=invalid_room_id");
        exit;
    }

    // Retrieve form data
    $room_name = $_POST['room_name'];
    $price = $_POST['price'];
    $room_size = $_POST['room_size'];
    $num_rooms = $_POST['num_rooms'];
    $num_bathrooms = $_POST['num_bathrooms'];

    // Check if a new image file is uploaded
    if ($_FILES['image_path']['error'] === UPLOAD_ERR_OK) {
        $file_name = $_FILES['image_path']['name'];
        $file_tmp = $_FILES['image_path']['tmp_name'];
        $file_type = $_FILES['image_path']['type'];

        // Move the uploaded file to a permanent location
        $target_directory = "../uploads/";
        $target_file = $target_directory . basename($file_name);
        move_uploaded_file($file_tmp, $target_file);

        // Update the database with the new file location
        $sql = "UPDATE rooms SET room_name=?, price=?, room_size=?, num_rooms=?, num_bathrooms=?, image_path=?, amenities=? WHERE room_id=?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("sssssssi", $room_name, $price, $room_size, $num_rooms, $num_bathrooms, $target_file, $amenities, $room_id);
    } else {
        // No new image uploaded, update the database without changing the image_path
        $sql = "UPDATE rooms SET room_name=?, price=?, room_size=?, num_rooms=?, num_bathrooms=?, amenities=? WHERE room_id=?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("ssssssi", $room_name, $price, $room_size, $num_rooms, $num_bathrooms, $amenities, $room_id);
    }

    // Retrieve amenities
    $amenities = isset($_POST['amenities']) ? implode(",", $_POST['amenities']) : '';

    // Execute SQL query to update room details
    if ($stmt->execute()) {
        // Room details updated successfully
        header("Location: ../admin/rooms.php?success=room_updated");
        exit;
    } else {
        // Error occurred while updating room details
        header("Location: ../admin/edit-room.php?room_id={$room_id}&error=update_failed");
        exit;
    }
} else {
    // Redirect with error message if form is not submitted via POST method
    header("Location: ../admin/rooms.php?error=invalid_request");
    exit;
}
?>
