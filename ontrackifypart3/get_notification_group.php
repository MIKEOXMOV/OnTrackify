<?php
session_start();
include 'config.php';

$user_id = $_SESSION['user_id'];

// Fetch notifications for the user
$sql = "SELECT id, message, created_at FROM notificationsgroup WHERE student_id = ? AND is_read = 0";
$stmt = $conn->prepare($sql);
$stmt->bind_param('i', $user_id);
$stmt->execute();
$result = $stmt->get_result();

$notifications = [];
while ($row = $result->fetch_assoc()) {
    $notifications[] = $row;
}

echo json_encode($notifications);
?>