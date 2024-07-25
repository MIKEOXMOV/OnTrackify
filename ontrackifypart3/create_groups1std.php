<?php
session_start();

// Redirect if user is not logged in as coordinator
if (!isset($_SESSION['user_id']) || $_SESSION['role'] != 'coordinator') {
    header("Location: login.php");
    exit();
}

include 'config.php';

$project_id = $_GET['project_id'];

// Fetch students who have joined the project
$query = "
SELECT u.id, u.name 
FROM users u 
JOIN project_members pm ON u.id = pm.student_id 
WHERE pm.project_id = ? 
  AND u.role = 'student'
  AND u.id NOT IN (
    SELECT gm.student_id
    FROM group_members gm
    JOIN groups g ON gm.group_id = g.group_id
    WHERE g.project_id = ?
  )";
$stmt = $conn->prepare($query);
$stmt->bind_param("ii", $project_id, $project_id);
$stmt->execute();
$result = $stmt->get_result();
$students = $result->fetch_all(MYSQLI_ASSOC);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Create Groups for Project</title>
    <link rel="stylesheet" href="stdstyle.css">
    <style>
        body {
            background-color: #f0f0f0;
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 20px;
            display: flex;
            justify-content: center;
            align-items: center;
            min-height: 100vh;
        }

        .container {
            background-color: #fff;
            border-radius: 8px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
            padding: 20px;
            width: 100%;
            max-width: 600px;
        }

        h1, h2 {
            text-align: center;
            color: #333;
        }

        .student-list {
            margin-top: 20px;
            padding: 20px;
            background-color: #fff;
            border: 1px solid #ccc;
            border-radius: 5px;
        }

        .student-item {
            margin-bottom: 10px;
            padding: 10px;
            border: 1px solid #ccc;
            border-radius: 5px;
            background-color: #f9f9f9;
        }

        .group-form {
            margin-top: 20px;
            padding: 20px;
            background-color: #fff;
            border: 1px solid #ccc;
            border-radius: 5px;
        }

        .group-form label {
            font-weight: bold;
            margin-right: 10px;
        }

        .group-form input[type="number"] {
            padding: 8px;
            border: 1px solid #ccc;
            border-radius: 3px;
            width: 60px;
            margin-right: 10px;
        }

        .group-form button {
            padding: 10px 20px;
            background-color: #007bff;
            color: #fff;
            border: none;
            border-radius: 5px;
            cursor: pointer;
        }

        .group-form button:hover {
            background-color: #0056b3;
        }
    </style>
</head>
<body>
    <div class="container">
        <h1>Create Groups for Project</h1>

        <div class="student-list">
            <h2>Students</h2>
            <?php if (empty($students)): ?>
                <p>No students found.</p>
            <?php else: ?>
                <ol>
                    <?php foreach ($students as $student): ?>
                        <li class="student-item">
                            <?= htmlspecialchars($student['name'], ENT_QUOTES, 'UTF-8') ?>
                        </li>
                    <?php endforeach; ?>
                </ol>
            <?php endif; ?>
        </div>

        <?php if (!empty($students)): ?>
        <div class="group-form">
            <form action="process_create_groups.php" method="POST">
                <input type="hidden" name="project_id" value="<?= htmlspecialchars($project_id, ENT_QUOTES, 'UTF-8') ?>">
                <label for="group_size">Group Size:</label>
                <input type="number" name="group_size" min="1" required>
                <button type="submit">Create Groups</button>
            </form>
        </div>
        <?php endif; ?>
    </div>
</body>
</html>