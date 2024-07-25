<?php
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "notifications";

// Create connection
$conn = new mysqli($servername, $username, $password, $dbname);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Get POST data
$data = json_decode(file_get_contents("php://input"), true);
$notification = $data['notification'];
$recipient = $data['recipient'];

// Insert notification into database
$sql = "INSERT INTO notifications (notification, recipient) VALUES ('$notification', '$recipient')";

if ($conn->query($sql) === TRUE) {
    http_response_code(200);
    echo json_encode(["message" => "Notification sent successfully!"]);
} else {
    http_response_code(500);
    echo json_encode(["message" => "Failed to send notification."]);
}

$conn->close();
?>
