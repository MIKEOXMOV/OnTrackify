<?php
session_start(); // Start session if not already started

// Check if user is logged in, otherwise redirect to login page
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

// Database connection
$servername = "localhost";
$username = "root"; // Your MySQL username
$password = ""; // Your MySQL password
$dbname = "profile_management"; // Your database name for profile details

// Create connection
$conn = new mysqli($servername, $username, $password, $dbname);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Get form data
$name = $_POST['name'];
$email = $_POST['email'];
$register_or_faculty_id = $_POST['register_or_faculty_id'];
$role = $_POST['role'];
$department = $_POST['department'];
$semester = $_POST['semester'];
$college_name = $_POST['college_name'];
$batch = $_POST['batch'];

// Get user ID from session
$user_id = $_SESSION['user_id'];

// Insert user profile into the database
$sql = "INSERT INTO profile_details (role_id, name, email, register_or_faculty_id, department, semester, college_name, batch)
        VALUES ('$user_id', '$name', '$email', '$register_or_faculty_id', '$department', '$semester', '$college_name', '$batch')";

if ($conn->query($sql) === TRUE) {
    // Redirect to success page with query string parameters
    header("Location: success_page.php?name=$name&email=$email&register_or_faculty_id=$register_or_faculty_id&role=$role&department=$department&semester=$semester&college_name=$college_name&batch=$batch");
    exit();
} else {
    echo "Error updating record: " . $conn->error;
}

// Close the database connection
$conn->close();
?>
