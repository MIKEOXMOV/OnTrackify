<?php
session_start();

// Redirect if user is not logged in as coordinator
if (!isset($_SESSION['user_id']) || $_SESSION['role'] != 'coordinator') {
    header("Location: login.php");
    exit();
}

include 'config.php';

// Function to fetch group information created by the current coordinator for a specific project
function fetchGroups($conn, $project_id) {
    $query = "
        SELECT g.group_id, GROUP_CONCAT(u.name SEPARATOR ', ') AS group_members
        FROM groups g
        JOIN group_members gm ON g.group_id = gm.group_id
        JOIN users u ON gm.student_id = u.id
        WHERE g.coordinator_id = ? AND g.project_id = ?
        GROUP BY g.group_id
    ";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("ii", $_SESSION['user_id'], $project_id);
    $stmt->execute();
    $result = $stmt->get_result();
    return $result->fetch_all(MYSQLI_ASSOC);
}

// Fetch group information for the specified project
$project_id = $_GET['project_id'];
$group_info = fetchGroups($conn, $project_id);

?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Coordinator View Groups</title>
    <link rel="stylesheet" href="stdstyle.css">
    <style>
        body {
            background-color: #f5f5f5;
            font-family: Arial, sans-serif;
            line-height: 1.6;
            color: #333;
        }

        .container {
            max-width: 800px;
            margin: 0 auto;
            padding: 20px;
            background-color: #ffffff;
            border-radius: 8px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
        }

        .header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 20px;
        }

        .header .logo {
            display: flex;
            align-items: center;
        }

        .header img {
            height: 50px;
            margin-right: 10px;
        }

        .header h1 {
            margin: 0;
            font-size: 24px;
            color: #007bff;
        }

        .table-container {
            overflow-x: auto;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;
        }

        table, th, td {
            border: 1px solid #ddd;
        }

        th, td {
            padding: 10px;
            text-align: left;
        }

        th {
            background-color: #007bff;
            color: #ffffff;
        }

        .back-to-dashboard {
            display: inline-block;
            padding: 10px 20px;
            background-color: #007bff;
            color: #fff;
            text-decoration: none;
            border-radius: 5px;
            transition: background-color 0.3s;
        }

        .back-to-dashboard:hover {
            background-color: #0056b3;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <div class="logo">
                <img src="logo.png" alt="Logo"> <!-- Replace with your logo path -->
                <h1>OnTrackify</h1>
            </div>
            <a href="coordinator_panel.php" class="back-to-dashboard">Back to Dashboard</a>
        </div>

        <h3>Groups Created by Coordinator for Project <?= htmlspecialchars($project_id, ENT_QUOTES, 'UTF-8') ?></h3>
        
        <div class="table-container">
            <table>
                <thead>
                    <tr>
                        <th>Group ID</th>
                        <th>Group Members</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($group_info as $group): ?>
                        <tr>
                            <td><?= htmlspecialchars($group['group_id'], ENT_QUOTES, 'UTF-8') ?></td>
                            <td>
                                <ul style="list-style: none; padding: 0; margin: 0;">
                                    <?php $members = explode(', ', $group['group_members']); ?>
                                    <?php foreach ($members as $member): ?>
                                        <li><?= htmlspecialchars($member, ENT_QUOTES, 'UTF-8') ?></li>
                                    <?php endforeach; ?>
                                </ul>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>
</body>
</html>