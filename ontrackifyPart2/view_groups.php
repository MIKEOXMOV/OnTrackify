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

// Handle notify all action
if (isset($_POST['notify_all'])) {
    // Retrieve project ID from URL
    $project_id = $_GET['project_id'];

    $query = "
        SELECT gm.student_id
        FROM groups g
        JOIN group_members gm ON g.group_id = gm.group_id
        WHERE g.coordinator_id = ? AND g.project_id = ?
    ";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("ii", $_SESSION['user_id'], $project_id);
    $stmt->execute();
    $result = $stmt->get_result();
    $students = $result->fetch_all(MYSQLI_ASSOC);

    $conn->begin_transaction();

    foreach ($students as $student) {
        $student_id = $student['student_id'];
        $message = 'You have a new notification from your coordinator for Project ' . $project_id;

        $query_insert_notification = "
            INSERT INTO notificationsgroup (student_id, message, is_read, created_at) 
            VALUES (?, ?, 0, NOW())
        ";
        $stmt_insert_notification = $conn->prepare($query_insert_notification);
        $stmt_insert_notification->bind_param("is", $student_id, $message);
        $stmt_insert_notification->execute();
    }

    $conn->commit();
    $notification_success = true;
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
            text-align: center;
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

        .notify-success {
            margin-top: 20px;
            color: green;
        }

        .back-to-dashboard {
            margin-top: 20px;
            display: inline-block;
            padding: 10px 20px;
            background-color: #007bff; /* Bootstrap primary button color */
            color: #fff;
            text-decoration: none;
            border-radius: 5px;
        }

        .back-to-dashboard:hover {
            background-color: #0056b3; /* Darker shade of primary color on hover */
        }
    </style>
</head>
<body>
    <div class="container">
        <?php if (isset($notification_success) && $notification_success): ?>
            <h1>Notifications Sent Successfully</h1>
            <p class="notify-success">Notifications sent to all group members successfully!</p>
            <a href="coordinator_panel.php" class="back-to-dashboard">Back to Dashboard</a>
        <?php else: ?>
            <h1>Groups Created by Coordinator for Project <?= htmlspecialchars($project_id, ENT_QUOTES, 'UTF-8') ?></h1>
            <div class="group-info">
                <?php foreach ($group_info as $group): ?>
                    <div class="group">
                        <h3>Group ID: <?= htmlspecialchars($group['group_id'], ENT_QUOTES, 'UTF-8') ?></h3>
                        <ul class="group-members">
                            <?php $members = explode(', ', $group['group_members']); ?>
                            <?php foreach ($members as $member): ?>
                                <li><?= htmlspecialchars($member, ENT_QUOTES, 'UTF-8') ?></li>
                            <?php endforeach; ?>
                        </ul>
                    </div>
                <?php endforeach; ?>
            </div>

            <!-- Notify All Button -->
            <form action="<?= $_SERVER['PHP_SELF'] ?>?project_id=<?= $project_id ?>" method="POST" class="action-btn">
                <button type="submit" name="notify_all">Notify All</button>
            </form>
        <?php endif; ?>
    </div>
</body>
</html>