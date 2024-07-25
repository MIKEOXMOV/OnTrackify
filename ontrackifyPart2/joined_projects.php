<?php
session_start();
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'guide') {
    header("Location: login.php");
    exit();
}

include 'config.php';

$guide_id = $_SESSION['user_id'];

// Fetch joined projects for the guide
$sql = "SELECT p.id, p.name, p.semester, p.course_code, u.name AS coordinator_name
        FROM projects p
        JOIN guide_projects gp ON p.id = gp.project_id
        JOIN users u ON p.coordinator_id = u.id
        WHERE gp.guide_id = '$guide_id'";
$result = $conn->query($sql);
$joined_projects = $result->fetch_all(MYSQLI_ASSOC);

$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Joined Projects</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
</head>
<body>
<nav class="navbar navbar-light bg-light">
    <a class="navbar-brand" href="#">
        <img src="logo.png" width="40" height="40" class="d-inline-block align-top" alt="">
        OnTrackify
    </a>
    <a href="guide_panel.php" class="btn btn-primary ml-auto">Back</a>
</nav>

<div class="container mt-5">
    <div class="card mb-4">
        <div class="card-header">
            <h2>Joined Projects</h2>
        </div>
        <div class="card-body">
            <table class="table table-striped">
                <thead>
                    <tr>
                        <th>Project Name</th>
                        <th>Semester</th>
                        <th>Course Code</th>
                        <th>Coordinator</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($joined_projects as $project): ?>
                        <tr>
                            <td><?php echo htmlspecialchars($project['name']); ?></td>
                            <td><?php echo htmlspecialchars($project['semester']); ?></td>
                            <td><?php echo htmlspecialchars($project['course_code']); ?></td>
                            <td><?php echo htmlspecialchars($project['coordinator_name']); ?></td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<script src="https://code.jquery.com/jquery-3.5.1.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.5.3/dist/umd/popper.min.js"></script>
<script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
</body>
</html>