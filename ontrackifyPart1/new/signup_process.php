<?php
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

$name = $_POST['name'];
$email = $_POST['email'];
$password = $_POST['password'];
$confirm_password = $_POST['confirm_password'];
$role = $_POST['role'];

if ($password !== $confirm_password) {
    die("Passwords do not match.");
}

// Assign the appropriate ID based on the role
if ($role == 'student') {
    $register_number = $_POST['register_number'];
    if (empty($register_number)) {
        die("Register Number is required for students.");
    }
    $register_or_faculty_id = $register_number;
} else {
    $faculty_id = $_POST['faculty_id'];
    if (empty($faculty_id)) {
        die("Faculty ID is required for coordinators and guides.");
    }
    $register_or_faculty_id = $faculty_id;
}

// Hash the password
$hashed_password = password_hash($password, PASSWORD_BCRYPT);

// Prepare the SQL statement
$stmt = $conn->prepare("INSERT INTO users (name, register_or_faculty_id, email, password, role) VALUES (?, ?, ?, ?, ?)");
$stmt->bind_param("sssss", $name, $register_or_faculty_id, $email, $hashed_password, $role);

if ($stmt->execute()) {
    echo "Signup successful. You can now <a href='login.php'>login</a>.";
} else {
    if ($conn->errno == 1062) {
        echo "Error: Duplicate ID. The ID '$register_or_faculty_id' is already in use.";
    } else {
        echo "Error: " . $stmt->error;
    }
}

$stmt->close();
$conn->close();
?>
