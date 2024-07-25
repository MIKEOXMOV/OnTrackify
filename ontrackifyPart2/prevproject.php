<?php
// Initialize variables for alert message
$alertMessage = "";

// Check if form is submitted
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Connect to MySQL database
    $servername = "localhost";
    $username = "root";
    $password = "";
    $dbname = "project1";

    $conn = new mysqli($servername, $username, $password, $dbname);

    if ($conn->connect_error) {
        die("Connection failed: " . $conn->connect_error);
    }

    // Get form data
    $projectName = $_POST["projectName"];
    $description = $_POST["description"];
    $groupMembers = $_POST["groupMembers"];
    $contactNumber = $_POST["contactNumber"];
    $references = $_POST["references"];

    // Insert project details into database
    $sql = "INSERT INTO prevprojects (projectName, description, groupMembers, contactNumber, `references`)
            VALUES ('$projectName', '$description', '$groupMembers', '$contactNumber', '$references')";

    if ($conn->query($sql) === TRUE) {
        // Set alert message for successful upload
        $alertMessage = "Project details uploaded successfully";
    } else {
        // Set alert message for upload error
        $alertMessage = "Error: " . $sql . "<br>" . $conn->error;
    }

    // Close MySQL connection
    $conn->close();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Upload Project Details</title>
    <link rel="stylesheet" href="prevstyle.css">
    <script>
        // JavaScript function to display alert message
        function showAlert(message) {
            alert(message);
        }
    </script>
    <style> /* CSS for button styles */
.btn {
    padding: 10px 20px;
    background-color: #3498db; /* Blue background color */
    color: white;
    border: none;
    border-radius: 50px;
    cursor: pointer;
    text-decoration: none; /* Remove default underline */
    display: inline-block; /* Ensure button is displayed inline */
}

.btn:hover {
    background-color: #2980b9; /* Darker blue on hover */
} .back-button {
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
            <div class="logo-name">OnTrackify</div>
        </div>
        <a href="showprevtable.php" class="back-button">Back</a>
    </div>
</div>
    <div class="container">
        <h2>Enter Your Project Details</h2>
        <!-- Display alert message if set -->
        <?php if (!empty($alertMessage)) : ?>
            <script>
                // Call JavaScript function to display alert message
                showAlert("<?php echo $alertMessage; ?>");
            </script>
        <?php endif; ?>

        <form action="<?php echo $_SERVER['PHP_SELF']; ?>" method="post">
            <label for="projectName">Project Name:</label>
            <input type="text" id="projectName" name="projectName" required><br>

            <label for="description">Description:</label>
            <textarea id="description" name="description" rows="4" required></textarea><br>

            <label for="groupMembers">Group Members:</label>
            <input type="text" id="groupMembers" name="groupMembers" required><br>

            <label for="contactNumber">Contact Number:</label>
            <input type="text" id="contactNumber" name="contactNumber" required><br>

            <label for="references">References:</label>
            <textarea id="references" name="references" rows="4" required></textarea><br>

            <input type="submit" value="Add">
            <button class="btn" onclick="viewPage()">View</button> <!-- Button for viewing -->
 <!-- JavaScript function to redirect to view_page.php -->
 <script>
    function viewPage() {
        window.location.href = "showprevtable.php";
    }
    </script>

        </form>
    </div>
   
</body>
</html>
