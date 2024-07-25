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

    // Delete from upload table
    $query_delete_uploads = "DELETE u FROM upload u JOIN groups g ON u.group_id = g.group_id WHERE g.project_id = ?";
    $stmt_delete_uploads = $conn->prepare($query_delete_uploads);
    $stmt_delete_uploads->bind_param("i", $project_id);
    $stmt_delete_uploads->execute();

    // Delete existing groups
    $query_delete_groups = "DELETE FROM groups WHERE project_id = ?";
    $stmt_delete_groups = $conn->prepare($query_delete_groups);
    $stmt_delete_groups->bind_param("i", $project_id);
    $stmt_delete_groups->execute();


    // Reset auto-increment for groups table
$query_reset_auto_increment = "ALTER TABLE groups AUTO_INCREMENT = 1";
$stmt_reset_auto_increment = $conn->prepare($query_reset_auto_increment);
$stmt_reset_auto_increment->execute();

    // Commit transaction
    $conn->commit();
}

// Function to create groups
function createGroups($conn, $project_id, $group_size) {
    // Initialize an array to store group information
    $group_info = [];

    // Calculate number of groups needed
    $query = "SELECT COUNT(*) AS total_students FROM project_members WHERE project_id = ?";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("i", $project_id);
    $stmt->execute();
    $result = $stmt->get_result();
    $row = $result->fetch_assoc();
    $total_students = $row['total_students'];
    $num_groups = ceil($total_students / $group_size);

    // Fetch students for the project
    $query_students = "
        SELECT u.id, u.name 
        FROM users u 
        JOIN project_members pm ON u.id = pm.student_id 
        WHERE pm.project_id = ? AND u.role = 'student'
    ";
    $stmt_students = $conn->prepare($query_students);
    $stmt_students->bind_param("i", $project_id);
    $stmt_students->execute();
    $result_students = $stmt_students->get_result();
    $students = $result_students->fetch_all(MYSQLI_ASSOC);

    // Shuffle students randomly
    shuffle($students);

    // Initialize group index and group members array
    $group_index = 0;
    $group_members = [];

    // Begin transaction for atomicity
    $conn->begin_transaction();

    // Loop through shuffled students to create groups
    foreach ($students as $student) {
        // Add student to current group members array
        $group_members[] = $student;

        // Check if current group members array size equals group size
        if (count($group_members) == $group_size || count($group_members) + $group_index * $group_size >= $total_students) {
            // Insert group into database
            $query_insert_group = "
                INSERT INTO groups (coordinator_id, project_id, group_size) 
                VALUES (?, ?, ?)
            ";
            $stmt_insert_group = $conn->prepare($query_insert_group);
            $stmt_insert_group->bind_param("iii", $_SESSION['user_id'], $project_id, $group_size);
            $stmt_insert_group->execute();
            $group_id = $stmt_insert_group->insert_id;

            // Insert group members into database
            foreach ($group_members as $member) {
                $student_id = $member['id'];
                $query_insert_member = "
                    INSERT INTO group_members (group_id, student_id) 
                    VALUES (?, ?)
                ";
                $stmt_insert_member = $conn->prepare($query_insert_member);
                $stmt_insert_member->bind_param("ii", $group_id, $student_id);
                $stmt_insert_member->execute();
            }

            // Reset group members array and move to next group
            $group_members = [];
            $group_index++;
        }
    }

    // Commit transaction
    $conn->commit();

    // Retrieve created group information
    $query_fetch_groups = "
        SELECT g.group_id, g.group_size, GROUP_CONCAT(u.name SEPARATOR ', ') AS group_members
        FROM groups g
        JOIN group_members gm ON g.group_id = gm.group_id
        JOIN users u ON gm.student_id = u.id
        WHERE g.project_id = ? AND g.created_at >= NOW() - INTERVAL 1 HOUR
        GROUP BY g.group_id
    ";
    $stmt_fetch_groups = $conn->prepare($query_fetch_groups);
    $stmt_fetch_groups->bind_param("i", $project_id);
    $stmt_fetch_groups->execute();
    $result_groups = $stmt_fetch_groups->get_result();
    $group_info = $result_groups->fetch_all(MYSQLI_ASSOC);

    return $group_info;
}

// Get project_id and group_size from POST data
$project_id = $_POST['project_id'];
$group_size = $_POST['group_size'];

// Delete existing groups and members for the project
try {
    deleteExistingGroups($conn, $project_id);
} catch (Exception $e) {
    // Handle any exceptions or errors in deletion
    error_log("Error deleting existing groups: " . $e->getMessage());
    echo "Error deleting existing groups: " . $e->getMessage();
    exit();
}

// Attempt to create groups
$group_info = [];
try {
    $group_info = createGroups($conn, $project_id, $group_size);
} catch (Exception $e) {
    // Handle any exceptions or errors in group creation
    error_log("Error creating groups: " . $e->getMessage());
    echo "Error creating groups: " . $e->getMessage();
    exit();
}

// Redirect to success.php to show newly created groups
header("Location: success1.php?project_id=" . $project_id);
exit();
?>
