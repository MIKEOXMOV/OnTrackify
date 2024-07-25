<?php
// Database connection
$conn = new mysqli('localhost', 'root', '', 'profile_management');

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    if (isset($_FILES['profile_picture']) && $_FILES['profile_picture']['error'] == UPLOAD_ERR_OK) {
        $upload_dir = 'uploads/';
        $upload_file = $upload_dir . basename($_FILES['profile_picture']['name']);

        // Move the uploaded file to the server
        if (move_uploaded_file($_FILES['profile_picture']['tmp_name'], $upload_file)) {
            // Check if user_id is set in GET parameters
            if (isset($_GET['user_id'])) {
                $user_id = $_GET['user_id'];
                $stmt = $conn->prepare("UPDATE profile_details SET profile_picture = ? WHERE id = ?");
                $stmt->bind_param('si', $upload_file, $user_id);

                if ($stmt->execute()) {
                    echo "Profile picture updated successfully.";
                    // Redirect to the profile page or any other page
                    header("Location: profile.php?user_id=" . $user_id);
                    exit();
                } else {
                    echo "Error updating profile picture: " . $conn->error;
                }
                $stmt->close();
            } else {
                echo "User ID not provided.";
            }
        } else {
            echo "Error uploading file.";
        }
    } else {
        echo "No file uploaded or there was an upload error.";
    }
}

$conn->close();
?>
