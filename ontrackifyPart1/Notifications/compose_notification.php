<?php
session_start();

// Check if user is not logged in, redirect to login page
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

include 'config.php';

$user_id = $_SESSION['user_id'];
$role = $_SESSION['role'];

// Handle form submission
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['send'])) {
    $message = $_POST['message'];
    $recipient = $_POST['recipient'];
    $coordinator_id = $user_id;
    $date_time = date('Y-m-d H:i:s');

    $sql = "INSERT INTO notifications (message, recipient, coordinator_id, date_time) VALUES (?, ?, ?, ?)";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("ssis", $message, $recipient, $coordinator_id, $date_time);
    $stmt->execute();
    $stmt->close();
    echo "<script>alert('Notification sent'); window.location.href='compose_notification.php';</script>";
}

// Fetch notifications
$sql = "SELECT * FROM notifications WHERE coordinator_id = ? ORDER BY date_time DESC";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();
$notifications = $result->fetch_all(MYSQLI_ASSOC);
$stmt->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Compose Notification</title>
    <link rel="stylesheet" href="stdstyle.css">
    <style>
       /* Ensure high specificity to override existing styles */
        body {
            font-family: Arial, sans-serif;
            background-color: #f0f0f0;
            margin: 0;
            padding: 0;
        }

        .compose-container {
            max-width: 600px;
            margin: 50px auto;
            padding: 20px;
            border: 1px solid #ccc;
            border-radius: 5px;
            background-color: #fff;
        }

        .compose-container h2 {
            text-align: center;
            margin-bottom: 20px;
        }

        .compose-container form {
            display: flex;
            flex-direction: column;
        }

        .compose-container label {
            margin-bottom: 5px;
        }

        .compose-container input,
        .compose-container select,
        .compose-container textarea {
            padding: 10px;
            margin-bottom: 15px;
            border: 1px solid #ccc;
            border-radius: 5px;
        }

        .compose-container button {
            padding: 10px;
            background-color: #3498db;
            color: #fff;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            transition: background-color 0.3s ease;
        }

        .compose-container button:hover {
            background-color: #2980b9;
        }

        .notification-list {
            max-width: 600px;
            margin: 20px auto;
            background-color: #fff;
            padding: 20px;
            border: 1px solid #ccc;
            border-radius: 5px;
        }

        .notification-item {
            border: 1px solid #ccc;
            padding: 10px;
            margin-bottom: 10px;
            border-radius: 5px;
            background-color: #f9f9f9; /* Light gray background color */
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .notification-item span {
            flex-grow: 1;
        }

        .notification-item button {
            margin-left: 10px;
            background-color: #e74c3c;
            color: #fff;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            transition: background-color 0.3s ease;
        }

        .notification-item button.edit {
            background-color: #3498db;
        }

        .notification-item button:hover {
            background-color: #c0392b;
        }

        .notification-item button.edit:hover {
            background-color: #2980b9;
        }

        .back-button {
            display: inline-block;
            padding: 10px 20px;
            background-color: #007bff;
            color: #fff;
            text-decoration: none;
            border-radius: 5px;
            margin-top: 20px;
            transition: background-color 0.3s ease;
        }

        .back-button:hover {
            background-color: #0056b3;
        }
    </style>
</head>
<body>
    <div class="compose-container">
        <h2>Compose Notification</h2>
        <form action="compose_notification.php" method="POST">
            <label for="recipient">Recipient:</label>
            <select name="recipient" id="recipient" required>
                <option value="student">Student</option>
                <option value="guide">Guide</option>
            </select>

            <label for="message">Message:</label>
            <textarea name="message" id="message" rows="5" required></textarea>

            <button type="submit" name="send">Send</button>
        </form>
    </div>

    <div class="notification-list">
        <h2>Sent Notifications</h2>
        <?php foreach ($notifications as $notification): ?>
            <div class="notification-item" data-id="<?= $notification['id'] ?>">
                <span><?= htmlspecialchars($notification['message']) ?> (<?= htmlspecialchars($notification['recipient']) ?>)</span>
                <button class="edit">Edit</button>
                <button class="delete">Delete</button>
            </div>
        <?php endforeach; ?>
    </div>

    <a href="coordinator_panel.php" class="back-button">Back to Coordinator Panel</a>

    <script>
        document.addEventListener("DOMContentLoaded", function() {
            const notificationItems = document.querySelectorAll('.notification-item');
            notificationItems.forEach(item => {
                const editButton = item.querySelector('.edit');
                const deleteButton = item.querySelector('.delete');
                
                editButton.addEventListener('click', () => {
                    const id = item.dataset.id;
                    window.location.href = `edit_notification.php?id=${id}`;
                });

                deleteButton.addEventListener('click', () => {
                    const id = item.dataset.id;
                    if (confirm('Are you sure you want to delete this notification?')) {
                        fetch(`delete_notification.php?id=${id}`, {
                            method: 'GET'
                        }).then(response => response.text()).then(data => {
                            if (data === 'success') {
                                item.remove();
                            } else {
                                alert('Error deleting notification');
                            }
                        });
                    }
                });
            });
        });
    </script>
</body>
</html>
