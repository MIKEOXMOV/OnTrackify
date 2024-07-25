<?php
session_start();
include 'config.php';

// Ensure only guides can access this page
if ($_SESSION['role'] != 'guide') {
    header("Location: login.php");
    exit();
}

$guide_id = $_SESSION['user_id'];

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $data = json_decode(file_get_contents("php://input"), true);
    $marksToUpdate = $data['marks'];

    foreach ($marksToUpdate as $mark) {
        $student_id = $mark['student_id'];
        $guide_marks = $mark['guide_marks'];
        $group_id = $mark['group_id'];
        $project_id = $mark['project_id'];

        // Check if marks already exist
        $check_query = "SELECT * FROM guide_marks WHERE student_id = '$student_id' AND project_id = '$project_id' AND group_id = '$group_id' AND guide_id = '$guide_id'";
        $check_result = mysqli_query($conn, $check_query);

        if (mysqli_num_rows($check_result) > 0) {
            // Update existing marks
            $update_query = "UPDATE guide_marks SET marks = '$guide_marks', updated_at = NOW() WHERE student_id = '$student_id' AND project_id = '$project_id' AND group_id = '$group_id' AND guide_id = '$guide_id'";
            mysqli_query($conn, $update_query);
        } else {
            // Insert new marks
            $insert_query = "INSERT INTO guide_marks (project_id, group_id, guide_id, student_id, marks, created_at, updated_at) VALUES ('$project_id', '$group_id', '$guide_id', '$student_id', '$guide_marks', NOW(), NOW())";
            mysqli_query($conn, $insert_query);
        }
    }

    echo json_encode(['status' => 'success']);
    exit();
}

// Fetch approved group IDs and their members with registration IDs
$approvedGroupsQuery = "SELECT DISTINCT r.group_id, g.project_id, u.name AS student_name, u.register_or_faculty_id, u.id AS student_id, guide_marks.marks AS guide_marks
                       FROM requests r
                       JOIN groups g ON r.group_id = g.group_id
                       JOIN group_members gm ON r.group_id = gm.group_id
                       JOIN users u ON gm.student_id = u.id
                       LEFT JOIN guide_marks ON guide_marks.student_id = u.id AND guide_marks.project_id = g.project_id AND guide_marks.guide_id = '$guide_id'
                       WHERE r.guide_id = '$guide_id' AND r.status = 'approved'";

$approvedGroupsResult = mysqli_query($conn, $approvedGroupsQuery);

$approvedGroups = [];
if ($approvedGroupsResult && mysqli_num_rows($approvedGroupsResult) > 0) {
    while ($row = mysqli_fetch_assoc($approvedGroupsResult)) {
        $group_id = $row['group_id'];
        $project_id = $row['project_id'];
        $student_name = $row['student_name'];
        $register_id = $row['register_or_faculty_id'];
        $student_id = $row['student_id'];
        $guide_marks = $row['guide_marks'];

        if (!isset($approvedGroups[$group_id])) {
            $approvedGroups[$group_id] = [
                'project_id' => $project_id,
                'students' => []
            ];
        }
        // Store student details in the group array
        $approvedGroups[$group_id]['students'][] = [
            'name' => $student_name,
            'register_id' => $register_id,
            'student_id' => $student_id,
            'guide_marks' => $guide_marks // Store guide marks
        ];
    }
}

mysqli_close($conn);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Approved Groups Evaluation</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <style>
        body {
            background-color: #f9f9f9;
            padding: 20px;
        }
        .container {
            background-color: #fff;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
            border-radius: 8px;
            padding: 20px;
            margin-top: 20px;
        }
        h1 {
            text-align: center;
            color: #333;
            font-size: 1.5em;
            margin-bottom: 20px;
        }
        .card {
            margin-bottom: 20px;
            border: 1px solid #ddd;
            border-radius: 8px;
            overflow: hidden;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
        }
        .card-header {
            background-color: #007bff;
            color: #fff;
            padding: 12px 20px;
            font-weight: bold;
        }
        .card-body {
            padding: 20px;
        }
        .group-title {
            font-size: 1.2em;
            margin-bottom: 10px;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 15px;
        }
        th, td {
            border: 1px solid #ddd;
            padding: 10px;
            text-align: left;
        }
        th {
            background-color: #f2f2f2;
            font-weight: bold;
        }
        .evaluation-mark {
            font-weight: bold;
            color: #333;
        }
        .edit-input {
            width: 80px;
            padding: 5px;
            border: 1px solid #ccc;
            border-radius: 5px;
            text-align: center;
            display: none;
        }
        .evaluate-button {
            background-color: #28a745;
            color: #fff;
            border: none;
            border-radius: 5px;
            padding: 8px 16px;
            cursor: pointer;
            margin-top: 10px;
        }
        .evaluate-button:hover {
            background-color: #218838;
        }
        .save-button {
            background-color: #007bff;
            color: #fff;
            border: none;
            border-radius: 5px;
            padding: 8px 16px;
            cursor: pointer;
            margin-top: 10px;
            float: right;
            display: none;
        }
        .back-button {
            text-decoration: none;
            background-color: #007bff;
            color: #fff;
            padding: 10px 20px;
            border-radius: 5px;
            border: none;
            cursor: pointer;
            display: inline-block;
            margin-top: 20px;
        }.back-button {
            display: inline-block;
            padding: 10px 20px;
            background-color: #007bff;
            color: #fff;
            text-decoration: none;
            border-radius: 5px;
            margin-bottom: 20px;
            transition: background-color 0.3s ease;
        }

        .back-button:hover {
            background-color: #0056b3;
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
        <a href="view_groupsproject.php" class="back-button">Back</a>
    </div>
   
    <div class="container">
        <h1>Approved Groups Evaluation</h1>
        <?php foreach ($approvedGroups as $group_id => $group): ?>
            <div class="card">
                <div class="card-header">
                    Group No: <?php echo $group_id; ?>
                </div>
                <div class="card-body">
                    <div class="group-title">Group Members:</div>
                    <table>
                        <thead>
                            <tr>
                                <th>Student Name</th>
                                <th>Register ID</th>
                                <th>Evaluation</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($group['students'] as $student): ?>
                                <tr>
                                    <td><?php echo htmlspecialchars($student['name']); ?></td>
                                    <td><?php echo htmlspecialchars($student['register_id']); ?></td>
                                    <td>
                                        <span class="evaluation-mark"><?php echo $student['guide_marks'] ?? 'Not evaluated'; ?></span>
                                        <input type="number" class="edit-input" name="guide_marks" value="<?php echo $student['guide_marks'] ?? ''; ?>" placeholder="0-15" min="0" max="15">
                                        <input type="hidden" class="student-id" value="<?php echo $student['student_id']; ?>">
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                    <button class="evaluate-button" onclick="toggleEvaluation(this)">Evaluate</button>
                    <button class="save-button" onclick="saveEvaluation(this, <?php echo $group_id; ?>, <?php echo $group['project_id']; ?>)" style="display:none;">Save</button>
                </div>
            </div>
        <?php endforeach; ?>
    </div>

    <script>
        function toggleEvaluation(button) {
            let cardBody = button.parentElement;
            let editInputs = cardBody.querySelectorAll('.edit-input');
            let evaluationMark = cardBody.querySelectorAll('.evaluation-mark');
            let saveButton = cardBody.querySelector('.save-button');
            let evaluateButton = button;

            editInputs.forEach(input => {
                input.style.display = 'inline-block';
            });

            evaluationMark.forEach(span => {
                span.style.display = 'none';
            });

            saveButton.style.display = 'inline-block';
            evaluateButton.style.display = 'none';
        }

        function saveEvaluation(button, group_id, project_id) {
            let cardBody = button.parentElement;
            let editInputs = cardBody.querySelectorAll('.edit-input');
            let studentIds = cardBody.querySelectorAll('.student-id');
            let marks = [];

            editInputs.forEach((input, index) => {
                let student_id = studentIds[index].value;
                let guide_marks = input.value;

                marks.push({
                    student_id: student_id,
                    guide_marks: guide_marks,
                    group_id: group_id,
                    project_id: project_id
                });
            });

            // Send data to server via AJAX
            fetch('guide_evaluation.php', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json'
                },
                body: JSON.stringify({ marks: marks })
            })
            .then(response => response.json())
            .then(data => {
                if (data.status === 'success') {
                    alert('Evaluation marks saved successfully!');
                    location.reload();
                } else {
                    alert('Failed to save evaluation marks. Please try again.');
                }
            })
            .catch(error => {
                console.error('Error:', error);
                alert('Failed to save evaluation marks. Please try again.');
            });
        }
    </script>
</body>
</html>