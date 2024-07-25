<?php
session_start();

if (!isset($_SESSION['user_id'])) {
    http_response_code(403);
    exit('Access denied');
}

$servername = "localhost";
$username = "root";
$password = "";
$dbname = "role_management";

$conn = new mysqli($servername, $username, $password, $dbname);

if ($conn->connect_error) {
    http_response_code(500);
    exit("Connection failed: " . $conn->connect_error);
}

$user_id = $_SESSION['user_id'];
$new_count = $_POST['count'];

// Update notification count in the database
$sql = "UPDATE notifications SET is_read = 0 WHERE student_id = ? AND is_read = 1";
$stmt = $conn->prepare($sql);
$stmt->bind_param("ii", $new_count, $user_id);
$stmt->execute();
$stmt->close();

$conn->close();

// Return success response
http_response_code(200);
echo "Notification count updated successfully.";
?>