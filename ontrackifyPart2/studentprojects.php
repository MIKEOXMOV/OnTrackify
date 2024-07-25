<?php
session_start();
include 'config.php';

// Ensure only students can access this page
if ($_SESSION['role'] != 'student') {
    header("Location: login.html");
    exit();
}


// Fetch distinct semesters available for projects
$semester_query = "SELECT DISTINCT semester FROM projects";
$semester_result = $conn->query($semester_query);

$selected_semester = '';
$projects = [];

// Fetch projects based on selected semester
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $selected_semester = $_POST['semester'];
    $project_query = "SELECT * FROM projects WHERE semester = '$selected_semester'";
    $project_result = $conn->query($project_query);

    while ($project = $project_result->fetch_assoc()) {
        // Check if the student is already a member of the project
        $student_id = $_SESSION['user_id'];
        $project_id = $project['id'];

        $check_query = "SELECT * FROM project_members WHERE student_id = ? AND project_id = ?";
        $stmt_check = $conn->prepare($check_query);
        $stmt_check->bind_param("ii", $student_id, $project_id);
        $stmt_check->execute();
        $check_result = $stmt_check->get_result();

        if ($check_result->num_rows > 0) {
            // Student is already a member of this project
            $project['joined'] = true;
        } else {
            $project['joined'] = false;
        }

        $projects[] = $project;
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Select Semester and View Projects</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f0f0f0; /* Light grey background */
            color: #333333; /* Dark grey text */
            padding: 20px;
        }
        h1 {
            margin-bottom: 20px;
        }
        form {
            margin-bottom: 20px;
        }
        select {
            padding: 8px;
            font-size: 16px;
        }
        button {
            padding: 8px 15px;
            font-size: 16px;
            background-color: #2196F3; /* Blue button */
            color: white;
            border: none;
            cursor: pointer;
        }
        button:hover {
            background-color: #0e81d1; /* Darker blue on hover */
        }
        .project-card {
            border: 1px solid #ccc;
            background-color: #ffffff; /* White background for projects */
            padding: 10px;
            margin-bottom: 10px;
            width: 300px; /* Adjust card width as needed */
            box-shadow: 0 4px 8px rgba(0,0,0,0.1); /* Add box shadow for depth */
            transition: box-shadow 0.3s ease-in-out; /* Smooth transition on hover */
            position: relative; /* Position relative for button positioning */
        }
        .project-card:hover {
            box-shadow: 0 8px 16px rgba(0,0,0,0.2); /* Increase shadow on hover */
        }
        .project-card h3 {
            margin-top: 0;
            font-size: 18px;
        }
        .project-card p {
            margin-bottom: 10px;
            font-size: 14px;
            color: #666666;
        }
        .logo-container {
            display: flex;
            align-items: center;
            justify-content: space-between;
            margin-bottom: 20px;
        }
        .logo {
            width: 80px; /* Adjust size as needed */
            margin-right: 10px;
        }
        .logo-name {
            font-size: 24px;
            font-weight: bold;
        }
        .back-button {
            padding: 10px 20px;
            background-color: #2196F3; /* Blue button */
            color: white;
            border: none;
            cursor: pointer;
            text-decoration: none;
        }
        .back-button:hover {
            background-color: #0e81d1; /* Darker blue on hover */
        }
        .joined-button {
            position: absolute;
            top: 10px;
            right: 10px;
        }
        .joined-button button {
            background-color: #4CAF50; /* Green button */
        }
        .joined-button button:hover {
            background-color: #45a049; /* Darker green on hover */
        }
    </style>
</head>
<body>
    <!-- Logo and name -->
    <div class="logo-container">
        <div>
            <img src="logo.png" alt="Logo" class="logo">
            <span class="logo-name">OnTrackify</span>
        </div>
        <a href="student_panel.php" class="back-button">Back</a>
    </div>

    <h1>Select Semester</h1>
    <form method="POST" action="">
        <label for="semester">Semester:</label>
        <select name="semester" id="semester" required>
            <option value="">Select Semester</option>
            <?php while ($row = $semester_result->fetch_assoc()): ?>
                <option value="<?php echo htmlspecialchars($row['semester']); ?>" <?php if ($row['semester'] == $selected_semester) echo 'selected'; ?>>
                    <?php echo htmlspecialchars($row['semester']); ?>
                </option>
            <?php endwhile; ?>
        </select>
        <button type="submit">Show Projects</button>
    </form>

    <?php if (!empty($projects)): ?>
        <h2>Projects for Semester: <?php echo htmlspecialchars($selected_semester); ?></h2>
        <?php foreach ($projects as $project): ?>
            <div class="project-card">
                <h3><?php echo htmlspecialchars($project['name']); ?></h3>
                <p>Course Code: <?php echo htmlspecialchars($project['course_code']); ?></p>
                <?php if ($project['joined']): ?>
                    <div class="joined-button">
                        <button type="button">Joined</button>
                    </div>
                <?php else: ?>
                    <form action="join_project.php" method="POST">
                        <input type="hidden" name="project_id" value="<?php echo $project['id']; ?>">
                        <button type="submit">Join Project</button>
                    </form>
                <?php endif; ?>
            </div>
        <?php endforeach; ?>
    <?php elseif ($_SERVER['REQUEST_METHOD'] == 'POST'): ?>
        <p>No projects available for the selected semester.</p>
    <?php endif; ?>
</body>
</html>