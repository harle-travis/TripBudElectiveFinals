<?php
// Start the session
@session_start();

include('../db/db.php');

// Check if the user is logged in and the user ID is set in the session
if(isset($_SESSION['user_id'])) {
    $userId = $_SESSION['user_id'];

    // Prepare the SQL statement to update the is_read status of notifications
    $sql = "UPDATE notifications SET is_read = 1 WHERE user_id = ? AND is_read = 0";
    $stmt = $conn->prepare($sql);

    if ($stmt) {
        // Bind parameters and execute the statement
        $stmt->bind_param('i', $userId); // Assuming user_id is an integer
        $stmt->execute();
        $stmt->close();

        // Output success message
        echo "Notifications marked as read successfully.";
    } else {
        // Handle the case where the statement preparation fails
        echo "Error preparing statement: " . $conn->error;
    }

    // Close the database connection
    $conn->close();
} else {
    // Handle the case where the user is not logged in
    echo "User is not logged in.";
}
?>
