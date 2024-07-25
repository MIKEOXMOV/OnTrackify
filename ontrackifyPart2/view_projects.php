<?php
session_start();
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'guide') {
    header("Location: login.php");
    exit();
}

include 'config.php';

// Fetch all projects
$sql = "SELECT p.id, p.name, p.semester, p.course_code, u.name AS coordinator_name
        FROM projects p
        JOIN users u ON p.coordinator_id = u.id";
$result = $conn->query($sql);
$projects = $result->fetch_all(MYSQLI_ASSOC);

// Fetch projects that the guide has already joined
$guide_id = $_SESSION['user_id'];
$joinedProjectsQuery = "SELECT project_id FROM guide_projects WHERE guide_id = '$guide_id'";
$joinedProjectsResult = $conn->query($joinedProjectsQuery);
$joinedProjects = [];
while ($row = $joinedProjectsResult->fetch_assoc()) {
    $joinedProjects[] = $row['project_id'];
}

$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>View Projects</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <style>
        .card-header h2, .card-header h3 {
            display: inline;
        }
        .card-header h3 {
            float: right;
            font-size: 1.2rem;
            color: #007bff;
        }
    </style>
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
            <h2>Available Projects</h2>
        </div>
        <div class="card-body">
            <table class="table table-striped">
                <thead>
                    <tr>
                        <th>Project Name</th>
                        <th>Semester</th>
                        <th>Course Code</th>
                        <th>Coordinator</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($projects as $project): ?>
                        <tr>
                            <td><?php echo htmlspecialchars($project['name']); ?></td>
                            <td><?php echo htmlspecialchars($project['semester']); ?></td>
                            <td><?php echo htmlspecialchars($project['course_code']); ?></td>
                            <td><?php echo htmlspecialchars($project['coordinator_name']); ?></td>
                            <td>
                                <form method="POST" action="join_projectguide.php">
                                    <input type="hidden" name="project_id" value="<?php echo $project['id']; ?>">
                                    <?php if (in_array($project['id'], $joinedProjects)): ?>
                                        <button type="submit" class="btn btn-success" disabled>Joined</button>
                                    <?php else: ?>
                                        <button type="submit" class="btn btn-success">Join</button>
                                    <?php endif; ?>
                                </form>
                            </td>
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