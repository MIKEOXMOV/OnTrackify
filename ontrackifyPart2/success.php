<?php
session_start();

// Redirect if user is not logged in as coordinator
if (!isset($_SESSION['user_id']) || $_SESSION['role'] != 'coordinator') {
    header("Location: login.php");
    exit();
}

include 'config.php';

// Get project_id from GET data
$project_id = $_GET['project_id'];
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Success</title>
    <link rel="stylesheet" href="stdstyle.css">
    <style>
        body {
            background-color: #ffffff;
            font-family: Arial, sans-serif;
            line-height: 1.6;
            text-align: center;
        }

        .container {
            max-width: 800px;
            margin: 0 auto;
            padding: 20px;
        }

        .success-message {
            margin-top: 50px;
            background-color: #d4edda;
            color: #155724;
            border: 1px solid #c3e6cb;
            padding: 15px;
            border-radius: 5px;
        }

        .back-to-dashboard,
        .view-groups {
            margin-top: 20px;
            display: inline-block;
            padding: 10px 20px;
            background-color: #007bff;
            color: #fff;
            text-decoration: none;
            border-radius: 5px;
        }

        .back-to-dashboard:hover,
        .view-groups:hover {
            background-color: #0056b3;
        }
    </style>
</head>
<body>
    <div class="container">
        <h1>Groups Successfully Finalized</h1>
        <div class="success-message">
            <p>Your groups have been successfully finalized.</p>
        </div>
        <a href="coordinator_panel.php" class="back-to-dashboard">Back to Dashboard</a>
        <a href="view_groups.php?project_id=<?= htmlspecialchars($project_id, ENT_QUOTES, 'UTF-8') ?>" class="view-groups">View Groups</a>
    </div>
</body>
</html>