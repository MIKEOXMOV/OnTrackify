<?php
session_start();
include 'config.php';

$user_id = $_SESSION['user_id'];
$role = $_SESSION['role'];

try {
    $conn = new PDO("mysql:host=$servername;dbname=$dbname", $username, $password);
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    // Prepare and execute the SQL statement to count unread notifications
    $stmt = $conn->prepare("SELECT COUNT(*) as count FROM notifications WHERE user_id = :user_id AND role = :role AND status = 'unread'");
    $stmt->bindParam(':user_id', $user_id, PDO::PARAM_INT);
    $stmt->bindParam(':role', $role, PDO::PARAM_STR);
    $stmt->execute();

    $result = $stmt->fetch(PDO::FETCH_ASSOC);
    echo json_encode(['count' => $result['count']]);
} catch(PDOException $e) {
    echo json_encode(['error' => $e->getMessage()]);
}

$conn = null;
?>
