<?php
include 'db_connection.php';

$sql = "SELECT * FROM projects";
$result = $conn->query($sql);

$projects = array();
while ($row = $result->fetch_assoc()) {
    $projects[] = $row;
}

echo json_encode($projects);

$conn->close();
?>
