<?php
session_start();
include 'config.php';

// Ensure only students can access this page
if ($_SESSION['role'] != 'student') {
    header("Location: login.html");
    exit();
}

$student_id = $_SESSION['user_id'];

// Fetch projects enrolled by the student
$projects_query = "SELECT p.id, p.name, p.semester, p.course_code
                   FROM projects p
                   INNER JOIN project_members pm ON p.id = pm.project_id
                   WHERE pm.student_id = ?";
$stmt_projects = $conn->prepare($projects_query);
$stmt_projects->bind_param("i", $student_id);
$stmt_projects->execute();
$projects_result = $stmt_projects->get_result();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Student Panel</title>
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
        .card {
            margin-bottom: 20px;
            width: 300px; /* Adjust card width */
        }
        .card-body {
            padding: 10px;
        }
        .btn-view-results {
            width: 100%;
        }
        
    </style>
</head>
<body>
    <nav class="navbar navbar-light bg-light">
        <a class="navbar-brand" href="#">
            <img src="logo.png" width="40" height="40"  class="d-inline-block align-top" alt="">
            OnTrackify
        </a>
        <a href="student_panel.php" class="btn btn-outline-primary back-button">Back</a>
    </nav>

    <div class="container mt-5">
        <h2>Enrolled Projects</h2>
        <?php while ($project = $projects_result->fetch_assoc()): ?>
            <div class="card">
                <div class="card-body">
                    <h5 class="card-title"><?php echo htmlspecialchars($project['name']); ?></h5>
                    <p class="card-text">Semester: <?php echo htmlspecialchars($project['semester']); ?></p>
                    <p class="card-text">Course Code: <?php echo htmlspecialchars($project['course_code']); ?></p>
                    <a href="view_marks.php?project_id=<?php echo $project['id']; ?>" class="btn btn-primary btn-view-results">View Results</a>
                </div>
            </div>
        <?php endwhile; ?>

        <?php if ($projects_result->num_rows == 0): ?>
            <p>No projects enrolled yet.</p>
        <?php endif; ?>
    </div>

    <script src="https://code.jquery.com/jquery-3.5.1.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.5.3/dist/umd/popper.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
</body>
</html>