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

// Close the database connection
$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Profile</title>
    <!-- Include Bootstrap CSS or your preferred CSS -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/twitter-bootstrap/4.1.3/css/bootstrap.min.css" rel="stylesheet">
    <!-- Include Font Awesome CSS -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.12.1/css/all.min.css">
    <!-- Include your custom CSS -->
    <link rel="stylesheet" href="css/style.css">
</head>
<body>
    <div class="ScriptTop">
        <div class="rt-container">
            <div class="col-rt-4" id="float-right">
                <!-- Ad Here -->
            </div>
            <div class="col-rt-2">
                <ul>
                    <!-- Link back to the home or dashboard page -->
                    <li><a href="<?php echo $_SESSION['role'] === 'student' ? 'student_panel.php' : ($_SESSION['role'] === 'coordinator' ? 'coordinator_panel.php' : 'guide_panel.php'); ?>" title="Back to Home">Back to Home</a></li>
                </ul>
            </div>
        </div>
    </div>

    <header class="ScriptHeader">
        <div class="rt-container">
            <div class="col-rt-12">
                <div class="rt-heading">
                    <h1>Profile</h1>
                    <p>Welcome, <?php echo $name; ?>!</p>
                </div>
            </div>
        </div>
    </header>

    <section>
        <div class="rt-container">
            <div class="col-rt-12">
                <div class="Scriptcontent">
                    <div class="student-profile py-4">
                        <div class="container">
                            <div class="row">
                                <div class="col-lg-4">
                                    <div class="card shadow-sm">
                                        <div class="card-header bg-transparent text-center">
                                            <!-- Use your profile image or placeholder -->
                                            <img class="profile_img" src="https://via.placeholder.com/300x200" alt="Profile Picture">
                                            <h3><?php echo $name; ?></h3>
                                        </div>
                                        <div class="card-body">
                                            <p class="mb-0"><strong class="pr-1">Email:</strong><?php echo $email; ?></p>
                                            <p class="mb-0"><strong class="pr-1">Register Number/Faculty ID:</strong><?php echo $register_or_faculty_id; ?></p>
                                            <p class="mb-0"><strong class="pr-1">Role:</strong><?php echo $role; ?></p>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-lg-8">
                                    <div class="card shadow-sm">
                                        <div class="card-header bg-transparent border-0">
                                            <h3 class="mb-0"><i class="far fa-clone pr-1"></i>Additional Details</h3>
                                        </div>
                                        <div class="card-body pt-0">
                                            <table class="table table-bordered">
                                                <?php if ($_SESSION['role'] === 'student') { ?>
                                                <tr>
                                                    <th width="30%">Department</th>
                                                    <td width="2%">:</td>
                                                    <td><?php echo $department; ?></td>
                                                </tr>
                                                <tr>
                                                    <th width="30%">Semester</th>
                                                    <td width="2%">:</td>
                                                    <td><?php echo $semester; ?></td>
                                                </tr>
                                                <tr>
                                                    <th width="30%">College Name</th>
                                                    <td width="2%">:</td>
                                                    <td><?php echo $college_name; ?></td>
                                                </tr>
                                                <tr>
                                                    <th width="30%">Batch</th>
                                                    <td width="2%">:</td>
                                                    <td><?php echo $batch; ?></td>
                                                </tr>
                                                <?php } elseif ($_SESSION['role'] === 'coordinator') { ?>
                                                <tr>
                                                    <th width="30%">Department</th>
                                                    <td width="2%">:</td>
                                                    <td><?php echo $department; ?></td>
                                                </tr>
                                                <tr>
                                                    <th width="30%">College Name</th>
                                                    <td width="2%">:</td>
                                                    <td><?php echo $college_name; ?></td>
                                                </tr>
                                                <?php } elseif ($_SESSION['role'] === 'guide') { ?>
                                                <tr>
                                                    <th width="30%"> Department</th>
                                                    <td width="2%">:</td>
                                                    <td><?php echo $department; ?></td>
                                                </tr>
                                                <tr>
                                                    <th width="30%">College Name</th>
                                                    <td width="2%">:</td>
                                                    <td><?php echo $college_name; ?></td>
                                                </tr>
                                                <?php } ?>
                                            </table>
                                            <!-- Update button to go to profile update form -->
                                            <a href="profile_update.php" class="btn btn-primary">Update Profile</a>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Include necessary scripts here -->

</body>
</html>
