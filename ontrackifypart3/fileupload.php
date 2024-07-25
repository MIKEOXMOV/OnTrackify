<?php
session_start(); // Start session for user authentication

// Database connection
$conn = new PDO('mysql:host=localhost;dbname=role_management', 'root', '');

// Check if the form is submitted
if (isset($_POST['submit'])) {
    $name = $_FILES['file']['name'];
    $fname = date("YmdHis") . '_' . $name;
    $temp = $_FILES['file']['tmp_name'];

    // Check if user is logged in and has a valid session
    if (isset($_SESSION['user_id']) && !empty($_SESSION['user_id'])) {
        $user_id = $_SESSION['user_id'];
    } else {
        die("User session not found. Please log in.");
    }

    // Fetch the group_id for the logged-in user
    $stmt = $conn->prepare("SELECT group_id FROM group_members WHERE student_id = :student_id");
    $stmt->bindParam(':student_id', $user_id);
    $stmt->execute();
    $result = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($result) {
        $group_id = $result['group_id'];
    } else {
        die("Group ID not found for the user. Please check the group_members table.");
    }

    // Move uploaded file to upload directory
    $upload_directory = "uploads/"; // Directory where files will be uploaded
    $move = move_uploaded_file($temp, $upload_directory . $fname);

    if ($move) {
        // Insert uploaded file details into database with group ID
        $query = $conn->prepare("INSERT INTO upload (name, fname, student_id, group_id) VALUES (:name, :fname, :student_id, :group_id)");
        $query->bindParam(':name', $name);
        $query->bindParam(':fname', $fname);
        $query->bindParam(':student_id', $user_id);
        $query->bindParam(':group_id', $group_id);
        
        if ($query->execute()) {
            // Redirect after successful upload
            header("Location: fileupload.php");
            exit;
        } else {
            die("An error occurred: Unable to execute query. Please try again later.");
        }
    } else {
        die("File upload failed. Please try again.");
    }
}

// Check if delete request is made
if (isset($_GET['delete'])) {
    $delete_id = $_GET['delete'];
    // Check if the logged-in user is the owner of the file
    $stmt = $conn->prepare("SELECT fname FROM upload WHERE id = :id AND student_id = :student_id");
    $stmt->bindParam(':id', $delete_id);
    $stmt->bindParam(':student_id', $_SESSION['user_id']);
    $stmt->execute();
    $file = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($file) {
        // Delete the file from the server
        unlink("uploads/" . $file['fname']);
        // Delete the record from the database
        $stmt = $conn->prepare("DELETE FROM upload WHERE id = :id AND student_id = :student_id");
        $stmt->bindParam(':id', $delete_id);
        $stmt->bindParam(':student_id', $_SESSION['user_id']);
        $stmt->execute();
        // Redirect after successful deletion
        header("Location: fileupload.php");
        exit;
    } else {
        die("You do not have permission to delete this file.");
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>File Upload</title>
    <!-- Include Bootstrap CSS -->
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/css/bootstrap.min.css">
    <style>
        .form-group {
            margin-top: 50px;
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
            <div class="logo-name">OnTrackify</div>
        </div>
        <a href="group_panel.php" class="back-button">Back</a>
    </div>
</div>


    <div class="container">
        <!-- Upload form -->
        <form enctype="multipart/form-data" action="" method="post">
            <div class="form-group">
                <label for="file">Select File:</label >
                <input type="file" class="form-control-file" id="file" name="file">
            </div>
            <button type="submit" name="submit" class="btn btn-primary">Submit</button>
        </form>
        
        <hr>
        <br>

        <!-- Display uploaded files for the logged-in user/group -->
        <table class="table table-striped table-bordered">
            <thead>
                <tr>
                    <th width="50%" align="center">File Name</th>
                    <th width="30%" align="center">Uploaded By</th>
                    <th align="center">Action</th>
                </tr>
            </thead>
            <tbody>
                <?php
                // Fetch files uploaded by the logged-in user or other students in the same group
                $query_str = "
                    SELECT upload.id, upload.name AS file_name, upload.fname, users.name AS student_name, upload.student_id
                    FROM upload 
                    JOIN users ON upload.student_id = users.id 
                    WHERE upload.group_id IN (SELECT group_id FROM group_members WHERE student_id = :student_id)
                    ORDER BY upload.id DESC";
                $query = $conn->prepare($query_str);
                $query->bindParam(':student_id', $_SESSION['user_id']);
                $query->execute();

                while ($row = $query->fetch(PDO::FETCH_ASSOC)) {
                    $file_name = $row['file_name'];
                    $fname = $row['fname'];
                    $student_name = $row['student_name'];
                    $file_id = $row['id'];
                    $student_id = $row['student_id'];
                ?>
                <tr>
                    <td><?php echo htmlspecialchars($file_name); ?></td>
                    <td><?php echo htmlspecialchars($student_name); ?></td>
                    <td>
                        <a href="download.php?filename=<?php echo urlencode($file_name); ?>&f=<?php echo urlencode($fname); ?>" class="btn btn-success">Download</a>
                        <?php if ($student_id == $_SESSION['user_id']) { ?>
                            <a href="fileupload.php?delete=<?php echo $file_id; ?>" class="btn btn-danger">Delete</a>
                        <?php } ?>
                    </td>
                </tr>
                <?php } ?>
            </tbody>
        </table>

        <!-- Back button to group panel -->
        
    </div>

</body>
</html>
