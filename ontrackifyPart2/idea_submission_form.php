<?php
session_start();
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

include 'config.php';

$student_id = $_SESSION['user_id'];
$group_id = null;

$groupQuery = "SELECT group_id FROM group_members WHERE student_id = '$student_id'";
$groupResult = mysqli_query($conn, $groupQuery);

if ($groupResult && mysqli_num_rows($groupResult) > 0) {
    $row = mysqli_fetch_assoc($groupResult);
    $group_id = $row['group_id'];
}

// Fetch students in the same group
$studentsQuery = "SELECT u.id, u.name FROM group_members gm JOIN users u ON gm.student_id = u.id WHERE gm.group_id = '$group_id'";
$studentsResult = mysqli_query($conn, $studentsQuery);

$students = [];
if ($studentsResult && mysqli_num_rows($studentsResult) > 0) {
    while ($row = mysqli_fetch_assoc($studentsResult)) {
        $students[] = $row;
    }
}

// Handle form submission to save ideas or delete ideas
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    if (isset($_POST['ideas'])) {
        foreach ($_POST['ideas'] as $studentId => $idea) {
            $ideaDescription = mysqli_real_escape_string($conn, $idea['description']);

            $checkQuery = "SELECT * FROM ideas WHERE student_id = '$studentId' AND group_id = '$group_id'";
            $checkResult = mysqli_query($conn, $checkQuery);

            if ($checkResult && mysqli_num_rows($checkResult) == 0) {
                $sql = "INSERT INTO ideas (student_id, group_id, description, status) VALUES ('$studentId', '$group_id', '$ideaDescription', 'pending')";
                mysqli_query($conn, $sql);
            } else {
                $updateSql = "UPDATE ideas SET description = '$ideaDescription', status = 'pending' WHERE student_id = '$studentId' AND group_id = '$group_id'";
                mysqli_query($conn, $updateSql);
            }
        }
    }

    // Handle delete idea request
    if (isset($_POST['delete_student_id'])) {
        $delete_student_id = $_POST['delete_student_id'];

        $deleteQuery = "DELETE FROM ideas WHERE student_id = '$delete_student_id' AND group_id = '$group_id'";
        mysqli_query($conn, $deleteQuery);

        // Redirect to avoid resubmission issues
        header("Location: idea_submission_form.php");
        exit();
    }

    // Redirect to same page to show updated data
    header("Location: idea_submission_form.php");
    exit();
}

// Fetch updated ideas list after submission
$ideasQuery = "SELECT i.student_id, i.description, i.status, u.name FROM ideas i JOIN users u ON i.student_id = u.id WHERE i.group_id = '$group_id'";
$ideasResult = mysqli_query($conn, $ideasQuery);

mysqli_close($conn);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Ideas Submission</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f9f9f9;
            margin: 0;
            padding: 0;
        }
        .container {
            width: 90%;
            margin: 20px auto;
            padding: 10px;
            background-color: #fff;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
            border-radius: 8px;
        }
        h1 {
            text-align: center;
            color: #333;
            font-size: 1.2em;
            margin-bottom: 10px;
        }
        .student-idea {
            margin-bottom: 10px;
        }
        .student-idea label {
            font-weight: bold;
            display: block;
            margin-bottom: 5px;
            color: #555;
            font-size: 0.9em;
        }
        .student-idea textarea {
            width: calc(100% - 20px);
            height: 60px;
            padding: 8px;
            border: 1px solid #ddd;
            border-radius: 4px;
            resize: vertical;
            font-family: Arial, sans-serif;
            font-size: 0.9em;
        }
        .student-idea textarea[disabled] {
            background-color: #f2f2f2;
        }
        .submit-btn {
            display: block;
            width: 10%;
            padding: 12px;
            background-color: #007bff;
            color: #fff;
            border: none;
            border-radius: 4px;
            font-size: 1em;
            cursor: pointer;
            margin-top: 20px;
            text-align: center;
        }
        .submit-btn:hover {
            background-color: #0056b3;
        }
        .success-message {
            text-align: center;
            margin: 10px 0;
            color: green;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
        }
        table th, table td {
            border: 1px solid #ddd;
            padding: 8px;
            text-align: left;
        }
        table th {
            background-color: #f2f2f2;
        }
        .status-btn {
            padding: 8px 12px;
            background-color: #007bff;
            color: #fff;
            border: none;
            border-radius: 4px;
            cursor: pointer;
            text-decoration: none;
        }
        .status-btn:hover {
            background-color: #0056b3;
        }
        .delete-btn {
            padding: 8px 12px;
            background-color: #dc3545;
            color: #fff;
            border: none;
            border-radius: 4px;
            cursor: pointer;
        }
        .delete-btn:hover {
            background-color: #c82333;
        }
        .back-button {
            text-decoration: none;
            background-color: #007bff;
            color: #fff;
            padding: 10px 20px;
            border-radius: 5px;
            border: none;
            cursor: pointer;
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
    </style>
</head>
<body>
<div class="header">
        <div class="logo">
            <img src="logo.png" alt="Logo">
            <div class="logo-name">OnTrackify</div>
        </div>
        <a href="group_panel.php" class="back-button">Back</a>
    </div>
    </style>
</head>
<body>
    <div class="container">
        <h1>Ideas Submission</h1>
        <?php if (isset($successMessage)): ?>
            <p class="success-message"><?php echo $successMessage; ?></p>
        <?php endif; ?>
        <form action="<?php echo htmlspecialchars($_SERVER['PHP_SELF']); ?>" method="post">
            <?php foreach ($students as $student): ?>
                <div class="student-idea">
                    <label for="idea_<?php echo $student['id']; ?>"><?php echo htmlspecialchars($student['name']); ?>'s Idea</label>
                    <textarea name="ideas[<?php echo $student['id']; ?>][description]" id="idea_<?php echo $student['id']; ?>" <?php echo ($student['id'] != $student_id) ? 'disabled' : 'required'; ?>></textarea>
                </div>
            <?php endforeach; ?>
            <button type="submit" class="submit-btn">Submit All Ideas</button>
        </form>

        <?php if (isset($ideasResult) && mysqli_num_rows($ideasResult) > 0): ?>
            <h2>Submitted Ideas</h2>
            <table>
                <thead>
                    <tr>
                        <th>Student Name</th>
                        <th>Idea Description</th>
                        <th>Status</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody>
                    <?php while ($idea = mysqli_fetch_assoc($ideasResult)): ?>
                        <tr>
                            <td><?php echo htmlspecialchars($idea['name']); ?></td>
                            <td><?php echo htmlspecialchars($idea['description']); ?></td>
                            <td>
                                <?php if ($idea['status'] === 'pending'): ?>
                                    <a href="#" class="status-btn">Pending</a>
                                <?php elseif ($idea['status'] === 'approved'): ?>
                                    <a href="#" class="status-btn" style="background-color: green;">Approved</a>
                                <?php else: ?>
                                    <a href="#" class="status-btn" style="background-color: red;">Rejected</a>
                                <?php endif; ?>
                            </td>
                            <td>
                                <?php if ($idea['student_id'] == $student_id): ?>
                                    <form action="<?php echo htmlspecialchars($_SERVER['PHP_SELF']); ?>" method="post">
                                        <input type="hidden" name="delete_student_id" value="<?php echo htmlspecialchars($idea['student_id']); ?>">
                                        <button type="submit" class="delete-btn">Delete</button>
                                    </form>
                                <?php endif; ?>
                            </td>
                        </tr>
                    <?php endwhile; ?>
                </tbody>
            </table>
        <?php endif; ?>
    </div>
</body>
</html>
