<?php
session_start();
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

include 'config.php';

$guide_id = $_SESSION['user_id'];

// Retrieve project_id from URL parameter
if (!isset($_GET['project_id'])) {
    // Handle case when project_id is not provided
    header("Location: guide_panel.php");
    exit();
}
$project_id = mysqli_real_escape_string($conn, $_GET['project_id']); // Sanitize input

// Fetch approved group IDs and their members along with ideas and register_or_faculty_id
$approvedGroupsQuery = "SELECT r.group_id, u.id AS student_id, u.name AS student_name, u.register_or_faculty_id, i.description, i.status, i.id AS idea_id
                        FROM requests r
                        JOIN groups g ON r.group_id = g.group_id
                        JOIN group_members gm ON r.group_id = gm.group_id
                        JOIN users u ON gm.student_id = u.id
                        LEFT JOIN ideas i ON i.student_id = u.id AND i.group_id = r.group_id
                        JOIN guide_projects gp ON r.guide_id = gp.guide_id AND gp.project_id = g.project_id AND gp.project_id = '$project_id'
                        JOIN project_members pm ON pm.project_id = gp.project_id AND pm.student_id = u.id AND pm.project_id = '$project_id'
                        WHERE r.guide_id = '$guide_id' AND r.status = 'approved'";
$approvedGroupsResult = mysqli_query($conn, $approvedGroupsQuery);

$approvedGroups = [];
if ($approvedGroupsResult && mysqli_num_rows($approvedGroupsResult) > 0) {
    while ($row = mysqli_fetch_assoc($approvedGroupsResult)) {
        $group_id = $row['group_id'];
        if (!isset($approvedGroups[$group_id])) {
            $approvedGroups[$group_id] = [
                'students' => []
            ];
        }
        $approvedGroups[$group_id]['students'][] = [
            'student_id' => $row['student_id'],
            'student_name' => $row['student_name'],
            'register_or_faculty_id' => $row['register_or_faculty_id'],
            'description' => $row['description'],
            'status' => $row['status'],
            'idea_id' => $row['idea_id']
        ];
    }
}

// Handle status update form submission
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['status'])) {
    foreach ($_POST['status'] as $ideaId => $status) {
        // Validate status (to prevent SQL injection, you should use prepared statements)
        $validStatuses = ['approved', 'rejected'];
        if (in_array($status, $validStatuses)) {
            $updateQuery = "UPDATE ideas SET status = '$status' WHERE id = '$ideaId'";
            mysqli_query($conn, $updateQuery);
        }
    }
    // Set session variable to indicate success
    $_SESSION['status_updated'] = true;
    // Redirect to avoid resubmission on page refresh
    header("Location: view_ideas.php");
    exit();
}

mysqli_close($conn);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Approved Groups and Ideas</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f9f9f9;
            margin: 0;
            padding: 0;
        }
        .container {
            width: 80%;
            margin: 20px auto;
            padding: 10px;
            background-color: #fff;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
            border-radius: 8px;
        }
        h1 {
            text-align: center;
            color: #333;
            font-size: 1.5em;
            margin-bottom: 10px;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;
        }
        th, td {
            padding: 10px;
            border: 1px solid #ddd;
            text-align: left;
        }
        th {
            background-color: #f2f2f2;
            color: #333;
        }
        .status-select {
            padding: 8px;
            border-radius: 4px;
            border: 1px solid #ddd;
            font-size: 0.9em;
            margin-right: 10px;
        }
        .update-btn {
            background-color: #007bff;
            color: #fff;
            border: none;
            border-radius: 4px;
            padding: 8px 12px;
            cursor: pointer;
            text-decoration: none;
        }
        .update-btn:hover {
            background-color: #0056b3;
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
    <script>
        // JavaScript to show alert if status updated successfully
        document.addEventListener('DOMContentLoaded', function() {
            <?php if (isset($_SESSION['status_updated']) && $_SESSION['status_updated']): ?>
                alert('Status updated successfully.');
            <?php
                // Unset the session variable after displaying the alert
                unset($_SESSION['status_updated']);
            ?>
            <?php endif; ?>
        });
    </script>
</head>
<body>
<div class="header">
        <div class="logo">
            <img src="logo.png" alt="Logo">
            <div class="logo-name">OnTrackify</div>
        </div>
        <a href="guide_projects_idea.php" class="back-button">Back</a>
    </div>
    <div class="container">
        <h1>Groups and Ideas</h1>
        <form action="<?php echo htmlspecialchars($_SERVER['PHP_SELF'] . '?project_id=' . $project_id); ?>" method="post">
            <table>
                <thead>
                    <tr>
                        <th>Group No</th>
                        <th>Register or Faculty ID</th>
                        <th>Students</th>
                        <th>Description</th>
                        <th>Project Idea Status</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($approvedGroups as $group_id => $group): ?>
                        <?php foreach ($group['students'] as $student): ?>
                            <tr>
                                <td><?php echo htmlspecialchars($group_id); ?></td>
                                <td><?php echo htmlspecialchars($student['register_or_faculty_id']); ?></td>
                                <td><?php echo htmlspecialchars($student['student_name']); ?></td>
                                <td><?php echo htmlspecialchars($student['description']); ?></td>
                                <td>
                                    <?php if ($student['idea_id']): ?>
                                        <select name="status[<?php echo $student['idea_id']; ?>]" class="status-select">
                                            <option value="approved" <?php echo ($student['status'] === 'approved') ? 'selected' : ''; ?>>Approved</option>
                                            <option value="rejected" <?php echo ($student['status'] === 'rejected') ? 'selected' : ''; ?>>Rejected</option>
                                        </select>
                                    <?php else: ?>
                                        No idea submitted
                                    <?php endif; ?>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php endforeach; ?>
                </tbody>
            </table>
            <button type="submit" class="update-btn">Update Status</button>
        </form>
    </div>
</body>
</html>
