<?php
session_start();
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

include 'config.php';



// Example query to retrieve notifications
$query = "SELECT id, message, recipient FROM notifications";

$result = $conn->query($query);
if ($result->num_rows > 0) {
    $notifications = array();
    while ($row = $result->fetch_assoc()) {
        $notifications[] = $row;
    }
    echo json_encode($notifications);
} else {
    echo json_encode(array()); // Return empty array if no notifications found
}
// Fetch unread notifications count
$unread_sql = "SELECT COUNT(*) as unread_count FROM notifications WHERE recipient = 'student' AND `read` = 0";
$unread_result = $conn->query($unread_sql);
$unread_count = $unread_result->fetch_assoc()['unread_count'];

// Close connection
$conn->close();

echo json_encode(['unread_count' => $unread_count]);
?>

