<?php
session_start();
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "role_management";

// Create connection
$conn = new mysqli($servername, $username, $password, $dbname);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

$user_id = $_SESSION['user_id']; // Assume this is set during login

// Fetch group details for the logged-in user
$groupQuery = "SELECT group_id FROM student_groups WHERE student_id = ?";
$stmt = $conn->prepare($groupQuery);
$stmt->bind_param("i", $user_id);
$stmt->execute();
$stmt->bind_result($group_id);
$stmt->fetch();
$stmt->close();

if ($group_id) {
    // If group_id is found, redirect to group_chat.php
    header("Location: group_chat.php?group_id=" . $group_id);
    exit();
} else {
    echo "You are not part of any group.";
}

$conn->close();
?>