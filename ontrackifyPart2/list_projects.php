<?php
include 'db_connection.php';

// Select query
$sql = "SELECT * FROM projects";
$result = $conn->query($sql);

if ($result->num_rows > 0) {
    while($row = $result->fetch_assoc()) {
        echo "Course Code: " . $row["course_code"]. " - Course Name: " . $row["course_name"]. " - Coordinator: " . $row["coordinator_name"]. "<br>";
    }
} else {
    echo "0 results";
}

$conn->close();
?>
