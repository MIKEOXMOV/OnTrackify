<?php
session_start();
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'guide') {
    header("Location: login.php");
    exit();
}

include 'config.php';

$guide_id = $_SESSION['user_id'];
$project_id = isset($_GET['project_id']) ? $_GET['project_id'] : null;

if ($project_id === null) {
    die("Project ID not provided.");
}

// Query to fetch project name based on project_id
$sql_project_name = "SELECT name FROM projects WHERE id = ?";
$stmt_project_name = $conn->prepare($sql_project_name);
$stmt_project_name->bind_param('i', $project_id);
$stmt_project_name->execute();
$stmt_project_name->bind_result($project_name);
$stmt_project_name->fetch();
$stmt_project_name->close();

$sql = "
    SELECT 
        u.name AS file_name, 
        u.fname, 
        r.group_id, 
        us.name AS student_name, 
        us.register_or_faculty_id 
    FROM 
        upload u 
        JOIN requests r ON u.group_id = r.group_id 
        JOIN project_members pm ON r.student_id = pm.student_id 
        JOIN guide_projects gp ON pm.project_id = gp.project_id 
        JOIN users us ON u.student_id = us.id 
    WHERE 
        gp.guide_id = ?
        AND gp.project_id = ?
        AND r.status = 'approved'
    ORDER BY r.group_id, u.id DESC";

$stmt = $conn->prepare($sql);
$stmt->bind_param('ii', $guide_id, $project_id);
$stmt->execute();
$result = $stmt->get_result();
$files = $result->fetch_all(MYSQLI_ASSOC);

$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>View Group Files</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <style>
        .table-container {
            margin-top: 20px;
        }
        .table-container h3 {
            margin-bottom: 10px;
            color: #007bff;
        }
        .table-container .table {
            background-color: #f8f9fa; /* Light gray background */
        }
        .table-container .table th {
            background-color: #007bff; /* Blue header background */
            color: #ffffff; /* White text color */
        }
        .table-container .table-striped tbody tr:nth-of-type(odd) {
            background-color: rgba(0, 123, 255, 0.1); /* Light blue shade for odd rows */
        }
        .table-container .table-striped tbody tr:hover {
            background-color: rgba(0, 123, 255, 0.2); /* Slightly darker blue on hover */
        }
        .btn-primary {
            background-color: #ffffff; /* White background */
            color: #007bff; /* Blue text color */
            border-color: #007bff; /* Blue border color */
        }
        .btn-primary:hover {
            background-color: #007bff; /* Blue background on hover */
            color: #ffffff; /* White text color on hover */
            border-color: #007bff; /* Blue border color on hover */
        }
    </style>
</head>
<body>
<nav class="navbar navbar-light bg-light">
    <a class="navbar-brand" href="#">
        <img src="logo.png" width="40" height="40" class="d-inline-block align-top" alt="">
        OnTrackify
    </a>
    <a href="guide_projects_upload.php" class="btn btn-primary ml-auto">Back</a>
</nav>

<div class="container">
    <div class="table-container">
        <h2 class="mt-4 mb-4">Uploaded Files of  <?php echo htmlspecialchars($project_name); ?>Group</h2>
        
        <?php if (empty($files)): ?>
            <div class="alert alert-info" role="alert">
                No files found for this project.
            </div>
        <?php else: ?>
            <?php
            $current_group_id = null;
            foreach ($files as $file):
                if ($current_group_id !== $file['group_id']): // Start new group section
                    if ($current_group_id !== null): // Close previous group if not the first iteration
                        echo '</tbody></table></div>';
                    endif;
                    $current_group_id = $file['group_id'];
            ?>
            <div>
                <h4>Group No: <?php echo htmlspecialchars($file['group_id']); ?></h4>
                <table class="table table-striped table-bordered">
                    <thead>
                        <tr>
                            <th>File Name</th>
                            <th>Uploaded By</th>
                            <th>Register/Faculty ID</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                    <tbody>
            <?php endif; ?>
                            <tr>
                                <td><?php echo htmlspecialchars($file['file_name']); ?></td>
                                <td><?php echo htmlspecialchars($file['student_name']); ?></td>
                                <td><?php echo htmlspecialchars($file['register_or_faculty_id']); ?></td>
                                <td>
                                    <a href="download.php?filename=<?php echo urlencode($file['file_name']); ?>&f=<?php echo urlencode($file['fname']); ?>" class="btn btn-success">Download</a>
                                </td>
                            </tr>
            <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        <?php endif; ?>
    </div>
</div>

<script src="https://code.jquery.com/jquery-3.5.1.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.5.3/dist/umd/popper.min.js"></script>
<script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
</body>
</html>
