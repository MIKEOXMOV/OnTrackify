<?php
session_start();
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

// Process login form submission
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $identifier = mysqli_real_escape_string($conn, $_POST['identifier']);
    $password = mysqli_real_escape_string($conn, $_POST['password']);

    $sql = "SELECT * FROM users WHERE email='$identifier' OR register_or_faculty_id='$identifier'";
    $result = $conn->query($sql);

    if ($result->num_rows > 0) {
        $row = $result->fetch_assoc();
        if (password_verify($password, $row['password'])) {
            $_SESSION['user_id'] = $row['id'];
            $_SESSION['role'] = $row['role'];
            if ($row['role'] == 'student') {
                header("Location: student_panel.php");
            } elseif ($row['role'] == 'coordinator') {
                header("Location: coordinator_panel.php");
            } elseif ($row['role'] == 'guide') {
                header("Location: guide_panel.php");
            } else {
                echo "Access denied.";
            }
            exit();
        } else {
            echo "Invalid password.";
        }
    } else {
        echo "No user found with this email or register number.";
    }
}

$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
            background-color: #f0f8ff;
            margin: 0;
        }
        .form-container {
            background: #007bff;
            border-radius: 10px;
            padding: 20px;
            width: 300px;
            box-shadow: 0 4px 8px rgba(0,0,0,0.1);
        }
        .form-container .header {
            color: white;
            text-align: center;
            margin-bottom: 20px;
            font-size: 24px;
        }
        .form-container form {
            display: flex;
            flex-direction: column;
        }
        .form-container form label {
            color: white;
            margin-bottom: 5px;
        }
        .form-container form input[type="text"],
        .form-container form input[type="password"] {
            padding: 10px;
            margin-bottom: 10px;
            border: none;
            border-radius: 5px;
        }
        .form-container form input[type="submit"] {
            padding: 10px;
            border: none;
            border-radius: 5px;
            background: white;
            color: #007bff;
            font-size: 16px;
            cursor: pointer;
        }
        .form-container .links {
            text-align: center;
            margin-top: 10px;
        }
        .form-container .links a {
            color: white;
            text-decoration: none;
            margin: 0 5px;
        }
        .form-container .links a:hover {
            text-decoration: underline;
        }
    </style>
</head>
<body>
    <div class="form-container">
        <div class="header">Login</div>
        <form action="" method="POST">
            <label for="identifier">Register Number/Faculty ID or Email:</label>
            <input type="text" id="identifier" name="identifier" required>

            <label for="password">Password:</label>
            <input type="password" id="password" name="password" required><br>

            <input type="submit" value="Login">
        </form>
        <div class="links">
            <p>Not a member? <a href="signup.html">Sign up</a></p>
            <p><a href="forgot_password.html">Forgot Password?</a></p>
        </div>
    </div>
</body>
</html>