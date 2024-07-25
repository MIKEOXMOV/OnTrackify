<?php
session_start();

// Check if user is not logged in, redirect to login page
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

// Database connection parameters
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "role_management";

// Create connection
$conn = new mysqli($servername, $username, $password, $dbname);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Retrieve user ID and role from session
$user_id = $_SESSION['user_id'];
$role = $_SESSION['role'];

// Fetch notifications for students
$sql = "SELECT * FROM notifications WHERE recipient = 'guide' ORDER BY date_time DESC";
$result = $conn->query($sql);

// Initialize an array to store notifications
$notifications = [];

if ($result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $notifications[] = $row;
    }
}

// Close connection
$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Notifications</title>

    <!-- Custom Styles -->
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f0f0f0;
            margin: 0;
            padding: 0;
        }

        .container {
            max-width: 800px;
            margin: 20px auto;
            background-color: #fff;
            border-radius: 5px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
            padding: 20px;
        }

        .notification {
            border-bottom: 1px solid #ddd;
            padding: 10px 0;
        }

        .notification-title {
            font-weight: bold;
            color: #333;
        }

        .notification-message {
            color: #666;
            margin-top: 5px;
        }
        .back-button {
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
    </style>
</head>
<body>
    <div class="container">
        <h2>Notifications</h2>
        <?php foreach ($notifications as $notification): ?>
            <div class="notification">
               
                <div class="notification-message"><?= htmlspecialchars($notification['message']) ?></div>
                <div class="notification-date"><?= htmlspecialchars($notification['date_time']) ?></div>
            </div>
        <?php endforeach; ?>
        <a href="guide_panel.php" class="back-button">Back </a>
    </div>
</body>
</html>
