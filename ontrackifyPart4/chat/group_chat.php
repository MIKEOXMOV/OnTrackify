<?php
session_start();

// Check if the user is logged in
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

// Initialize variables
$user_id = $_SESSION['user_id'];
$group_id = isset($_GET['group_id']) ? $_GET['group_id'] : 0;

// Handle form submission (sending messages)
if (isset($_POST['submit'])) {
    $msg = $_POST['msg'];
    if (!empty($msg)) {
        $link = mysqli_connect("localhost", "root", "", "chat_app");
        if ($link === false) {
            die("ERROR: Could not connect. " . mysqli_connect_error());
        }

        $msg = mysqli_real_escape_string($link, $msg);
        date_default_timezone_set('Asia/Kolkata');
        $ts = date('y-m-d h:ia');

        $sql = "INSERT INTO chats (user_id, group_id, msg, dt) VALUES ('$user_id', '$group_id', '$msg', '$ts')";
        if (mysqli_query($link, $sql)) {
            // Message sent successfully
        } else {
            echo "ERROR: Message not sent!!!";
        }

        mysqli_close($link);
    }
}

// Fetch chat messages for the current group
$chat_messages = [];
if ($group_id > 0) {
    $link = mysqli_connect("localhost", "root", "", "chat_app");
    if ($link === false) {
        die("ERROR: Could not connect. " . mysqli_connect_error());
    }

    $sql = "SELECT c.*, u.username 
            FROM chats c 
            INNER JOIN users u ON c.user_id = u.id 
            WHERE c.group_id = '$group_id' 
            ORDER BY c.dt ASC";

    $result = mysqli_query($link, $sql);
    if ($result) {
        while ($row = mysqli_fetch_assoc($result)) {
            $chat_messages[] = $row;
        }
    }

    mysqli_close($link);
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Group Chat</title>
<style>
    /* CSS styles for the chat interface */
    body {
        font-family: Arial, sans-serif;
        background-color: #f2f2f2;
        margin: 0;
        padding: 0;
    }

    .chat-container {
        max-width: 800px;
        margin: 20px auto;
        background-color: #fff;
        padding: 20px;
        border-radius: 5px;
        box-shadow: 0 0 10px rgba(0,0,0,0.1);
    }

    .chat-messages {
        max-height: 400px;
        overflow-y: auto;
        border: 1px solid #ccc;
        padding: 10px;
        margin-bottom: 10px;
        border-radius: 5px;
    }

    .message {
        margin-bottom: 10px;
        padding: 10px;
        background-color: #f2f2f2;
        border-radius: 5px;
    }

    .message .meta {
        font-size: 12px;
        color: #666;
    }

    .message .username {
        font-weight: bold;
        margin-right: 10px;
    }

    .chat-input {
        display: flex;
    }

    .chat-input textarea {
        flex: 1;
        resize: none;
        padding: 10px;
        font-size: 14px;
        border-radius: 5px;
        border: 1px solid #ccc;
    }

    .chat-input button {
        padding: 10px 20px;
        margin-left: 10px;
        font-size: 14px;
        border: none;
        border-radius: 5px;
        cursor: pointer;
        background-color: #007bff;
        color: #fff;
    }
</style>
</head>
<body>
<div class="chat-container">
    <h2>Group Chat</h2>

    <!-- Display chat messages -->
    <div class="chat-messages">
        <?php foreach ($chat_messages as $message) : ?>
            <div class="message">
                <span class="username"><?php echo htmlspecialchars($message['username']); ?>:</span>
                <?php echo htmlspecialchars($message['msg']); ?>
                <div class="meta"><?php echo htmlspecialchars($message['dt']); ?></div>
            </div>
        <?php endforeach; ?>
    </div>

    <!-- Chat input form -->
    <form method="post" action="">
        <div class="chat-input">
            <textarea name="msg" placeholder="Type your message" required></textarea>
            <button type="submit" name="submit">Send</button>
        </div>
    </form>
</div>
</body>
</html>
