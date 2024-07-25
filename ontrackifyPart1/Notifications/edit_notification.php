<?php
session_start();
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

include 'config.php';

$user_id = $_SESSION['user_id'];
$role = $_SESSION['role'];

if (isset($_GET['id'])) {
    $id = $_GET['id'];

    // Fetch notification
    $sql = "SELECT * FROM notifications WHERE id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $id);
    $stmt->execute();
    $result = $stmt->get_result();
    $notification = $result->fetch_assoc();
    $stmt->close();

    if ($_SERVER["REQUEST_METHOD"] == "POST") {
        $message = $_POST['message'];
        $recipient = $_POST['recipient'];
        $date_time = date('Y-m-d H:i:s');

        $sql = "UPDATE notifications SET message = ?, recipient = ?, date_time = ? WHERE id = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("sssi", $message, $recipient, $date_time, $id);
        $stmt->execute();
        $stmt->close();

        echo "<script>alert('Notification updated'); window.location.href='compose_notification.php';</script>";
        exit();
    }
} else {
    echo "<script>alert('Invalid notification ID'); window.location.href='compose_notification.php';</script>";
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Notification</title>
    <link rel="stylesheet" href="stdstyle.css">
    <style>
        /* Custom CSS for Edit Notification Page */
        .edit-container {
            max-width: 600px;
            margin: 50px auto;
            padding: 20px;
            border: 1px solid #ccc;
            border-radius: 5px;
            background-color: #f9f9f9;
        }

        .edit-container h2 {
            text-align: center;
            margin-bottom: 20px;
        }

        .edit-container form {
            display: flex;
            flex-direction: column;
        }

        .edit-container label {
            margin-bottom: 5px;
        }

        .edit-container input, 
        .edit-container select, 
        .edit-container textarea {
            padding: 10px;
            margin-bottom: 15px;
            border: 1px solid #ccc;
            border-radius: 5px;
        }

        .edit-container button {
            padding: 10px;
            background-color: #3498db;
            color: #fff;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            transition: background-color 0.3s ease;
        }

        .edit-container button:hover {
            background-color: #2980b9;
        }
    </style>
</head>
<body>
    <div class="edit-container">
        <h2>Edit Notification</h2>
        <form action="edit_notification.php?id=<?= $id ?>" method="POST">
            <label for="recipient">Recipient:</label>
            <select name="recipient" id="recipient" required>
                <option value="student" <?= $notification['recipient'] == 'student' ? 'selected' : '' ?>>Student</option>
                <option value="guide" <?= $notification['recipient'] == 'guide' ? 'selected' : '' ?>>Guide</option>
            </select>

            <label for="message">Message:</label>
            <textarea name="message" id="message" rows="5" required><?= htmlspecialchars($notification['message']) ?></textarea>

            <button type="submit">Update</button>
        </form>
    </div>
</body>
</html>
