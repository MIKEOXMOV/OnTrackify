<?php
session_start();

// Redirect if user is not logged in as coordinator
if (!isset($_SESSION['user_id']) || $_SESSION['role'] != 'coordinator') {
    header("Location: login.php");
    exit();
}

include 'config.php';

// Function to fetch newly created groups for a project
function fetchNewlyCreatedGroups($conn, $project_id) {
    $query = "SELECT g.group_id, g.group_size, GROUP_CONCAT(u.name SEPARATOR ', ') AS group_members
              FROM groups g
              JOIN group_members gm ON g.group_id = gm.group_id
              JOIN users u ON gm.student_id = u.id
              WHERE g.project_id = ? AND g.created_at >= NOW() - INTERVAL 1 HOUR
              GROUP BY g.group_id
    ";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("i", $project_id);
    $stmt->execute();
    $result = $stmt->get_result();
    return $result->fetch_all(MYSQLI_ASSOC);
}

// Get project_id from query parameter
$project_id = $_GET['project_id'];

// Fetch newly created groups for the project
$groups = fetchNewlyCreatedGroups($conn, $project_id);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Newly Created Groups</title>
    <link rel="stylesheet" href="stdstyle.css">
    <style>
        /* Add your custom styles here */
        body {
            background-color: #ffffff; /* White background */
            font-family: Arial, sans-serif;
            line-height: 1.6;
        }

        .container {
            max-width: 800px;
            margin: 0 auto;
            padding: 20px;
        }

        .group-info {
            margin-top: 20px;
        }

        .group {
            margin-bottom: 20px;
            padding: 10px;
            border: 1px solid #ccc;
            border-radius: 5px;
        }

        .group h3 {
            margin-bottom: 10px;
        }

        .group-members {
            list-style-type: none;
            padding: 0;
        }

        .group-members li {
            margin-bottom: 5px;
        }

        .action-btn {
            margin-top: 20px;
        }
    </style>
</head>
<body>
    <div class="container">
        <h1>Newly Created Groups</h1>
        <div class="group-info">
            <?php foreach ($groups as $group): ?>
                <div class="group">
                    <h3>Group ID: <?= $group['group_id'] ?></h3>
                    <p>Group Size: <?= $group['group_size'] ?></p>
                    <ul class="group-members">
                        <?php $members = explode(', ', $group['group_members']); ?>
                        <?php foreach ($members as $member): ?>
                            <li><?= htmlspecialchars($member, ENT_QUOTES, 'UTF-8') ?></li>
                        <?php endforeach; ?>
                    </ul>
                </div>
            <?php endforeach; ?>
        </div>

        <!-- Finalize Button -->
        <form action="finalize.php" method="POST" class="action-btn">
            <input type="hidden" name="project_id" value="<?= htmlspecialchars($project_id, ENT_QUOTES, 'UTF-8') ?>">
            <button type="submit" name="finalize">Finalize</button>
        </form>

        <!-- Regroup Button -->
        <form action="regroup.php" method="POST" class="action-btn">
            <input type="hidden" name="project_id" value="<?= htmlspecialchars($project_id, ENT_QUOTES, 'UTF-8') ?>">
            <button type="submit" name="regroup">Regroup</button>
        </form>
    </div>
</body>
</html>