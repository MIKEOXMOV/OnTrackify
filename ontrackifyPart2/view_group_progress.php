<?php
session_start();

// Redirect if user is not logged in as coordinator
if (!isset($_SESSION['user_id']) || $_SESSION['role'] != 'coordinator') {
    header("Location: login.php");
    exit();
}

include 'config.php';

$coordinator_id = $_SESSION['user_id'];

// Fetch projects created by the coordinator
$sql_projects = "SELECT * FROM projects WHERE coordinator_id = ?";
$stmt_projects = $conn->prepare($sql_projects);
$stmt_projects->bind_param("i", $coordinator_id);
$stmt_projects->execute();
$result_projects = $stmt_projects->get_result();
$projects = $result_projects->fetch_all(MYSQLI_ASSOC);

// Fetch groups and their progress for the selected project if provided
if (isset($_GET['project_id'])) {
    $project_id = $_GET['project_id'];

    // Fetch groups and their progress for the specified project
    $sql_groups = "
    SELECT g.group_id, g.group_size, 
           COALESCE(SUM(p.idea_submission), 0) AS idea_submission,
           COALESCE(SUM(p.idea_confirmation), 0) AS idea_confirmation,
           COALESCE(SUM(p.design_phase), 0) AS design_phase,
           COALESCE(SUM(p.task_appointment), 0) AS task_appointment,
           COALESCE(SUM(p.zeroth_phase_presentation), 0) AS zeroth_phase_presentation,
           COALESCE(SUM(p.objectives), 0) AS objectives,
           COALESCE(SUM(p.system_architecture), 0) AS system_architecture,
           COALESCE(SUM(p.ppt_making_first_presentation), 0) AS ppt_making_first_presentation,
           COALESCE(SUM(p.first_presentation), 0) AS first_presentation,
           COALESCE(SUM(p.fifty_percent_project_completed), 0) AS fifty_percent_project_completed,
           COALESCE(SUM(p.seventy_five_percent_project_completed), 0) AS seventy_five_percent_project_completed,
           COALESCE(SUM(p.hundred_percent_project_completed), 0) AS hundred_percent_project_completed,
           COALESCE(SUM(p.internal_presentation), 0) AS internal_presentation,
           COALESCE(SUM(p.verified_report), 0) AS verified_report,
           COALESCE(SUM(p.external_presentation), 0) AS external_presentation
           
    FROM groups g
    LEFT JOIN progress p ON g.group_id = p.group_id
    WHERE g.project_id = ?
    GROUP BY g.group_id, g.group_size
    ORDER BY g.group_id";
    $stmt_groups = $conn->prepare($sql_groups);
    $stmt_groups->bind_param("i", $project_id);
    $stmt_groups->execute();
    $result_groups = $stmt_groups->get_result();
    $groups = $result_groups->fetch_all(MYSQLI_ASSOC);
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>View Group Progress</title>
    <link rel="stylesheet" href="stdstyle.css">
     <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <style>
        body {
            background-color: #f0f0f0;
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 20px;
            display: flex;
            justify-content: center;
            align-items: center;
            min-height: 100vh;
        }

        .container {
            background-color: #fff;
            border-radius: 8px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
            padding: 20px;
            width: 100%;
            max-width: 800px;
        }

        h1, h2 {
            text-align: center;
            color: #333;
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
            color: #000;
        }

        .group-list {
            margin-top: 20px;
            background-color: #fff;
            border: 1px solid #ccc;
            border-radius: 5px;
            padding: 20px;
        }

        .group-item {
            margin-bottom: 10px;
            padding: 10px;
            border: 1px solid #ccc;
            border-radius: 5px;
            background-color: #f9f9f9;
        }

        .group-item h3 {
            margin: 5px 0;
            color: #007bff;
        }

        .progress-bar {
            background-color: #ddd;
            border-radius: 5px;
            height: 15px;
            margin-top: 5px;
            overflow: hidden;
        }

        .progress {
            background-color: #007bff;
            height: 100%;
        }

        .progress-text {
            text-align: center;
            margin-top: 5px;
        }
    </style>
</head>
<body>
<nav class="navbar navbar-light bg-light">
    <a class="navbar-brand" href="#">
        <img src="logo.png" width="40" height="40" class="d-inline-block align-top" alt="">
        OnTrackify
    </a>
    <a href="coordinator_panel.php" class="btn btn-primary ml-auto">Back</a>
</nav>
    <div class="container">
        <h1>View Group Progress</h1>

        <div class="project-list">
            <h2>Your Projects</h2>
            <?php foreach ($projects as $project): ?>
                <div class="project-item">
                    <a href="view_group_progress.php?project_id=<?= $project['id'] ?>"><?= htmlspecialchars($project['name'], ENT_QUOTES, 'UTF-8') ?></a>
                </div>
            <?php endforeach; ?>
        </div>

        <?php if (isset($groups)): ?>
            <div class="group-list">
                <h2>Project Groups and Progress</h2>
                <?php foreach ($groups as $group): ?>
                    <div class="group-item">
                        <h3>Group : <?= htmlspecialchars($group['group_id'], ENT_QUOTES, 'UTF-8') ?></h3>
                        <div class="progress-bar">
                            <?php
                            $total_tasks = 15; // Total number of tasks in the `progress` table
                            $completed_tasks = $group['idea_submission'] + $group['idea_confirmation'] + $group['design_phase'] +
                                               $group['task_appointment'] + $group['zeroth_phase_presentation'] + 
                                               $group['objectives'] + $group['system_architecture'] + 
                                               $group['ppt_making_first_presentation'] + $group['first_presentation'] +
                                               $group['fifty_percent_project_completed'] + $group['seventy_five_percent_project_completed'] +
                                               $group['hundred_percent_project_completed'] + $group['internal_presentation'] +
                                               $group['verified_report'] + $group['external_presentation'];
                            $progress_percent = ($total_tasks > 0) ? ($completed_tasks / $total_tasks) * 100 : 0;
                            ?>
                            <div class="progress" style="width: <?= $progress_percent ?>%;"></div>
                        </div>
                        <div class="progress-text">
                            Progress: <?= $completed_tasks ?> / <?= $total_tasks ?> tasks completed
                        </div>

                    </div>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>
    </div>
</body>
</html>