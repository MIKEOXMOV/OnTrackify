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

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $login_id = $_POST['login_id'];
    $password = $_POST['password'];
    $role = $_POST['role'];

    // Prepare statement to find user by email or register/faculty ID
    $stmt = $conn->prepare("SELECT id, name, password, role FROM users WHERE (email = ? OR register_or_faculty_id = ?) AND FIND_IN_SET(?, role)");
    $stmt->bind_param("sss", $login_id, $login_id, $role);
    $stmt->execute();
    $stmt->store_result();

    if ($stmt->num_rows > 0) {
        $stmt->bind_result($id, $name, $hashed_password, $roles);
        $stmt->fetch();

        if (password_verify($password, $hashed_password)) {
            $_SESSION['user_id'] = $id;
            $_SESSION['name'] = $name;
            $_SESSION['role'] = $role;

            // Redirect user to their respective panel based on their role
            if ($role == 'student') {
                header("Location: student_panel.php");
            } elseif ($role == 'coordinator') {
                header("Location: coordinator_panel.php");
            } elseif ($role == 'guide') {
                header("Location: guide_panel.php");
            } else {
                echo "Invalid role.";
            }
            exit();
        } else {
            $login_error = "Incorrect password.";
        }
    } else {
        $login_error = "No user found with that login ID or role.";
    }

    $stmt->close();
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
            flex-direction: column;
            justify-content: center;
            align-items: center;
            height: 100vh;
            background-color: #f0f8ff;
            margin: 0;
        }
        .header {
            width: 100%;
            display: flex;
            justify-content: space-between;
            align-items: center;
            background-color: #fff;
            padding: 10px 20px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
            position: fixed;
            top: 0;
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

        .form-container {
            background: #ffffff;
            border-radius: 10px;
            padding: 20px;
            width: 300px;
            box-shadow: 0 4px 8px rgba(0,0,0,0.1);
            margin-top: 80px; /* Adjusting margin-top to align with fixed header */
            border: 1px solid #007bff;
        }

        .form-container form {
            display: flex;
            flex-direction: column;
        }
        .form-container form label {
            color: #333;
            margin-bottom: 5px;
            font-weight: bold;
        }
        .form-container form select,
        .form-container form input[type="text"],
        .form-container form input[type="email"],
        .form-container form input[type="password"] {
            padding: 10px;
            margin-bottom: 10px;
            border: 1px solid #ccc;
            border-radius: 5px;
            width: 100%;
            box-sizing: border-box;
            font-size: 14px;
        }
        .form-container form input[type="submit"] {
            padding: 10px;
            border: none;
            border-radius: 5px;
            background: #007bff;
            color: #fff;
            font-size: 16px;
            cursor: pointer;
            margin-top: 10px;
            transition: background 0.3s ease;
        }
        .form-container form input[type="submit"]:hover {
            background: #0056b3;
        }
        .form-container .error-message {
            color: red;
            text-align: center;
            margin-top: 10px;
        }
        .form-container .forgot-password,
        .form-container .signup-link {
            color: #007bff;
            text-align: center;
            margin-top: 10px;
        }
        .form-container .signup-link a,
        .form-container .forgot-password a {
            color: #007bff;
            text-decoration: none;
            cursor: pointer;
        }
        .form-container .signup-link a:hover,
        .form-container .forgot-password a:hover {
            text-decoration: underline;
        }
    </style>
</head>
<body>
    <div class="header">
        <div class="logo">
            <img src="logo.png" alt="Logo">
            <div class="logo-name">OnTrackify</div>
        </div>
    </div>
    <div class="form-container">
        <?php if(isset($login_error)) { ?>
            <div class="error-message"><?php echo $login_error; ?></div>
        <?php } ?>
        
        <form action="login.php" method="POST">
            <label for="login_id">Email or Register/Faculty ID:</label>
            <input type="text" id="login_id" name="login_id" required>

            <label for="password">Password:</label>
            <input type="password" id="password" name="password" required>

            <label for="role">Role:</label>
            <select id="role" name="role" required>
                <option value="student">Student</option>
                <option value="coordinator">Coordinator</option>
                <option value="guide">Guide</option>
            </select>

            <input type="submit" value="Login">
        </form>

        <div class="signup-link">
            <a href="signup.html">Not a member? Signup</a>
        </div>

        <div class="forgot-password">
            <a href="forgot_password.php">Forgot Password?</a>
        </div>
    </div>
</body>
</html>