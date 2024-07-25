<?php
session_start();

// Redirect if user is not logged in as coordinator
if (!isset($_SESSION['user_id']) || $_SESSION['role'] != 'coordinator') {
    header("Location: login.php");
    exit();
}

include 'config.php';

// Function to delete existing groups and their members for a project
function deleteExistingGroups($conn, $project_id) {
    // Begin transaction for atomicity
    $conn->begin_transaction();

    // Delete existing group members
    $query_delete_members = "DELETE gm FROM group_members gm JOIN groups g ON gm.group_id = g.group_id WHERE g.project_id = ?";
    $stmt_delete_members = $conn->prepare($query_delete_members);
    $stmt_delete_members->bind_param("i", $project_id);
    $stmt_delete_members->execute();

    // Delete existing groups
    $query_delete_groups = "DELETE FROM groups WHERE project_id = ?";
    $stmt_delete_groups = $conn->prepare($query_delete_groups);
    $stmt_delete_groups->bind_param("i", $project_id);
    $stmt_delete_groups->execute();

    // Commit transaction
    $conn->commit();
}

// Get project_id from POST data
$project_id = $_POST['project_id'];

// Delete existing groups and members for the project
try {
    deleteExistingGroups($conn, $project_id);
} catch (Exception $e) {
    // Handle any exceptions or errors in deletion
    error_log("Error deleting existing groups: " . $e->getMessage());
    // Redirect or display error message
    header("Location: error.php");
    exit();
}

// Redirect to process_create_groups.php to recreate groups
header("Location:create_groups1std.php?project_id=" . $project_id);
exit();
?>