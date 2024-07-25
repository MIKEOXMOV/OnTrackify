<?php
session_start();
include 'config.php';

$user_id = $_SESSION['user_id'];

// Query to check if the student is in any group
$stmt = $conn->prepare("SELECT group_id FROM group_members WHERE student_id = ?");
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows > 0) {
    // Student is a member of at least one group
    $row = $result->fetch_assoc();
    $group_id = $row['group_id'];
    $response = array('status' => 'member', 'group_id' => $group_id);
} else {
    // Student is not a member of any group
    $response = array('status' => 'not_member');
}

$stmt->close();
$conn->close();

echo json_encode($response);
?>