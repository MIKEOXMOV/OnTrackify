<?php
session_start();
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'coordinator') {
    header("Location: login.html");
    exit();
}

$servername = "localhost";
$username = "root";
$password = "";
$dbname = "role_management";
$coordinator_id = $_SESSION['user_id'];

// Create connection
$conn = new mysqli($servername, $username, $password, $dbname);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Handle create project
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['create_project'])) {
    $project_name = $_POST['project_name'];
    $semester = $_POST['semester'];
    $course_code = $_POST['course_code'];
    $sql = "INSERT INTO projects (name, semester, course_code, coordinator_id) VALUES ('$project_name', '$semester', '$course_code', $coordinator_id)";
    $conn->query($sql);
}

// Handle delete project
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['delete_project'])) {
    $project_id = $_POST['project_id'];
    $sql = "DELETE FROM projects WHERE id = $project_id AND coordinator_id = $coordinator_id";
    $conn->query($sql);
}

// Handle edit project
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['edit_project'])) {
    $project_id = $_POST['project_id'];
    $project_name = $_POST['project_name'];
    $semester = $_POST['semester'];
    $course_code = $_POST['course_code'];
    $sql = "UPDATE projects SET name = '$project_name', semester = '$semester', course_code = '$course_code' WHERE id = $project_id AND coordinator_id = $coordinator_id";
    $conn->query($sql);
}

// Fetch all projects for this coordinator
$sql = "SELECT * FROM projects WHERE coordinator_id = $coordinator_id";
$result = $conn->query($sql);
$projects = $result->fetch_all(MYSQLI_ASSOC);

$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Coordinator Dashboard</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <link rel="stylesheet" href="styles.css"> <!-- Link to your external CSS file -->
</head>
<body>
<nav class="navbar navbar-light bg-light">
    <a class="navbar-brand" href="#">
        <img src="logo.png" width="40" height="40" class="d-inline-block align-top" alt="">
        OnTrackify
    </a>
    <a href="coordinator_panel.php" class="btn btn-primary ml-auto">Back</a>
</nav>

<div class="container mt-5">
    <div class="card mb-4">
        <div class="card-header">
            <h2>Create Project</h2>
        </div>
        <div class="card-body">
            <form method="POST">
                <div class="form-group">
                    <input type="text" name="project_name" class="form-control" placeholder="Project Name" required>
                </div>
                <div class="form-group">
                    <input type="text" name="semester" class="form-control" placeholder="Semester" required>
                </div>
                <div class="form-group">
                    <input type="text" name="course_code" class="form-control" placeholder="Course Code" required>
                </div>
                <button type="submit" name="create_project" class="btn btn-primary">Create Project</button>
            </form>
        </div>
    </div>

    <div class="card">
        <div class="card-header">
            <h2>Existing Projects</h2>
        </div>
        <div class="card-body">
            <table class="table table-striped">
                <thead>
                    <tr>
                        <th>Project Name</th>
                        <th>Semester</th>
                        <th>Course Code</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($projects as $project): ?>
                        <tr>
                            <td><?php echo $project['name']; ?></td>
                            <td><?php echo $project['semester']; ?></td>
                            <td><?php echo $project['course_code']; ?></td>
                            <td>
                                <form method="POST" class="form-inline">
                                    <input type="hidden" name="project_id" value="<?php echo $project['id']; ?>">
                                    <div class="form-group mb-2">
                                        <input type="text" name="project_name" class="form-control" value="<?php echo $project['name']; ?>" required>
                                    </div>
                                    <div class="form-group mx-sm-2 mb-2">
                                        <input type="text" name="semester" class="form-control" value="<?php echo $project['semester']; ?>" required>
                                    </div>
                                    <div class="form-group mx-sm-2 mb-2">
                                        <input type="text" name="course_code" class="form-control" value="<?php echo $project['course_code']; ?>" required>
                                    </div>
                                    <button type="submit" name="edit_project" class="btn btn-warning mb-2">Edit</button>
                                    <button type="submit" name="delete_project" class="btn btn-danger mb-2 ml-2">Delete</button>
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
