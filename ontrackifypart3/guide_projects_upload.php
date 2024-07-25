<?php
session_start();
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'guide') {
    header("Location: login.php");
    exit();
}

include 'config.php';

$guide_id = $_SESSION['user_id'];

// Fetch projects that the guide has already joined
$sql = "SELECT p.id, p.name, p.semester, p.course_code, u.name AS coordinator_name
        FROM projects p
        JOIN users u ON p.coordinator_id = u.id
        JOIN guide_projects gp ON p.id = gp.project_id
        WHERE gp.guide_id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param('i', $guide_id);
$stmt->execute();
$result = $stmt->get_result();
$projects = $result->fetch_all(MYSQLI_ASSOC);
$stmt->close();

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
    <a href="guide_panel.php" class="btn btn-primary ml-auto">Back</a>
</nav>

<div class="container mt-5">
    <h2>View Files of Groups</h2> <!-- New heading -->
    <div class="row">
        <?php foreach ($projects as $project): ?>
            <div class="col-md-6">
                <div class="card">
                    <div class="card-body">
                        <h3><?php echo htmlspecialchars($project['name']); ?> (<?php echo htmlspecialchars($project['semester']); ?>)</h3>
                        <p>Course Code: <?php echo htmlspecialchars($project['course_code']); ?></p>
                        <p>Coordinator: <?php echo htmlspecialchars($project['coordinator_name']); ?></p>
                        <a href="fetch_from_guide.php?project_id=<?php echo $project['id']; ?>" class="btn btn-primary mt-2">View Groups</a>
                    </div>
                </div>
            </div>
        <?php endforeach; ?>
    </div>
</div>

<script src="https://code.jquery.com/jquery-3.5.1.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.5.3/dist/umd/popper.min.js"></script>
<script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
</body>
</html>
