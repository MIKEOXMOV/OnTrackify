<?php
include 'db_connection.php';

$id = intval($_GET['id']);

$sql = "SELECT * FROM projects WHERE id = $id";
$result = $conn->query($sql);

if ($result->num_rows > 0) {
    echo json_encode($result->fetch_assoc());
} else {
    echo json_encode(["error" => "Project not found"]);
}

$conn->close();
?>
