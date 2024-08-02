<?php
// Database connection info
$db_server = "It's not used lol";
$db_name = "But Nice Attempt!";
$db_username = "DucketOnTop";
$db_password = "Imean...Yeah? gg";

// Create connection
$conn = new mysqli($db_server, $db_username, $db_password, $db_name);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}
?>
