<?php
session_start();
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

include 'config.php';

$guide_id = $_SESSION['user_id'];

// Fetch approved group IDs and their members
$approvedGroupsQuery = "SELECT DISTINCT r.group_id, u.name AS student_name
                       FROM requests r
                       JOIN groups g ON r.group_id = g.group_id
                       JOIN group_members gm ON r.group_id = gm.group_id
                       JOIN users u ON gm.student_id = u.id
                       WHERE r.guide_id = '$guide_id' AND r.status = 'approved'";
$approvedGroupsResult = mysqli_query($conn, $approvedGroupsQuery);

$approvedGroups = [];
if ($approvedGroupsResult && mysqli_num_rows($approvedGroupsResult) > 0) {
    while ($row = mysqli_fetch_assoc($approvedGroupsResult)) {
        $group_id = $row['group_id'];
        $student_name = $row['student_name'];

        if (!isset($approvedGroups[$group_id])) {
            $approvedGroups[$group_id] = [
                'students' => []
            ];
        }
        $approvedGroups[$group_id]['students'][] = $student_name;
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
        h1 {
            text-align: center;
            color: #333;
            font-size: 1.5em;
            margin-bottom: 10px;
        }
        .group-card {
            margin-bottom: 20px;
            padding: 10px;
            border: 1px solid #ddd;
            border-radius: 8px;
            background-color: #f2f2f2;
        }
        .group-card h2 {
            font-size: 1.2em;
            color: #007bff;
            margin-bottom: 5px;
        }
        .group-card .students {
            margin-left: 20px;
        }
        .student-name {
            margin-right: 10px;
            padding: 5px 10px;
            color: black;
            display: block;
            margin-bottom: 5px;
        }
        .update-progress-btn {
            background-color: #007bff;
            color: #fff;
            border: none;
            border-radius: 4px;
            padding: 8px 12px;
            cursor: pointer;
            text-decoration: none;
            margin-top: 10px;
            display: inline-block;
        }
        .update-progress-btn:hover {
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
</head>
<body>
<div class="header">
        <div class="logo">
            <img src="logo.png" alt="Logo">
            <div class="logo-name">OnTrackify</div>
        </div>
        <a href="guide_panel.php" class="back-button">Back</a>
    </div>
    <div class="container">
        <h1> Groups</h1>
        <?php foreach ($approvedGroups as $group_id => $group): ?>
            <div class="group-card">
                <h2>Group No: <?php echo $group_id; ?></h2>
                <p><strong>Group Members:</strong></p>
                <div class="students">
                    <?php foreach ($group['students'] as $student): ?>
                        <span class="student-name"><?php echo htmlspecialchars($student); ?></span><br>
                    <?php endforeach; ?>
                </div>

                <a href="update_progress.php?group_id=<?php echo $group_id; ?>" class="update-progress-btn">update progress</a>
            </div>
        <?php endforeach; ?>
    </div>
</body>
</html>