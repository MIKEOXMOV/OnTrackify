<?php
function updateNotificationCount($conn, $user_id) {
    $query = "SELECT COUNT(*) AS unread_count FROM notifications WHERE student_id = ? AND is_read = 0";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("i", $user_id);
    $stmt->execute();
    $result = $stmt->get_result();
    $row = $result->fetch_assoc();
    $_SESSION['notification_count'] = $row['unread_count'];
}
?>