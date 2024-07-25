<?php
session_start(); // Start session if not already started

// Check if user is logged in, otherwise redirect to login page
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

// Database connection parameters
$servername = "localhost";
$username = "root"; // Your MySQL username
$password = ""; // Your MySQL password
$dbname = "role_management"; // Your database name

// Create connection
$conn = new mysqli($servername, $username, $password, $dbname);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Fetch user data based on session user ID
$user_id = $_SESSION['user_id'];
$sql = "SELECT u.name, u.email, u.register_or_faculty_id, u.role";

// Determine if user is a student, coordinator, or guide
if ($_SESSION['role'] === 'student') {
    // Fetch additional details for students
    $sql .= ", s.department, s.semester, s.college_name, s.batch";
    $sql .= " FROM users u LEFT JOIN students s ON u.id = s.user_id WHERE u.id = $user_id";
} elseif ($_SESSION['role'] === 'coordinator') {
    // Fetch additional details for coordinators
    $sql .= ", c.department, c.college_name";
    $sql .= " FROM users u LEFT JOIN coordinators c ON u.id = c.user_id WHERE u.id = $user_id";
} elseif ($_SESSION['role'] === 'guide') {
    // Fetch additional details for guides
    $sql .= ", g.department, g.college_name";
    $sql .= " FROM users u LEFT JOIN guides g ON u.id = g.user_id WHERE u.id = $user_id";
} else {
    // Handle other roles if needed
    echo "Role not recognized.";
    exit();
}

$result = $conn->query($sql);

if ($result->num_rows > 0) {
    // User found, fetch and display user details
    $row = $result->fetch_assoc();
    $name = $row["name"];
    $email = $row["email"];
    $register_or_faculty_id = $row["register_or_faculty_id"];
    $role = $row["role"];
    // Additional details based on role
    $department = isset($row["department"]) ? $row["department"] : '';
    $semester = isset($row["semester"]) ? $row["semester"] : '';
    $college_name = isset($row["college_name"]) ? $row["college_name"] : '';
    $batch = isset($row["batch"]) ? $row["batch"] : '';
} else {
    echo "User profile not found.";
    exit(); // Exit if user profile not found
}

// Fetch group details and members if user is a student
$groupMembers = [];
$guideName = '';

if ($_SESSION['role'] === 'student') {
    $groupIdQuery = "SELECT group_id FROM group_members WHERE student_id = $user_id";
    $groupIdResult = $conn->query($groupIdQuery);

    if ($groupIdResult->num_rows > 0) {
        $groupIdRow = $groupIdResult->fetch_assoc();
        $groupId = $groupIdRow['group_id'];

        // Fetch group members
        $groupMembersQuery = "SELECT u.id AS student_id, u.name AS member_name, u.register_or_faculty_id AS member_register_id FROM group_members gm JOIN users u ON gm.student_id = u.id WHERE gm.group_id = $groupId";
        $groupMembersResult = $conn->query($groupMembersQuery);

        if ($groupMembersResult->num_rows > 0) {
            while ($memberRow = $groupMembersResult->fetch_assoc()) {
                $groupMembers[] = [
                    'student_id' => $memberRow['student_id'],
                    'member_name' => $memberRow['member_name'],
                    'member_register_id' => $memberRow['member_register_id']
                ];
            }
        } else {
            $groupMembers[] = ["student_id" => "", "member_name" => "No group members found.", "member_register_id" => ""];
        }

        // Fetch guide if assigned
        $guideQuery = "SELECT u.name AS guide_name FROM group_members gm JOIN users u ON gm.guide_id = u.id WHERE gm.group_id = $groupId LIMIT 1";
        $guideResult = $conn->query($guideQuery);

        if ($guideResult->num_rows > 0) {
            $guideRow = $guideResult->fetch_assoc();
            $guideName = $guideRow['guide_name'];
        } else {
            $guideName = "No guide assigned.";
        }
    } else {
        $groupMembers[] = ["student_id" => "", "member_name" => "Not assigned to any group.", "member_register_id" => ""];
    }
}

// Close the database connection
$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Profile and Group Details</title>
    <!-- Include Bootstrap CSS or your preferred CSS -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/twitter-bootstrap/4.1.3/css/bootstrap.min.css" rel="stylesheet">
    <!-- Include Font Awesome CSS -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.12.1/css/all.min.css">
    <!-- Include your custom CSS -->
    <style>
        body {
            background-color: #f8f9fa;
            font-family: Arial, sans-serif;
        }
        .container {
            max-width: 800px;
            margin: 0 auto;
            padding: 20px;
        }
        .card {
            border: none;
            border-radius: 10px;
            box-shadow: 0 4px 8px rgba(0,0,0,0.1);
            margin-bottom: 20px;
        }
        .card-header {
            background-color: #007bff;
            color: #fff;
            border-radius: 10px 10px 0 0;
            padding: 10px 20px;
        }
        .card-body {
            padding: 20px;
        }
        .card-title {
            font-size: 1.5rem;
            margin-bottom: 20px;
        }
        .card-subtitle {
            color: #6c757d;
            margin-bottom: 10px;
        }
        .list-group-item {
            border: none;
            padding: 10px 0;
        }
        .list-group-item:first-child {
            border-top: none;
        }
        .list-group-item:last-child {
            border-bottom: none;
        }
        .btn {
            background-color: #007bff;
            color: #fff;
            border: none;
            border-radius: 5px;
            padding: 10px 20px;
            text-decoration: none;
            transition: background-color 0.3s ease;
        }
        .btn:hover {
            background-color: #0056b3;
        }
        .logo {
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
    <div class="container">
        <header class="header mb-4">
            <div class="d-flex align-items-center justify-content-between">
                <div>
                    <img src="logo.png" alt="Logo" class="logo">
                    <span class="logo-name">OnTrackify</span>
                </div>
                <a href="<?php echo $_SESSION['role'] === 'student' ? 'group_panel.php' : ($_SESSION['role'] === 'coordinator' ? 'coordinator_panel.php' : 'guide_panel.php'); ?>" class="btn">Back to Home</a>
            </div>
        </header>
        <div class="card">
            <div class="card-header">
                <h3 class="card-title">Group Details and Members</h3>
            </div>
            <div class="card-body">
                <?php if ($_SESSION['role'] === 'student'): ?>
                    <h4 class="card-subtitle mb-3">Group Members:</h4>
                    <ul class="list-group">
                        <?php foreach ($groupMembers as $member): ?>
                            <li class="list-group-item">
                                <strong>Name:</strong> <?php echo $member['member_name']; ?><br>
                                <strong>Register ID:</strong> <?php echo $member['member_register_id']; ?>
                            </li>
                        <?php endforeach; ?>
                    </ul>
                    <h4 class="card-subtitle mt-4">Guide:</h4>
                    <p><?php echo $guideName; ?></p>
                <?php else: ?>
                    <p class="text-center">You are not assigned to any group.</p>
                <?php endif; ?>
            </div>
        </div>
    </div>
</body>
</html>