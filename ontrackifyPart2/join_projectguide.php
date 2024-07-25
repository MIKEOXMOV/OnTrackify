<?php
session_start();
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'guide') {
    header("Location: login.php");
    exit();
}

include 'config.php';

$guide_id = $_SESSION['user_id'];
$project_id = $_POST['project_id'];

// Insert the guide and project relationship
$sql = "INSERT INTO guide_projects (guide_id, project_id) VALUES ('$guide_id', '$project_id')";
if ($conn->query($sql) === TRUE) {
    header("Location: joined_projects.php");
} else {
    echo "Error: " . $sql . "<br>" . $conn->error;
}

$conn->close();
?>