<?php
session_start();
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "role_management";

// Create connection
$conn = new mysqli($servername, $username, $password, $dbname);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

$user_id = $_SESSION['user_id'];
$sql = "SELECT u.name, u.email, u.register_or_faculty_id, u.role";

// Determine if user is a student, coordinator, or guide
if ($_SESSION['role'] === 'student') {
    $sql .= ", s.department, s.semester, s.college_name, s.batch";
    $sql .= " FROM users u LEFT JOIN students s ON u.id = s.user_id WHERE u.id = $user_id";
} elseif ($_SESSION['role'] === 'coordinator') {
    $sql .= ", c.department, c.college_name";
    $sql .= " FROM users u LEFT JOIN coordinators c ON u.id = c.user_id WHERE u.id = $user_id";
} elseif ($_SESSION['role'] === 'guide') {
    $sql .= ", g.department, g.college_name";
    $sql .= " FROM users u LEFT JOIN guides g ON u.id = g.user_id WHERE u.id = $user_id";
} else {
    echo "Role not recognized.";
    exit();
}

$result = $conn->query($sql);

if ($result->num_rows > 0) {
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

$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Update Profile</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <style>
        .container {
            max-width: 800px;
            margin-top: 50px;
        }
        .card {
            border: 1px solid #ddd;
            border-radius: 8px;
            padding: 20px;
        }
        .card-header {
            text-align: center;
            background-color: #007bff;
            color: white;
            border-radius: 8px 8px 0 0;
        }
        .form-group label {
            font-weight: bold;
        }
        .form-group {
            display: flex;
            flex-wrap: wrap;
        }
        .form-group > div {
            flex: 1;
            min-width: 200px;
            padding: 10px;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="card">
            <div class="card-header">
                <h3>Update Profile</h3>
            </div>
            <div class="card-body">
                <form action="save_profile.php" method="post">
                    <input type="hidden" name="role" value="<?php echo htmlspecialchars($role); ?>">
                    <div class="form-group">
                        <div>
                            <label for="name">Name:</label>
                            <input type="text" class="form-control" id="name" name="name" value="<?php echo htmlspecialchars($name); ?>">
                        </div>
                        <div>
                            <label for="email">Email:</label>
                            <input type="email" class="form-control" id="email" name="email" value="<?php echo htmlspecialchars($email); ?>">
                        </div>
                    </div>
                    <div class="form-group">
                        <div>
                            <label for="register_or_faculty_id">Register Number/Faculty ID:</label>
                            <input type="text" class="form-control" id="register_or_faculty_id" name="register_or_faculty_id" value="<?php echo htmlspecialchars($register_or_faculty_id); ?>">
                        </div>
                    </div>
                    <?php if ($role == 'student'): ?>
                        <div class="form-group">
                            <div>
                                <label for="department">Department:</label>
                                <input type="text" class="form-control" id="department" name="department" value="<?php echo htmlspecialchars($department); ?>">
                            </div>
                            <div>
                                <label for="semester">Semester:</label>
                                <input type="text" class="form-control" id="semester" name="semester" value="<?php echo htmlspecialchars($semester); ?>">
                            </div>
                        </div>
                        <div class="form-group">
                            <div>
                                <label for="college_name">College Name:</label>
                                <input type="text" class="form-control" id="college_name" name="college_name" value="<?php echo htmlspecialchars($college_name); ?>">
                            </div>
                            <div>
                                <label for="batch">Batch:</label>
                                <input type="text" class="form-control" id="batch" name="batch" value="<?php echo htmlspecialchars($batch); ?>">
                            </div>
                        </div>
                    <?php elseif ($role == 'coordinator' || $role == 'guide'): ?>
                        <div class="form-group">
                            <div>
                                <label for="department">Department:</label>
                                <input type="text" class="form-control" id="department" name="department" value="<?php echo htmlspecialchars($department); ?>">
                            </div>
                            <div>
                                <label for="college_name">College Name:</label>
                                <input type="text" class="form-control" id="college_name" name="college_name" value="<?php echo htmlspecialchars($college_name); ?>">
                            </div>
                        </div>
                    <?php endif; ?>
                    <button type="submit" class="btn btn-primary btn-block">Save Changes</button>
                </form>
            </div>
        </div>
    </div>
</body>
</html>
