<?php
session_start();

// Check if user is not logged in, redirect to login page
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

// Check if form is submitted via POST method
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Include database connection
    include 'config.php';

    // Validate idea ID
    if (isset($_GET['id'])) {
        $idea_id = $_GET['id'];

        // Delete idea query
        $deleteQuery = "DELETE FROM ideas WHERE id = ?";
        
        // Prepare statement
        $stmt = mysqli_prepare($conn, $deleteQuery);

        if ($stmt) {
            // Bind parameters
            mysqli_stmt_bind_param($stmt, "i", $idea_id);

            // Execute statement
            mysqli_stmt_execute($stmt);

            // Close statement
            mysqli_stmt_close($stmt);

            // Close connection
            mysqli_close($conn);

            // Redirect back to idea submission form with success message
            header("Location: idea_submission_form.php?delete=success");
            exit();
        } else {
            // Error in preparing statement
            echo "Error deleting idea.";
        }
    } else {
        // Idea ID not provided
        echo "Invalid request.";
    }
} else {
    // Handle invalid request method (not POST)
    echo "Invalid request method.";
}
?>
