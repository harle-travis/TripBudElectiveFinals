<?php
// Include the database connection
include('../db/db.php');

// Check if the request is a POST request
if ($_SERVER["REQUEST_METHOD"] === "POST") {
    // Decode the JSON data from the request body
    $data = json_decode(file_get_contents("php://input"));

    // Check if the data is not null and contains room_ids
    if ($data !== null && isset($data->room_ids)) {
        try {
            // Prepare the SQL statement to delete records
            $sql = "DELETE FROM rooms WHERE room_id IN (";
            $placeholders = implode(",", array_fill(0, count($data->room_ids), "?"));
            $sql .= $placeholders . ")";
        
            // Prepare the statement
            $stmt = $conn->prepare($sql);
            
            // Bind the room IDs to the statement parameters
            $stmt->bind_param(str_repeat("i", count($data->room_ids)), ...$data->room_ids);
            
            // Execute the statement
            if ($stmt->execute()) {
                // Return success response
                echo json_encode(array("success" => true));
                exit;
            } else {
                // Return error response
                echo json_encode(array("success" => false, "error" => "Failed to execute SQL query"));
                exit;
            }
        } catch (Exception $e) {
            // Return error response
            echo json_encode(array("success" => false, "error" => $e->getMessage()));
            exit;
        }
    } else {
        // If data is missing, return error response
        echo json_encode(array("success" => false, "error" => "Invalid data"));
        exit;
    }
}

// If the request is not a POST request, return error response
echo json_encode(array("success" => false, "error" => "Invalid request"));
?>
