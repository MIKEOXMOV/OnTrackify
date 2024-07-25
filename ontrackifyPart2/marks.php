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
        .card {
            margin-bottom: 20px;
            background-color: #cce5ff; /* Light blue shade */
            border: 1px solid #ddd; /* Optional border */
            border-radius: 5px; /* Optional rounded corners */
            box-shadow: 0 4px 8px rgba(0,0,0,0.1); /* Optional box shadow */
        }
        .card-body {
            padding: 20px;
        }
        .btn-primary {
            background-color: #ffffff; /* White background */
            color: #007bff; /* Blue text color */
            border-color: #007bff; /* Blue border color */
        }
        .btn-primary:hover {
            background-color: #007bff; /* Blue background on hover */
            color: #ffffff; /* White text color on hover */
            border-color: #007bff; /* Blue border color on hover */
        }
    </style>
</head>
<body>
    <nav class="navbar navbar-light bg-light">
        <a class="navbar-brand" href="#">
            <img src="logo.png" width="40" height="40" class="d-inline-block align-top" alt="">
            OnTrackify
        </a>
        <a href="coordinator_panel.php" class="btn btn-outline-primary back-button">Back</a>
    </nav>

    <div class="container mt-5">
        <h3 class="mb-4">View Students' Marks</h3>
        <div class="row">
            <?php while ($project = $projects_result->fetch_assoc()): ?>
                <div class="col-md-6">
                    <div class="card">
                        <div class="card-body">
                            <h3><?php echo htmlspecialchars($project['name']); ?> (<?php echo htmlspecialchars($project['semester']); ?>)</h3>
                            <p>Course Code: <?php echo htmlspecialchars($project['course_code']); ?></p>
                            <a href="display_marks.php?project_id=<?php echo $project['id']; ?>" class="btn btn-primary">View Students' Marks</a>
                        </div>
                    </div>
                </div>
            <?php endwhile; ?>
        </div>
    </div>

    <script src="https://code.jquery.com/jquery-3.5.1.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.5.3/dist/umd/popper.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
</body>
</html>