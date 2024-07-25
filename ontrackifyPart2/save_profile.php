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

// Retrieve user ID from session
$user_id = $_SESSION['user_id'];

// Escape and sanitize input values
$name = mysqli_real_escape_string($conn, $_POST['name']);
$email = mysqli_real_escape_string($conn, $_POST['email']);
$register_or_faculty_id = mysqli_real_escape_string($conn, $_POST['register_or_faculty_id']);
$role = mysqli_real_escape_string($conn, $_POST['role']);

// Update users table
$update_user_sql = "UPDATE users SET name='$name', email='$email', register_or_faculty_id='$register_or_faculty_id' WHERE id=$user_id";

if ($conn->query($update_user_sql) === TRUE) {
    // Update additional details based on role
    if ($role == 'student') {
        $department = mysqli_real_escape_string($conn, $_POST['department']);
        $semester = mysqli_real_escape_string($conn, $_POST['semester']);
        $college_name = mysqli_real_escape_string($conn, $_POST['college_name']);
        $batch = mysqli_real_escape_string($conn, $_POST['batch']);

        // Check if student record exists
        $check_student_sql = "SELECT * FROM students WHERE user_id = $user_id";
        $check_student_result = $conn->query($check_student_sql);

        if ($check_student_result->num_rows > 0) {
            // Update existing student record
            $update_student_sql = "UPDATE students SET department='$department', semester='$semester', college_name='$college_name', batch='$batch' WHERE user_id=$user_id";
            $conn->query($update_student_sql);
        } else {
            // Insert new student record
            $insert_student_sql = "INSERT INTO students (user_id, department, semester, college_name, batch) VALUES ($user_id, '$department', '$semester', '$college_name', '$batch')";
            $conn->query($insert_student_sql);
        }
    } elseif ($role == 'coordinator') {
        $department = mysqli_real_escape_string($conn, $_POST['department']);
        $college_name = mysqli_real_escape_string($conn, $_POST['college_name']);

        // Check if coordinator record exists
        $check_coordinator_sql = "SELECT * FROM coordinators WHERE user_id = $user_id";
        $check_coordinator_result = $conn->query($check_coordinator_sql);

        if ($check_coordinator_result->num_rows > 0) {
            // Update existing coordinator record
            $update_coordinator_sql = "UPDATE coordinators SET department='$department', college_name='$college_name' WHERE user_id=$user_id";
            $conn->query($update_coordinator_sql);
        } else {
            // Insert new coordinator record
            $insert_coordinator_sql = "INSERT INTO coordinators (user_id, department, college_name) VALUES ($user_id, '$department', '$college_name')";
            $conn->query($insert_coordinator_sql);
        }
    } elseif ($role == 'guide') {
        $department = mysqli_real_escape_string($conn, $_POST['department']);
        $college_name = mysqli_real_escape_string($conn, $_POST['college_name']);

        // Check if guide record exists
        $check_guide_sql = "SELECT * FROM guides WHERE user_id = $user_id";
        $check_guide_result = $conn->query($check_guide_sql);

        if ($check_guide_result->num_rows > 0) {
            // Update existing guide record
            $update_guide_sql = "UPDATE guides SET department='$department', college_name='$college_name' WHERE user_id=$user_id";
            $conn->query($update_guide_sql);
        } else {
            // Insert new guide record
            $insert_guide_sql = "INSERT INTO guides (user_id, department, college_name) VALUES ($user_id, '$department', '$college_name')";
            $conn->query($insert_guide_sql);
        }
    }

    // Redirect to dashboard based on role with success message
    $_SESSION['profile_update_success'] = true;
    if ($role == 'student') {
        header("Location: student_panel.php");
    } elseif ($role == 'coordinator') {
        header("Location: coordinator_panel.php");
    } elseif ($role == 'guide') {
        header("Location: guide_panel.php");
    } else {
        // Handle other roles as needed
        echo "Role not recognized.";
        exit();
    }
    exit();
} else {
    echo "Error updating profile: " . $conn->error;
}

$conn->close();
?>
