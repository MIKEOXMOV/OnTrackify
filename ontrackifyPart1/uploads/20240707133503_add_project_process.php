<?php
include 'db_connection.php';

// Escape user inputs for security
$course_code = mysqli_real_escape_string($conn, $_POST['course_code']);
$course_name = mysqli_real_escape_string($conn, $_POST['course_name']);
$coordinator_name = mysqli_real_escape_string($conn, $_POST['coordinator_name']);
$batch = mysqli_real_escape_string($conn, $_POST['batch']);
$semester = mysqli_real_escape_string($conn, $_POST['semester']);

// Insert query
$sql = "INSERT INTO projects (course_code, course_name, coordinator_name, batch, semester)
        VALUES ('$course_code', '$course_name', '$coordinator_name', '$batch', '$semester')";

if ($conn->query($sql) === TRUE) {
    echo "New project added successfully";
} else {
    echo "Error: " . $sql . "<br>" . $conn->error;
}

$conn->close();
?>
