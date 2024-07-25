<?php
session_start();

// Ensure only coordinators can access this page
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'coordinator') {
    header("Location: login.php");
    exit();
}

include 'config.php';

// Retrieve project_id from GET parameters
if (isset($_GET['project_id'])) {
    $project_id = $_GET['project_id'];
} else {
    // Redirect to an appropriate page or handle the error scenario
    header("Location: coordinator_panel.php");
    exit();
}

// Fetch project details
$project_query = "SELECT * FROM projects WHERE id = ?";
$stmt_project = $conn->prepare($project_query);
$stmt_project->bind_param("i", $project_id);
$stmt_project->execute();
$project_result = $stmt_project->get_result();
$project = $project_result->fetch_assoc();

// Fetch students associated with the project from project_members table
$students_query = "SELECT u.id, u.name, u.email, u.register_or_faculty_id
                   FROM project_members pm
                   INNER JOIN users u ON pm.student_id = u.id
                   WHERE pm.project_id = ?";
$stmt_students = $conn->prepare($students_query);
$stmt_students->bind_param("i", $project_id);
$stmt_students->execute();
$students_result = $stmt_students->get_result();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Evaluate Project: <?php echo htmlspecialchars($project['name']); ?></title>
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
        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
        }
        th, td {
            padding: 10px;
            text-align: left;
            border: 1px solid #ccc;
        }
        th {
            background-color: lightblue;
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
        <div class="card">
            <div class="card-body">
                <h4>Evaluate Project: <?php echo htmlspecialchars($project['name']); ?></h4>
                <h5>Semester: <?php echo htmlspecialchars($project['semester']); ?></h5>
                <h5>Course Code: <?php echo htmlspecialchars($project['course_code']); ?></h5>
            </div>
        </div>

        <h2>Students in this Project</h2>
        <?php if ($students_result->num_rows > 0): ?>
            <table>
                <thead>
                    <tr>
                        <th>Sl. No</th>
                        <th>Name</th>
                        <th>Email</th>
                        <th>Register ID</th>
                        <th>Evaluate</th>
                    </tr>
                </thead>
                <tbody>
                    <?php $serial_number = 1; ?>
                    <?php while ($student = $students_result->fetch_assoc()): ?>
                        <tr>
                            <td><?php echo $serial_number++; ?></td>
                            <td><?php echo htmlspecialchars($student['name']); ?></td>
                            <td><?php echo htmlspecialchars($student['email']); ?></td>
                            <td><?php echo htmlspecialchars($student['register_or_faculty_id']); ?></td>
                            <td>
                                <a href="evaluate_student.php?student_id=<?php echo $student['id']; ?>&project_id=<?php echo $project_id; ?>" class="btn btn-primary">Evaluate</a>
                            </td>
                        </tr>
                    <?php endwhile; ?>
                </tbody>
            </table>
        <?php else: ?>
            <p>No students have joined this project yet.</p>
        <?php endif; ?>
    </div>

    <script src="https://code.jquery.com/jquery-3.5.1.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.5.3/dist/umd/popper.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
</body>
</html>