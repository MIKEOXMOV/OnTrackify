<?php
session_start();
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

include 'config.php';

$user_id = $_SESSION['user_id'];
$user_role = $_SESSION['role']; // Assuming you have this session variable set when the user logs in

// Determine the back button URL based on user role
$back_button_url = '';
switch ($user_role) {
    case 'guide':
        $back_button_url = 'guide_panel.php';
        break;
    case 'coordinator':
        $back_button_url = 'coordinator_panel.php';
        break;
    case 'student':
        $back_button_url = 'student_panel.php';
        break;
    default:
        $back_button_url = 'login.php'; // Fallback in case the role is not recognized
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Previous Projects</title>
    <link rel="stylesheet" href="showprevtable.css">
    <style>
        /* Additional CSS for the back button */
        .back-button {
            margin-top: 20px; /* Adjust margin as needed */
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
        <a href="<?php echo htmlspecialchars($back_button_url); ?>" class="back-button">Back</a>
    </div>

    <div class="container">
        <h1>Previous Projects</h1>
        <div class="table-wrapper">
            <table>
                <thead>
                    <tr>
                        <th>Project Name</th>
                        <th>Description</th>
                        <th>Group Members</th>
                        <th>Contact Number</th>
                        <th>References</th>
                    </tr>
                </thead>
                <tbody>
                    <?php include 'display_projects.php'; ?>
                </tbody>
            </table>
        </div>
        <div class="upload-button">
            <a href="prevproject.php" class="btn">Add New</a>
        </div>

      
    </div>
</body>
</html>
