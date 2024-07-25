<?php
session_start();

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

// Include database configuration
include 'config.php';

// Function to update guide_id in group_members table for a specific student_id and group_id
function updateGuideIdInGroupMembers($conn, $guide_id, $group_id) {
    // Start a transaction
    mysqli_begin_transaction($conn);

    // Update guide_id in group_members table for all students in the group
    $updateGroupMembersQuery = "UPDATE group_members SET guide_id = ? WHERE group_id = ?";
    $stmtUpdateGroupMembers = mysqli_prepare($conn, $updateGroupMembersQuery);
    if ($stmtUpdateGroupMembers) {
        mysqli_stmt_bind_param($stmtUpdateGroupMembers, 'ii', $guide_id, $group_id);
        mysqli_stmt_execute($stmtUpdateGroupMembers);

        if (mysqli_stmt_affected_rows($stmtUpdateGroupMembers) > 0) {
            // Commit transaction
            mysqli_commit($conn);
            mysqli_stmt_close($stmtUpdateGroupMembers); // Close statement
            return true; // Return true on successful update
        } else {
            mysqli_rollback($conn); // Rollback if update fails
            mysqli_stmt_close($stmtUpdateGroupMembers); // Close statement
            return false; // Return false if update failed
        }
    } else {
        mysqli_rollback($conn); // Rollback if prepare fails
        echo "Error preparing update statement for group_members table: " . mysqli_error($conn);
        return false; // Return false on error
    }
}

// Handle form submission to approve or reject requests
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['action'], $_POST['request_id'])) {
    $action = $_POST['action'];
    $request_id = $_POST['request_id'];

    // Retrieve guide_id from session
    $guide_id = $_SESSION['user_id'];

    // Retrieve student_id and group_id from requests table
    $getRequestDataQuery = "SELECT student_id, group_id FROM requests WHERE id = ?";
    $stmtGetRequestData = mysqli_prepare($conn, $getRequestDataQuery);
    mysqli_stmt_bind_param($stmtGetRequestData, 'i', $request_id);
    mysqli_stmt_execute($stmtGetRequestData);
    mysqli_stmt_bind_result($stmtGetRequestData, $student_id, $group_id);

    if (mysqli_stmt_fetch($stmtGetRequestData)) {
        mysqli_stmt_close($stmtGetRequestData); // Close statement

        // Check if the student is already assigned to a guide
        $checkExistingAssignmentQuery = "SELECT guide_id, group_id FROM group_members WHERE student_id = ?";
        $stmtCheckAssignment = mysqli_prepare($conn, $checkExistingAssignmentQuery);
        mysqli_stmt_bind_param($stmtCheckAssignment, 'i', $student_id);
        mysqli_stmt_execute($stmtCheckAssignment);
        mysqli_stmt_bind_result($stmtCheckAssignment, $existing_guide_id, $existing_group_id);
        mysqli_stmt_fetch($stmtCheckAssignment);

        if ($existing_guide_id === null || $existing_group_id === null) {
            // No existing assignment, proceed with update
            mysqli_stmt_close($stmtCheckAssignment); // Close statement

            // Update guide_id for all students in the group
            if (updateGuideIdInGroupMembers($conn, $guide_id, $group_id)) {
                // Update status in requests table
                $updateRequestQuery = "";
                if ($action === 'approve') {
                    $updateRequestQuery = "UPDATE requests SET status = 'approved' WHERE id = ?";
                } elseif ($action === 'reject') {
                    $updateRequestQuery = "UPDATE requests SET status = 'rejected' WHERE id = ?";
                }

                $stmtUpdateRequest = mysqli_prepare($conn, $updateRequestQuery);
                if ($stmtUpdateRequest) {
                    mysqli_stmt_bind_param($stmtUpdateRequest, 'i', $request_id);
                    mysqli_stmt_execute($stmtUpdateRequest);

                    // Check affected rows to confirm update
                    if (mysqli_stmt_affected_rows($stmtUpdateRequest) > 0) {
                        // Redirect to the same page after action
                        header("Location: ".$_SERVER['PHP_SELF']);
                        exit();
                    } else {
                        echo "Error updating request status.";
                    }

                    // Close statement
                    mysqli_stmt_close($stmtUpdateRequest);
                } else {
                    echo "Error preparing update statement for requests table: " . mysqli_error($conn);
                }
            } else {
                echo "Error updating guide_id in group_members table.";
            }
        } else {
            mysqli_stmt_close($stmtCheckAssignment); // Close statement
            echo "Student is already assigned to another guide in group " . $existing_group_id; // This should only execute if there is actually an existing guide_id in group_members
        }
    } else {
        echo "Error retrieving student_id and group_id.";
        mysqli_stmt_close($stmtGetRequestData); // Close statement
    }
}

// Fetch pending requests where guide_id is assigned to the logged-in guide and student is not already assigned to any guide
$guide_id = $_SESSION['user_id'];
$requestsQuery = "SELECT r.id AS request_id, r.group_id, r.created_at AS request_date
                  FROM requests r
                  WHERE r.status = 'pending'
                  AND r.guide_id = ?
                  OR NOT EXISTS (
                      SELECT 1 FROM group_members gm WHERE gm.student_id = r.student_id
                  )";
$stmt = mysqli_prepare($conn, $requestsQuery);
if ($stmt) {
    mysqli_stmt_bind_param($stmt, 'i', $guide_id);
    mysqli_stmt_execute($stmt);
    mysqli_stmt_store_result($stmt); // Store result to free memory
    mysqli_stmt_bind_result($stmt, $request_id, $group_id, $request_date);

    $requests = [];
    while (mysqli_stmt_fetch($stmt)) {
        // Fetch student names for the group
        $studentsQuery = "SELECT u.name FROM group_members gm
                          INNER JOIN users u ON gm.student_id = u.id
                          WHERE gm.group_id = ?";
        $stmtStudents = mysqli_prepare($conn, $studentsQuery);
        mysqli_stmt_bind_param($stmtStudents, 'i', $group_id);
        mysqli_stmt_execute($stmtStudents);
        mysqli_stmt_store_result($stmtStudents); // Store result to free memory
        mysqli_stmt_bind_result($stmtStudents, $student_name);

        $student_names = [];
        while (mysqli_stmt_fetch($stmtStudents)) {
            $student_names[] = $student_name;
        }
        mysqli_stmt_close($stmtStudents);

        $requests[] = [
            'request_id' => $request_id,
            'group_id' => $group_id,
            'student_names' => $student_names,
            'request_date' => $request_date
        ];
    }

    // Close statement
    mysqli_stmt_close($stmt);
} else {
    echo "Error: " . mysqli_error($conn);
}

mysqli_close($conn);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Pending Requests Panel</title>
    <!-- Include any necessary CSS stylesheets -->
    <style>
        .requests-table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
        }
        .requests-table th, .requests-table td {
            border: 1px solid #ccc;
            padding: 8px;
            text-align: left;
        }
        .requests-table th {
            background-color: #f2f2f2;
        }
        .requests-table button {
            padding: 5px 10px;
            cursor: pointer;
        }
        .requests-table button.approve-btn {
            background-color: #4CAF50;
            color: white;
            border: none;
            border-radius: 3px;
        }
        .requests-table button.reject-btn {
            background-color: #f44336;
            color: white;
            border: none;
            border-radius: 3px;
        }
        .requests-table button:hover {
            opacity: 0.8;
        }
        .back-button {
            text-decoration: none;
            background-color: #007bff;
            color: #fff;
            padding: 10px 20px;
            border-radius: 5px;
            border: none;
            cursor: pointer;
        }
        .header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            background-color: #fff;
            padding: 10px 20px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
        }
        .logo {
            display: flex;
            align-items: center;
        }

        .logo img {
            height: 40px;
            margin-right: 10px;
        }

        .logo-name {
            font-size: 24px;
            font-weight: bold;
            color: #333;
        }
    </style>
</head>
<body>
    <div class="header">
        <div class="logo">
            <img src="logo.png" alt="Logo">
            <span class="logo-name">OnTrackify</span>
        </div>
        <a href="guide_panel.php" class="back-button">Back</a>
    </div>
    <h1>Pending Requests to Join Groups</h1>

    <div id="requestsList">
        <table class="requests-table">
            <thead>
                <tr>
                    <th>Group ID</th>
                    <th>Students</th>
                    <th>Request Date</th>
                    <th>Action</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($requests as $request): ?>
                    <tr>
                        <td><?php echo $request['group_id']; ?></td>
                        <td><?php echo implode(', ', array_map('htmlspecialchars', $request['student_names'])); ?></td>
                        <td><?php echo $request['request_date']; ?></td>
                        <td>
                            <form action="<?php echo htmlspecialchars($_SERVER['PHP_SELF']); ?>" method="post">
                                <input type="hidden" name="request_id" value="<?php echo $request['request_id']; ?>">
                                <button type="submit" name="action" value="approve" class="approve-btn">Approve</button>
                                <button type="submit" name="action" value="reject" class="reject-btn">Reject</button>
                            </form>
                        </td>
                    </tr>
                <?php endforeach; ?>
                <?php if (empty($requests)): ?>
                    <tr>
                        <td colspan="4">No pending requests to display</td>
                    </tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>

    <!-- Include any necessary JavaScript files -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>
</body>
</html>
