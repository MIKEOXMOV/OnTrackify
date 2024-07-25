<?php

session_start();

if (isset($_SESSION['user_id'])) {
    // Determine the role and redirect accordingly
    $role = $_SESSION['role']; // Assuming you have 'role' stored in session

    switch ($role) {
        case 'student':
            header("Location: student_panel.php");
            break;
        case 'guide':
            header("Location: guide_panel.php");
            break;
        case 'coordinator':
            header("Location: coordinator_panel.php");
            break;
        default:
            // Redirect to a default dashboard or handle error
            header("Location: dashboard.php");
            break;
    }
    exit();
}
?>

<!DOCTYPE html>
<!-- Your HTML code for the landing page -->
<html lang="en" dir="ltr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Website Layout | OnTrackify</title>
    <link rel="stylesheet" href="style1.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.2/css/all.min.css"/>
</head>
<body>
<nav>
    <div class="menu">
        <div class="logo">
            <a href="#">OnTrackify</a>
        </div>
        <ul>
            <li><a href="#">About</a></li>
            <li><a href="signup.html">Register</a></li>
            <li><a href="login.php">Login</a></li>
        </ul>
    </div>
</nav>
<div class="img"></div>
<div class="center">
    <div class="title">OnTrackify</div>
    <div class="sub_title">YOUR PROJECT COMPANION</div>
    <div class="btns">
        <button>Learn More</button>
        <button>START NOW</button>
    </div>
</div>
</body>
</html>