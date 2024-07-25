<?php
session_start();
include 'config.php';

// Ensure only students can access this page
if ($_SESSION['role'] != 'student') {
    header("Location: login.html");
    exit();
}

// Check if project_id is received via POST
if (isset($_POST['project_id'])) {
    $project_id = $_POST['project_id'];
    $student_id = $_SESSION['user_id'];

    // Check if the student is already a member of the project
    $check_query = "SELECT * FROM project_members WHERE student_id = ? AND project_id = ?";
    $stmt_check = $conn->prepare($check_query);
    $stmt_check->bind_param("ii", $student_id, $project_id);
    $stmt_check->execute();
    $check_result = $stmt_check->get_result();

    if ($check_result->num_rows > 0) {
        // Student is already a member of this project
        $project_details_query = "SELECT * FROM projects WHERE id = ?";
        $stmt_project_details = $conn->prepare($project_details_query);
        $stmt_project_details->bind_param("i", $project_id);
        $stmt_project_details->execute();
        $project_details_result = $stmt_project_details->get_result();

        if ($project_details_result->num_rows > 0) {
            $project = $project_details_result->fetch_assoc();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Project Details</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <style>
        .navbar-brand {
            display: flex;
            align-items: center;
        }
        .navbar-brand img {
            margin-right: 10px;
            width: 40px; /* Adjust logo size */
            height: 40px;
        }
        .back-button {
            position: absolute;
            top: 10px;
            right: 10px;
        }
        .card {
            margin-top: 20px;
            width: 400px; /* Adjust card width */
            text-align: center;
            border: 1px solid #ccc;
            border-radius: 8px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
        }
        .card-body {
            padding: 20px;
        }
    </style>
</head>
<body>
    <nav class="navbar navbar-light bg-light">
        <a class="navbar-brand" href="#">
            <img src="logo.png" class="d-inline-block align-top" alt="">
            OnTrackify
        </a>
        <a href="student_panel.php" class="btn btn-outline-primary back-button" onclick="history.back();">Back</a>
    </nav>

    <div class="container mt-5">
        <div class="card mx-auto">
            <div class="card-body">
                <h5 class="card-title">Project Details</h5>
                <p><strong>Name:</strong> <?php echo htmlspecialchars($project['name']); ?></p>
                <p><strong>Semester:</strong> <?php echo htmlspecialchars($project['semester']); ?></p>
                <p><strong>Course Code:</strong> <?php echo htmlspecialchars($project['course_code']); ?></p>
                <!-- Add more project details here -->
                <p>You are already a member of this project.</p>
            </div>
        </div>
    </div>

    <script src="https://code.jquery.com/jquery-3.5.1.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.5.3/dist/umd/popper.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
</body>
</html>
<?php
        } else {
            echo "Project details not found.";
        }
    } else {
        // Insert into project_members table
        $insert_query = "INSERT INTO project_members (student_id, project_id) VALUES (?, ?)";
        $stmt_insert = $conn->prepare($insert_query);
        $stmt_insert->bind_param("ii", $student_id, $project_id);

        if ($stmt_insert->execute()) {
            // Fetch project details for display
            $project_details_query = "SELECT * FROM projects WHERE id = ?";
            $stmt_project_details = $conn->prepare($project_details_query);
            $stmt_project_details->bind_param("i", $project_id);
            $stmt_project_details->execute();
            $project_details_result = $stmt_project_details->get_result();

            if ($project_details_result->num_rows > 0) {
                $project = $project_details_result->fetch_assoc();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Project Details</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <style>
        .navbar-brand {
            display: flex;
            align-items: center;
        }
        .navbar-brand img {
            margin-right: 10px;
            width: 40px; /* Adjust logo size */
            height: 40px;
        }
        .back-button {
            position: absolute;
            top: 10px;
            right: 10px;
        }
        .card {
            margin-top: 20px;
            width: 400px; /* Adjust card width */
            text-align: center;
            border: 1px solid #ccc;
            border-radius: 8px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
        }
        .card-body {
            padding: 20px;
        }
    </style>
</head>
<body>
    <nav class="navbar navbar-light bg-light">
        <a class="navbar-brand" href="#">
            <img src="logo.png" class="d-inline-block align-top" alt="">
            OnTrackify
        </a>
        <a href="#" class="btn btn-outline-primary back-button" onclick="history.back();">Back</a>
    </nav>

    <div class="container mt-5">
        <div class="card mx-auto">
            <div class="card-body">
                <h5 class="card-title">Project Details</h5>
                <p><strong>Name:</strong> <?php echo htmlspecialchars($project['name']); ?></p>
                <p><strong>Semester:</strong> <?php echo htmlspecialchars($project['semester']); ?></p>
                <p><strong>Course Code:</strong> <?php echo htmlspecialchars($project['course_code']); ?></p>
                <!-- Add more project details here -->
                <p>You have successfully joined the project.</p>
            </div>
        </div>
    </div>

    <script src="https://code.jquery.com/jquery-3.5.1.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.5.3/dist/umd/popper.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
</body>
</html>
<?php
            } else {
                echo "Project details not found.";
            }
        } else {
            echo "Error joining the project: " . $stmt_insert->error;
        }
    }
} else {
    echo "Project ID not provided.";
}

$conn->close();
?>