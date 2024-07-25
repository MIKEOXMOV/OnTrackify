<?php
session_start();
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

include 'config.php';

// Fetch group_id for the student
$student_id = $_SESSION['user_id'];
$group_id = null; // Initialize $group_id

$groupQuery = "SELECT group_id FROM group_members WHERE student_id = '$student_id'";
$groupResult = mysqli_query($conn, $groupQuery);

if ($groupResult && mysqli_num_rows($groupResult) > 0) {
    $row = mysqli_fetch_assoc($groupResult);
    $group_id = $row['group_id'];
}

// Query to fetch guides from guide_projects and users tables
$query = "SELECT gp.guide_id, u.name FROM guide_projects gp 
          JOIN users u ON gp.guide_id = u.id";
$result = mysqli_query($conn, $query);

$guides = [];
if ($result && mysqli_num_rows($result) > 0) {
    while ($row = mysqli_fetch_assoc($result)) {
        $guides[] = $row;
    }
}

// Fetch the project details
$projectQuery = "SELECT p.id, p.name, p.semester, p.course_code, u.name AS coordinator_name 
                 FROM projects p 
                 JOIN guide_projects gp ON p.id = gp.project_id 
                 JOIN users u ON p.coordinator_id = u.id 
                 WHERE gp.guide_id IN (SELECT guide_id FROM guide_projects)";
$projectResult = mysqli_query($conn, $projectQuery);
$projectDetails = mysqli_fetch_assoc($projectResult);

// Fetch requests status for all students in the group
$statuses = [];
$statusQuery = "SELECT guide_id, group_id, status FROM requests WHERE group_id = '$group_id'";
$statusResult = mysqli_query($conn, $statusQuery);
if ($statusResult && mysqli_num_rows($statusResult) > 0) {
    while ($row = mysqli_fetch_assoc($statusResult)) {
        $statuses[$row['guide_id']][$row['group_id']] = $row['status'];
    }
}

mysqli_close($conn);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Available Guides</title>
    <!-- Include any necessary CSS stylesheets -->
    <link rel="stylesheet" href="styles.css">
    <style>
        .guide-table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
        }
        .guide-table th, .guide-table td {
            border: 1px solid #ccc;
            padding: 8px;
            text-align: left;
        }
        .guide-table th {
            background-color: #f2f2f2;
        }
        .guide-table button {
            padding: 5px 10px;
            background-color: #4CAF50;
            color: white;
            border: none;
            border-radius: 3px;
            cursor: pointer;
        }
        .guide-table button:hover {
            background-color: #45a049;
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

        .card {
            border: 1px solid #ccc;
            border-radius: 5px;
            padding: 20px;
            margin-bottom: 20px;
        }

        .card-header {
            background-color: #f2f2f2;
            padding: 10px;
            margin-bottom: 10px;
            border-radius: 5px 5px 0 0;
        }

        .card-body {
            padding: 10px;
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

    <div class="container">
        <div class="card">
            <div class="card-header">
                <h2>Project Details</h2>
            </div>
            <div class="card-body">
                <p><strong>Project Name:</strong> <?php echo htmlspecialchars($projectDetails['name']); ?></p>
                <p><strong>Semester:</strong> <?php echo htmlspecialchars($projectDetails['semester']); ?></p>
                <p><strong>Course Code:</strong> <?php echo htmlspecialchars($projectDetails['course_code']); ?></p>
                <p><strong>Coordinator:</strong> <?php echo htmlspecialchars($projectDetails['coordinator_name']); ?></p>
            </div>
        </div>

        <h1>Available Guides</h1>

        <div id="guideList">
            <table class="guide-table">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Name</th>
                        <th>Status</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($guides as $guide): ?>
                        <tr>
                            <td><?php echo $guide['guide_id']; ?></td>
                            <td><?php echo htmlspecialchars($guide['name']); ?></td>
                            <td>
                                <?php if (isset($statuses[$guide['guide_id']][$group_id])): ?>
                                    <?php echo ucfirst($statuses[$guide['guide_id']][$group_id]); ?>
                                <?php else: ?>
                                    Not requested
                                <?php endif; ?>
                            </td>
                            <td>
                                <form action="<?php echo $_SERVER['PHP_SELF']; ?>" method="post">
                                    <input type="hidden" name="guide_id" value="<?php echo $guide['guide_id']; ?>">
                                    <?php if (isset($statuses[$guide['guide_id']][$group_id]) && $statuses[$guide['guide_id']][$group_id] == 'pending'): ?>
                                        <button type="button" disabled>Request Pending</button>
                                    <?php elseif (isset($statuses[$guide['guide_id']][$group_id]) && $statuses[$guide['guide_id']][$group_id] == 'approved'): ?>
                                        <button type="button" disabled>Approved</button>
                                    <?php elseif (isset($statuses[$guide['guide_id']][$group_id]) && $statuses[$guide['guide_id']][$group_id] == 'rejected'): ?>
                                        <button type="button" disabled>Rejected</button>
                                    <?php else: ?>
                                        <button type="submit">Request Guide</button>
                                    <?php endif; ?>
                                </form>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>

    <!-- Include any necessary JavaScript files -->
    <script src="script.js"></script>
</body>
</html>