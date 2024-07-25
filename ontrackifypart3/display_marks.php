<?php
session_start();
include 'config.php';

// Ensure only coordinators can access this page
if ($_SESSION['role'] != 'coordinator') {
    header("Location: login.html");
    exit();
}

$coordinator_id = $_SESSION['user_id'];

// Ensure project_id is set and valid
if (!isset($_GET['project_id']) || !is_numeric($_GET['project_id'])) {
    header("Location: coordinator_panel.php");
    exit();
}

$project_id = $_GET['project_id'];

// Fetch project details to display on the page
$project_query = "SELECT * FROM projects WHERE id = $project_id AND coordinator_id = $coordinator_id";
$project_result = $conn->query($project_query);

if ($project_result->num_rows == 0) {
    // Redirect if project not found or not authorized
    header("Location: coordinator_panel.php");
    exit();
}

// Fetch students' marks data specific to the selected project including group_id from group_members
$marks_query = "SELECT u.name, u.register_or_faculty_id, gm.group_id, sm.attendance, gmarks.marks as guide_marks, sm.project_report, sm.review1_total_marks, sm.review2_total_marks, sm.final_cie_mark
                FROM users u
                LEFT JOIN (
                    SELECT gm.student_id, gm.group_id
                    FROM group_members gm
                    WHERE gm.group_id IN (
                        SELECT g.group_id
                        FROM groups g
                        WHERE g.project_id = $project_id
                    )
                ) gm ON u.id = gm.student_id
                LEFT JOIN student_marks sm ON u.id = sm.student_id AND sm.project_id = $project_id
                LEFT JOIN guide_marks gmarks ON u.id = gmarks.student_id AND gmarks.project_id = $project_id
                WHERE u.role = 'student'
                ORDER BY u.register_or_faculty_id ASC"; // Order by register_or_faculty_id ascending
$result = $conn->query($marks_query);

// Fetch project details for displaying project name and details
$project_details = $project_result->fetch_assoc();

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Students' Marks for <?php echo htmlspecialchars($project_details['name']); ?></title>
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
            margin-top: 20px;
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
        <a href="coordinator_panel.php" class="btn btn-outline-primary back-button">Back</a>
    </nav>

    <div class="container mt-5">
        <div class="card">
            <div class="card-body">
                <h2 class="card-title">Project Details</h2>
                <h5>Project Name: <?php echo htmlspecialchars($project_details['name']); ?></h5>
                <p>Course Code: <?php echo htmlspecialchars($project_details['course_code']); ?></p>

            </div>
        </div>

        <h2 class="mt-5">Students' Marks for <?php echo htmlspecialchars($project_details['name']); ?></h2>
        <table class="table table-bordered">
            <thead>
                <tr>
                    <th>Serial No.</th>
                    <th>Name</th>
                    <th>Register ID / Faculty ID</th>
                    <th>Group ID</th>
                    <th>Attendance (out of 10)</th>
                    <th>Marks (from guide_marks table)</th>
                    <th>Project Report (out of 10)</th>
                    <th>Review 1 Total Marks (out of 40)</th>
                    <th>Review 2 Total Marks (out of 40)</th>
                    <th>Total CIE Marks (out of 75)</th>
                </tr>
            </thead>
            <tbody>
                <?php
                $serial = 1;
                while ($row = $result->fetch_assoc()) {
                    echo "<tr>";
                    echo "<td>" . $serial++ . "</td>";
                    echo "<td>" . htmlspecialchars($row['name']) . "</td>";
                    echo "<td>" . htmlspecialchars($row['register_or_faculty_id']) . "</td>";
                    echo "<td>" . (isset($row['group_id']) ? htmlspecialchars($row['group_id']) : 'Not Assigned') . "</td>";
                    echo "<td>" . htmlspecialchars($row['attendance']) . "</td>";
                    echo "<td>" . htmlspecialchars($row['guide_marks']) . "</td>"; // Displaying guide marks from guide_marks table
                    echo "<td>" . htmlspecialchars($row['project_report']) . "</td>";
                    echo "<td>" . htmlspecialchars($row['review1_total_marks']) . "</td>";
                    echo "<td>" . htmlspecialchars($row['review2_total_marks']) . "</td>";
                    echo "<td>" . htmlspecialchars($row['final_cie_mark']) . "</td>";
                    echo "</tr>";
                }
                ?>
            </tbody>
        </table>
    </div>

    <script src="https://code.jquery.com/jquery-3.5.1.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.5.3/dist/umd/popper.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
</body>
</html>