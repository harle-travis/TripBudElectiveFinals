<?php
// Include the database connection file
include('../db/db.php');

// Initialize response array
$response = array();

// Handle AJAX request
if ($_SERVER["REQUEST_METHOD"] === "POST") {
    // Start session
    session_start();

    // Validate and sanitize form data
    $gcashNumber = htmlspecialchars($_POST['gcash_number']);
    $referenceNumber = htmlspecialchars($_POST['reference_number']);
    $amount = $_POST['amount'];
    $bookingId = $_POST['booking_id'];

    // Check if the reference number already exists in the database
    $checkSql = "SELECT * FROM payments WHERE reference_number = ?";
    $checkStmt = $conn->prepare($checkSql);
    $checkStmt->bind_param("s", $referenceNumber);
    $checkStmt->execute();
    $checkResult = $checkStmt->get_result();

    if ($checkResult->num_rows > 0) {
        // Reference number already exists, return error response
        $response = array("status" => "error", "message" => "Reference number has already been used.");
    } else {
        // Perform database insertion
        if (isset($_SESSION['user_id'])) {
            $userId = $_SESSION['user_id'];
            $sql = "INSERT INTO payments (user_id, booking_id, gcash_number, reference_number, amount)
            VALUES (?, ?, ?, ?, ?)";
            $stmt = $conn->prepare($sql);

            if ($stmt === false) {
                // Error handling for prepare failure
                $response = array("status" => "error", "message" => $conn->error);
            } else {
                $stmt->bind_param("iisdd", $userId, $bookingId, $gcashNumber, $referenceNumber, $amount);

                if ($stmt->execute()) {
                    $response = array("status" => "success");
                } else {
                    // Error handling for execute failure
                    $response = array("status" => "error", "message" => $stmt->error);
                }

                // Close statement
                $stmt->close();
            }
        } else {
            $response = array("status" => "error", "message" => "User session not found.");
        }
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
