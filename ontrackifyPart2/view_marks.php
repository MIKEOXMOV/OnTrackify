<?php
session_start();
include 'config.php';

// Ensure only students can access this page
if ($_SESSION['role'] != 'student') {
    header("Location: login.php");
    exit();
}

$student_id = $_SESSION['user_id'];

// Fetch student's marks data including group_id from student_marks table
$marks_query = "SELECT sm.attendance, gm.marks AS guide_marks, sm.project_report, sm.review1_total_marks, sm.review2_total_marks, sm.final_cie_mark,
                       u.name AS student_name, u.register_or_faculty_id, sm.group_id
               FROM student_marks sm
               INNER JOIN users u ON sm.student_id = u.id
               LEFT JOIN guide_marks gm ON sm.student_id = gm.student_id AND sm.project_id = gm.project_id
               WHERE sm.student_id = ?";
$stmt = $conn->prepare($marks_query);
$stmt->bind_param("i", $student_id);
$stmt->execute();
$result = $stmt->get_result();

// Check if student has marks
if ($result->num_rows > 0) {
    $marks = $result->fetch_assoc();
} else {
    // Handle scenario where no marks are found
    $marks = null;
}

$stmt->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>View My Marks</title>
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
        .table {
            margin-top: 20px;
        }
    </style>
</head>
<body>
    <nav class="navbar navbar-light bg-light">
        <a class="navbar-brand" href="#">
            <img src="logo.png" width="40" height="40" class="d-inline-block align-top" alt="">
            OnTrackify
        </a>
        <a href="view_enrolled_projects.php" class="btn btn-outline-primary back-button">Back</a>
    </nav>

    <div class="container mt-5">
        <h2>My Marks</h2>
        <?php if ($marks): ?>
            <div class="card">
                <div class="card-body">
                    <h5 class="card-title">Student Details</h5>
                    <p><strong>Name:</strong> <?php echo htmlspecialchars($marks['student_name']); ?></p>
                    <p><strong>Register ID:</strong> <?php echo htmlspecialchars($marks['register_or_faculty_id']); ?></p>
                    <p><strong>Group ID:</strong> <?php echo isset($marks['group_id']) ? htmlspecialchars($marks['group_id']) : 'Not Assigned'; ?></p>
                </div>
            </div>

            <table class="table table-bordered mt-4">
                <thead>
                    <tr>
                        <th>Marks Category</th>
                        <th>Marks Obtained</th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td>Attendance (out of 10)</td>
                        <td><?php echo htmlspecialchars($marks['attendance']); ?></td>
                    </tr>
                    <tr>
                        <td>Guide Marks (out of 15)</td>
                        <td><?php echo htmlspecialchars($marks['guide_marks']); ?></td>
                    </tr>
                    <tr>
                        <td>Project Report (out of 10)</td>
                        <td><?php echo htmlspecialchars($marks['project_report']); ?></td>
                    </tr>
                    <tr>
                        <td>Review 1 Total Marks (out of 40)</td>
                        <td><?php echo htmlspecialchars($marks['review1_total_marks']); ?></td>
                    </tr>
                    <tr>
                        <td>Review 2 Total Marks (out of 40)</td>
                        <td><?php echo htmlspecialchars($marks['review2_total_marks']); ?></td>
                    </tr>
                    <tr>
                        <td>Total CIE Marks (out of 75)</td>
                        <td><?php echo htmlspecialchars($marks['final_cie_mark']); ?></td>
                    </tr>
                </tbody>
            </table>
        <?php else: ?>
            <p>No marks available.</p>
        <?php endif; ?>
    </div>

    <script src="https://code.jquery.com/jquery-3.5.1.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.5.3/dist/umd/popper.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
</body>
</html>