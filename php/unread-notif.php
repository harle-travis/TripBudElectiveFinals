<?php
@session_start();

include('../db/db.php');

// Check if the user is logged in and the user ID is set in the session
if(isset($_SESSION['user_id'])) {
    $userId = $_SESSION['user_id'];

    // Prepare the SQL statement to fetch the count of unread notifications
    $sql = "SELECT COUNT(*) AS unread_count FROM notifications WHERE user_id = ? AND is_read = 0";
    $stmt = $conn->prepare($sql);

    if ($stmt) {
        // Bind parameters and execute the statement
        $stmt->bind_param('i', $userId); // Assuming user_id is an integer
        $stmt->execute();

        // Bind the result variable
        $stmt->bind_result($unreadCount);

        // Fetch the result
        $stmt->fetch();

        // Close the statement
        $stmt->close();

        // Output the badge count
        echo $unreadCount;
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
