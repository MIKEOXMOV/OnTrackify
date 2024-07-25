<?php
session_start();
include 'config.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $notificationId = $_POST['id'];

    // Mark the notification as read in the database
    $sql = "UPDATE notifications SET is_read = 1 WHERE id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param('i', $notificationId);
    $stmt->execute();

    if ($stmt->affected_rows > 0) {
        echo "Notification marked as read.";
    } else {
        echo "Failed to mark notification as read.";
    }
}
?>