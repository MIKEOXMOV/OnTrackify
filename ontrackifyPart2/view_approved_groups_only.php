<?php
session_start();
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

include 'config.php';

$guide_id = $_SESSION['user_id'];
$project_id = $_GET['project_id']; // Retrieve project_id from URL parameter

// Fetch approved group IDs and their members with registration IDs for the specific project
$approvedGroupsQuery = "SELECT DISTINCT r.group_id, p.name AS project_name, u.name AS student_name, u.register_or_faculty_id
                        FROM requests r
                        JOIN groups g ON r.group_id = g.group_id
                        JOIN group_members gm ON r.group_id = gm.group_id
                        JOIN users u ON gm.student_id = u.id
                        JOIN guide_projects gp ON r.guide_id = gp.guide_id AND gp.project_id = '$project_id'
                        JOIN projects p ON gp.project_id = p.id
                        JOIN project_members pm ON p.id = pm.project_id AND pm.project_id = '$project_id'
                        WHERE r.guide_id = '$guide_id' AND r.status = 'approved' AND p.id = '$project_id'";
$approvedGroupsResult = mysqli_query($conn, $approvedGroupsQuery);

$approvedGroups = [];
if ($approvedGroupsResult && mysqli_num_rows($approvedGroupsResult) > 0) {
    while ($row = mysqli_fetch_assoc($approvedGroupsResult)) {
        $group_id = $row['group_id'];
        $project_name = $row['project_name'];
        $student_name = $row['student_name'];
        $register_id = $row['register_or_faculty_id'];

        if (!isset($approvedGroups[$group_id])) {
            $approvedGroups[$group_id] = [
                'project_name' => $project_name,
                'students' => []
            ];
        }
        // Store student name and register ID in the group array
        $approvedGroups[$group_id]['students'][] = [
            'name' => $student_name,
            'register_id' => $register_id
        ];
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
    <title>Approved Groups</title>
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
        .card {
            border: 1px solid #ddd;
            padding: 10px;
            margin-bottom: 20px;
            border-radius: 8px;
        }
        .card-header {
            font-size: 1.2em;
            font-weight: bold;
            margin-bottom: 10px;
            background-color: #f2f2f2;
            padding: 10px;
            border-top-left-radius: 8px;
            border-top-right-radius: 8px;
        }
        .card-content {
            padding: 10px;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 10px;
        }
        th, td {
            border: 1px solid #ddd;
            padding: 8px;
            text-align: left;
        }
        th {
            background-color: #f2f2f2;
        }
        .back-button {
            text-decoration: none;
            background-color: #007bff;
            color: #fff;
            padding: 10px 20px;
            border-radius: 5px;
            border: none;
            cursor: pointer;
            display: block;
            width: fit-content;
            margin: 20px auto;
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
        <a href="guide_projects.php" class="back-button">Back</a>
    </div>
    <div class="container">
        <h4>Approved Groups</h4>
        <?php foreach ($approvedGroups as $group_id => $group): ?>
            <div class="card">
                <div class="card-header">
                    Group No: <?php echo $group_id; ?> - Project: <?php echo htmlspecialchars($group['project_name']); ?>
                </div>
                <div class="card-content">
                    <table>
                        <thead>
                            <tr>
                                <th>Student Name</th>
                                <th>Register ID</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($group['students'] as $student): ?>
                                <tr>
                                    <td><?php echo htmlspecialchars($student['name']); ?></td>
                                    <td><?php echo htmlspecialchars($student['register_id']); ?></td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        <?php endforeach; ?>
    </div>
</body>
</html>
