<?php
// Connect to MySQL database
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "project1";

$conn = new mysqli($servername, $username, $password, $dbname);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Retrieve project details from database
$sql = "SELECT * FROM prevprojects";
$result = $conn->query($sql);

if ($result->num_rows > 0) {
    // Output data of each row
    while($row = $result->fetch_assoc()) {
        echo "<tr>";
        echo "<td>" . $row["projectName"] . "</td>";
        echo "<td>" . $row["description"] . "</td>";
        echo "<td>" . $row["groupMembers"] . "</td>";
        echo "<td>" . $row["contactNumber"] . "</td>";
        echo "<td>" . $row["references"] . "</td>";
        echo "</tr>";
    }
} else {
    echo "<tr><td colspan='5'>No projects found</td></tr>";
}

// Close MySQL connection
$conn->close();
?>
