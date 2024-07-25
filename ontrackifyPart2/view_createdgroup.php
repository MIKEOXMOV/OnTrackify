<?php
session_start();
if (!isset($_SESSION['user_id']) || $_SESSION['role'] != 'coordinator') {
    header("Location: login.php");
    exit();
}

include 'config.php';

$coordinator_id = $_SESSION['user_id'];

// Fetch projects created by the coordinator
$query = "SELECT id, name FROM projects WHERE coordinator_id = ?";
$stmt = $conn->prepare($query);
$stmt->bind_param("i", $coordinator_id);
$stmt->execute();
$result = $stmt->get_result();
$projects = $result->fetch_all(MYSQLI_ASSOC);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Coordinator Dashboard</title>
    <link rel="stylesheet" href="stdstyle.css">
    <style>
        body {
            background-color: #fff; /* Set background color to white */
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 0;
        }

        .header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 10px 20px;
            background-color: #f8f8f8;
            border-bottom: 1px solid #ddd;
        }

        .logo {
            display: flex;
            align-items: center;
        }

        .logo img {
            height: 40px;
            margin-right: 10px;
        }

        .back-button {
            text-decoration: none;
            background-color: #007bff;
            color: #fff;
            padding: 10px 20px;
            border-radius: 5px;
            border: none;
            cursor: pointer;
        }

        .container {
            padding: 20px;
        }

        .project-list {
            margin-top: 20px;
        }

        .project-item {
            margin-bottom: 10px;
            padding: 10px;
            border: 1px solid #ccc;
            border-radius: 5px;
            background-color: #f9f9f9;
        }

        .project-item a {
            text-decoration: none;
            color: #007bff;
        }
    </style>
</head>
<body>
    <div class="header">
        <div class="logo">
            <img src="logo.png" alt="Logo"> <!-- Replace with your logo image -->
            <h1>OnTrackify</h1> <!-- Replace with your logo name -->
        </div>
        <a href="coordinator_panel.php" class="back-button">Back</a> <!-- Navigate to coordinator_panel.php -->
    </div>
    <div class="container">
        <div class="project-list">
            <h2>Your Projects</h2>
            <?php foreach ($projects as $project): ?>
                <div class="project-item">
                    <a href="view_groups2.php?project_id=<?= $project['id'] ?>"><?= $project['name'] ?></a>
                </div>
            <?php endforeach; ?>
        </div>
    </div>
</body>
</html>