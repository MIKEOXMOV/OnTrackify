<?php
session_start();

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

include 'config.php'; // Include your database connection or configuration file

// Fetch student's group_id from group_members table
$user_id = $_SESSION['user_id'];

$selectGroupQuery = "SELECT group_id FROM group_members WHERE student_id = ?";
$stmtGroup = $conn->prepare($selectGroupQuery);
$stmtGroup->bind_param("i", $user_id);
$stmtGroup->execute();
$resultGroup = $stmtGroup->get_result();

if ($resultGroup && $resultGroup->num_rows > 0) {
    $row = $resultGroup->fetch_assoc();
    $group_id = $row['group_id'];

    // Prepare and execute query to fetch progress for the student's group_id
    $selectQuery = "SELECT * FROM progress WHERE group_id = ?";
    $stmtProgress = $conn->prepare($selectQuery);
    $stmtProgress->bind_param("i", $group_id);
    $stmtProgress->execute();
    $resultProgress = $stmtProgress->get_result();

    if ($resultProgress && $resultProgress->num_rows > 0) {
        // Fetch progress data
        $progress = $resultProgress->fetch_assoc();
    } else {
        echo "No progress found for Group ID: $group_id";
        exit();
    }

    // Close statements and database connection
    $stmtProgress->close();
    $stmtGroup->close();
    $conn->close();
} else {
    echo "Group ID not found for User ID: $user_id";
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Group Progress</title>
    <style>
        /* Styling for the progress bar and container */
        .container {
            max-width: 600px;
            margin: 20px auto;
            padding: 20px;
            background-color: #f0f0f0;
            border-radius: 5px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
        }

        .progress {
            width: 100%;
            height: 30px;
            background-color: #e0e0e0;
            border-radius: 5px;
            margin-bottom: 20px;
            overflow: hidden;
        }

        .progress-bar {
            height: 100%;
            background-color: #4d5bf9;
            text-align: center;
            line-height: 30px;
            color: white;
            font-weight: bold;
        }

        /* Styling for the task list */
        ul {
            list-style-type: none;
            padding: 0;
        }

        li {
            margin-bottom: 10px;
            padding: 10px;
            border-radius: 5px;
            background-color: #fff; /* Default background color */
        }

        .completed {
            background-color: #b1ffc8; /* Light green for completed tasks */
        }

        .incomplete {
            background-color: #ffc1c1; /* Light red for incomplete tasks */
        }.back-button {
            text-decoration: none;
            background-color: #007bff;
            color: #fff;
            padding: 10px 20px;
            border-radius: 5px;
            border: none;
            cursor: pointer;
        }
        .header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            background-color: #fff;
            padding: 10px 20px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
        }
        .logo {
            display: flex;
            align-items: center;
        }

        .logo img {
            height: 40px;
            margin-right: 10px;
        }

        .logo-name {
            font-size: 24px;
            font-weight: bold;
            color: #333;
        }
    </style>
</head>
<body>
<div class="header">
        <div class="logo">
            <img src="logo.png" alt="Logo">
            <div class="logo-name">OnTrackify</div>
        </div>
        <a href="group_panel.php" class="back-button">Back</a>
    </div>
    <div class="container">
        <h1>Progress for Group ID: <?php echo $group_id; ?></h1>

        <!-- Progress Bar -->
        <div class="progress">
            <?php
            // Calculate overall progress
            $totalTasks = 15; // Assuming there are 15 tasks
            $completedTasks = 0;

            foreach ($progress as $key => $value) {
                if ($key !== 'group_id' && $value == 1) {
                    $completedTasks++;
                }
            }

            // Calculate percentage
            $percentage = round(($completedTasks / $totalTasks) * 100);

            // Display progress bar
            echo "<div class='progress-bar' style='width: $percentage%;'>$percentage%</div>";
            ?>
        </div>

        <!-- Task List -->
        <h2>Task Progress</h2>
        <ul>
            <?php
            foreach ($progress as $task => $value) {
                if ($task !== 'group_id') {
                    $taskName = ucwords(str_replace('_', ' ', $task));
                    $status = $value == 1 ? 'Completed' : 'Not Completed';
                    $statusClass = $value == 1 ? 'completed' : 'incomplete';
                    echo "<li class='$statusClass'>$taskName: $status</li>";
                }
            }
            ?>
        </ul>
    </div>
</body>
</html>