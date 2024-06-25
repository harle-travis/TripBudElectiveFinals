<?php
$servername = "localhost"; // Change this if your database server is different
$username = "root"; // Change this to your database username
$password = ""; // Change this to your database password
$database = "southpick_db"; // Change this to your database name
$root = "3307";
// Create connection
$conn = new mysqli($servername, $username, $password, $database,$root);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}
?>
