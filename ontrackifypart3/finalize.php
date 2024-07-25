<?php
session_start();

// Redirect if user is not logged in as coordinator
if (!isset($_SESSION['user_id']) || $_SESSION['role'] != 'coordinator') {
    header("Location: login.php");
    exit();
}

include 'config.php';

// Get project_id from POST data
$project_id = $_POST['project_id'];

// Perform actions to finalize groups (update status, etc.)

// Redirect or display success message
header("Location: success.php?project_id=" . $project_id);
exit();
?>