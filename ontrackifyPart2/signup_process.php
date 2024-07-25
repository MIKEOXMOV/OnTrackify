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
} elseif ($role == 'coordinator') {
    $faculty_id = $_POST['faculty_id'];
    if (empty($faculty_id)) {
        die("Faculty ID is required for coordinators.");
    }
    $register_or_faculty_id = $faculty_id;
    $role .= ',guide'; // Add guide role for coordinators
} elseif ($role == 'guide') {
    $faculty_id = $_POST['faculty_id'];
    if (empty($faculty_id)) {
        die("Faculty ID is required for guides.");
    }
    $register_or_faculty_id = $faculty_id;
} else {
    die("Invalid role specified.");
}

// Hash the password
$hashed_password = password_hash($password, PASSWORD_BCRYPT);

// Check if the user already exists
$existing_user_stmt = $conn->prepare("SELECT role FROM users WHERE register_or_faculty_id = ? OR email = ?");
$existing_user_stmt->bind_param("ss", $register_or_faculty_id, $email);
$existing_user_stmt->execute();
$existing_user_stmt->store_result();

if ($existing_user_stmt->num_rows > 0) {
    // User exists, fetch existing roles
    $existing_user_stmt->bind_result($existing_roles);
    $existing_user_stmt->fetch();
    $existing_roles_array = explode(',', $existing_roles);

    $new_roles_array = array_merge($existing_roles_array, explode(',', $role));
    $new_roles_array = array_unique($new_roles_array); // Remove duplicate roles
    $new_roles = implode(',', $new_roles_array);

    // Update the user's roles
    $update_roles_stmt = $conn->prepare("UPDATE users SET role = ? WHERE register_or_faculty_id = ?");
    $update_roles_stmt->bind_param("ss", $new_roles, $register_or_faculty_id);
    if ($update_roles_stmt->execute()) {
        echo "<script>alert('Signup successful. Your roles have been updated.'); window.location.href = 'login.php';</script>";
    } else {
        echo "Error updating roles: " . $update_roles_stmt->error;
    }
    $update_roles_stmt->close();
} else {
    // New user, insert into the database
    $stmt = $conn->prepare("INSERT INTO users (name, register_or_faculty_id, email, password, role) VALUES (?, ?, ?, ?, ?)");
    $stmt->bind_param("sssss", $name, $register_or_faculty_id, $email, $hashed_password, $role);

    if ($stmt->execute()) {
        echo "<script>alert('Signup successful. You can now login.'); window.location.href = 'login.php';</script>";
    } else {
        if ($conn->errno == 1062) {
            echo "Error: Duplicate ID. The ID '$register_or_faculty_id' is already in use.";
        } else {
            echo "Error: " . $stmt->error;
        }
    }
    $stmt->close();
}

$existing_user_stmt->close();
$conn->close();
?>