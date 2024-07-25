<?php
include 'db_connection.php';

$id = intval($_GET['id']);

$sql = "DELETE FROM projects WHERE id = $id";

if ($conn->query($sql) === TRUE) {
    echo json_encode(["success" => true]);
} else {
    echo json_encode(["success" => false, "error" => $conn->error]);
}

$conn->close();
?>
