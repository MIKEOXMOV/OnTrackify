<?php
session_start();
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

// Redirect to compose_notification.php
header("Location: compose_notification.php");
exit();
?>
