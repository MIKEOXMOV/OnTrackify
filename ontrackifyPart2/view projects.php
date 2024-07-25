<?php
session_start();
include 'config.php';

// Ensure only coordinators can access this page
if ($_SESSION['role'] != 'coordinator') {
    header("Location: login.html");
    exit();
}

$coordinator_id = $_SESSION['user_id'];

// Fetch projects created by the logged-in coordinator
$projects_query = "SELECT * FROM projects WHERE coordinator_id = $coordinator_id";
$projects_result = $conn->query($projects_query);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Coordinator Dashboard</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <style>
        .navbar-brand {
            display: flex;
            align-items: center;
        }
        .navbar-brand img {
            margin-right: 10px;
        }
        .back-button {
            position: absolute;
            top: 10px;
            right: 10px;
        }
    </style>
</head>
<body>
    <nav class="navbar navbar-light bg-light">
        <a class="navbar-brand" href="coordinator_panel.php">
            <img src="logo.png" width="40" height="40" class="d-inline-block align-top" alt="">
            OnTrackify
        </a>
        <a href="coordinator_panel.php" class="btn btn-outline-primary back-button">Back</a>
    </nav>

    <div class="container mt-5">
        <h1 class="mb-4">Coordinator Dashboard</h1>
        <h2 class="mb-3">Your Projects</h2>
        <ul class="list-group">
            <?php while ($project = $projects_result->fetch_assoc()): ?>
                <li class="list-group-item">
                    <h3><?php echo htmlspecialchars($project['name']); ?> (<?php echo htmlspecialchars($project['semester']); ?>)</h3>
                    <p>Course Code: <?php echo htmlspecialchars($project['course_code']); ?></p>
                    <form action="view_project_members.php" method="GET" class="mt-2">
                        <input type="hidden" name="project_id" value="<?php echo $project['id']; ?>">
                        <button type="submit" class="btn btn-primary">View Students</button>
                    </form>
                </li>
            <?php endwhile; ?>
        </ul>
    </div>

    <script src="https://code.jquery.com/jquery-3.5.1.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.5.3/dist/umd/popper.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
</body>
</html>
