<?php
session_start();
include 'config.php';

// Ensure only coordinators can access this page
if ($_SESSION['role'] != 'coordinator') {
    header("Location: login.html");
    exit();
}

$project_id = $_GET['project_id'];

// Fetch project details
$project_query = "SELECT * FROM projects WHERE id = $project_id";
$project_result = $conn->query($project_query);
$project = $project_result->fetch_assoc();

// Fetch students associated with the project, including register ID
$students_query = "SELECT users.name, users.email, users.register_or_faculty_id 
                   FROM users 
                   INNER JOIN project_members ON users.id = project_members.student_id 
                   WHERE project_members.project_id = $project_id";
$students_result = $conn->query($students_query);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Project Students</title>
    <style>
        .header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            background-color: #fff;
            padding: 10px 20px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
        }
        body {
            font-family: Arial, sans-serif;
            line-height: 1.6;
            margin: 20px;
            padding: 20px;
        }
        h1, h2, h3 {
            margin-bottom: 10px;
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
            background-color: #f2f2f2;
        }
        .header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            background-color: #fff;
            padding: 10px 20px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
        }
        .logo {
            display: flex;
            align-items: center;
        }

        .logo img {
            height: 40px;
            margin-right: 10px;
        }

        .logo-name {
            font-size: 24px;
            font-weight: bold;
            color: #333;
        }
        .back-button {
            float: right;
        }
        .back-button {
            display: inline-block;
            padding: 10px 20px;
            background-color: #007bff;
            color: #fff;
            text-decoration: none;
            border-radius: 5px;
            margin-top: -40px;
            transition: background-color 0.3s ease;
        }

        .back-button:hover {
            background-color: #0056b3;
        }
    </style>
</head>
<body>
<div class="header">
        <div class="logo">
            <img src="logo.png" alt="Logo">
            <div class="logo-name">OnTrackify</div>
        </div>
        <a href="view projects.php" class="back-button">Back</a>
    </div>
   

    <h1>Project: <?php echo htmlspecialchars($project['name']); ?></h1>
    <h2>Semester: <?php echo htmlspecialchars($project['semester']); ?></h2>
    <h3>Course Code: <?php echo htmlspecialchars($project['course_code']); ?></h3>

    <h2>Students in this Project</h2>
    <?php if ($students_result->num_rows > 0): ?>
        <table>
            <thead>
                <tr>
                    <th>Sl. No</th>
                    <th>Name</th>
                    <th>Email</th>
                    <th>Register ID</th>
                </tr>
            </thead>
            <tbody>
                <?php 
                $serial_number = 1;
                while ($student = $students_result->fetch_assoc()): 
                ?>
                    <tr>
                        <td><?php echo $serial_number++; ?></td>
                        <td><?php echo htmlspecialchars($student['name']); ?></td>
                        <td><?php echo htmlspecialchars($student['email']); ?></td>
                        <td><?php echo htmlspecialchars($student['register_or_faculty_id']); ?></td>
                    </tr>
                <?php endwhile; ?>
            </tbody>
        </table>
    <?php else: ?>
        <p>No students have joined this project yet.</p>
    <?php endif; ?>
</body>
</html>