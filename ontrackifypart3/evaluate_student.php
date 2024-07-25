<?php
session_start();
include 'config.php';

// Ensure only coordinators can access this page
if ($_SESSION['role'] != 'coordinator') {
    header("Location: login.php");
    exit();
}

// Retrieve student_id and project_id from GET parameters
if (isset($_GET['student_id']) && isset($_GET['project_id'])) {
    $student_id = $_GET['student_id'];
    $project_id = $_GET['project_id'];
} else {
    // Redirect to an appropriate page or handle the error scenario
    header("Location: display_project.php");
    exit();
}

// Fetch student details including group_id
$student_query = "SELECT u.id, u.name, u.email, u.register_or_faculty_id, gm.group_id
                  FROM users u
                  LEFT JOIN group_members gm ON u.id = gm.student_id
                  WHERE u.id = ?";
$stmt_student = $conn->prepare($student_query);
$stmt_student->bind_param("i", $student_id);
$stmt_student->execute();
$student_result = $stmt_student->get_result();
$student = $student_result->fetch_assoc();

// Fetch project details
$project_query = "SELECT * FROM projects WHERE id = ?";
$stmt_project = $conn->prepare($project_query);
$stmt_project->bind_param("i", $project_id);
$stmt_project->execute();
$project_result = $stmt_project->get_result();
$project = $project_result->fetch_assoc();

// Initialize variables for marks
$attendance = $guide_marks = $project_report = $review1_total_marks = $review2_total_marks = $average_review_marks = $final_cie_mark = 0;
$success_message = $error_message = '';

// Check if form is submitted
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $attendance = $_POST['attendance'];
    $project_report = $_POST['project_report'];
    $review1_total_marks = $_POST['review1_total_marks'];
    $review2_total_marks = $_POST['review2_total_marks'];

    // Calculate average review marks
    $average_review_marks = ($review1_total_marks + $review2_total_marks) / 2;

    // Fetch existing guide marks if available
    $guide_marks_query = "SELECT marks FROM guide_marks WHERE student_id = ? AND project_id = ?";
    $stmt_guide_marks = $conn->prepare($guide_marks_query);
    $stmt_guide_marks->bind_param("ii", $student_id, $project_id);
    $stmt_guide_marks->execute();
    $guide_marks_result = $stmt_guide_marks->get_result();

    if ($guide_marks_result->num_rows > 0) {
        // Guide marks already exist, fetch and retain them
        $guide_marks_row = $guide_marks_result->fetch_assoc();
        $guide_marks = $guide_marks_row['marks']; // Retrieve the 'marks' value
    } else {
        // Handle case where guide marks are not set
        $guide_marks = 0; // Default value if guide marks are not set
    }

    // Calculate final_cie_mark as the total of average review marks, guide marks, attendance, and project report
    $final_cie_mark = $average_review_marks + $guide_marks + $attendance + $project_report;
    $final_cie_mark = min($final_cie_mark, 75); // Ensure it totals out of 75

    // Update student marks in the student_marks table
    $update_sql = "UPDATE student_marks
                   SET attendance = ?,
                       project_report = ?,
                       review1_total_marks = ?,
                       review2_total_marks = ?,
                       average_review_marks = ?,
                       final_cie_mark = ?
                   WHERE student_id = ? AND project_id = ?";

    $stmt_update = $conn->prepare($update_sql);
    $stmt_update->bind_param("iiiidiii",
                             $attendance,
                             $project_report,
                             $review1_total_marks,
                             $review2_total_marks,
                             $average_review_marks,
                             $final_cie_mark,
                             $student_id,
                             $project_id);

    if ($stmt_update->execute()) {
        // Success message
        $success_message = "Student evaluation updated successfully.";
    } else {
        // Error message
        $error_message = "Error updating student evaluation: " . $conn->error;
    }

    $stmt_update->close();
} else {
    // Fetch existing marks if available
    $marks_query = "SELECT * FROM student_marks WHERE student_id = ? AND project_id = ?";
    $stmt_marks = $conn->prepare($marks_query);
    $stmt_marks->bind_param("ii", $student_id, $project_id);
    $stmt_marks->execute();
    $marks_result = $stmt_marks->get_result();

    if ($marks_result->num_rows > 0) {
        // Marks already exist, fetch and display them
        $marks_row = $marks_result->fetch_assoc();
        $attendance = $marks_row['attendance'];
        $project_report = $marks_row['project_report'];
        $review1_total_marks = $marks_row['review1_total_marks'];
        $review2_total_marks = $marks_row['review2_total_marks'];

        // Calculate average review marks
        $average_review_marks = ($review1_total_marks + $review2_total_marks) / 2;

        // Calculate final_cie_mark
        $final_cie_mark = $marks_row['final_cie_mark'];
    }

    // Fetch guide marks separately
    $guide_marks_query = "SELECT marks FROM guide_marks WHERE student_id = ? AND project_id = ?";
    $stmt_guide_marks = $conn->prepare($guide_marks_query);
    $stmt_guide_marks->bind_param("ii", $student_id, $project_id);
    $stmt_guide_marks->execute();
    $guide_marks_result = $stmt_guide_marks->get_result();

    if ($guide_marks_result->num_rows > 0) {
        $guide_marks_row = $guide_marks_result->fetch_assoc();
        $guide_marks = $guide_marks_row['marks']; // Retrieve the 'marks' value
    } else {
        // Handle case where guide marks are not set
        $guide_marks = 0; // Default value if guide marks are not set
    }

    $stmt_guide_marks->close();
}

// Determine if guide marks input should be disabled based on existing marks
$disable_guide_marks = ($guide_marks > 0) ? 'disabled' : '';

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Evaluate Student</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <style>
        .navbar-brand {
            display: flex;
            align-items: center;
        }
        .navbar-brand img {
            margin-right: 10px;
        }
        .back-button {
            position: absolute;
            top: 10px;
            right: 10px;
        }
        .card {
            margin-top: 20px;
            background-color: lightblue; /* Blue background */
            color: black; /* White text */
        }
        .form-group {
            margin-bottom: 15px;
        }
        .form-group label {
            font-weight: bold;
        }
        .form-control {
            border-radius: 0;
        }
        .btn-primary {
            border-radius: 0;
        }
        .btn-secondary {
            border-radius: 0;
        }
    </style>
    <script>
        function calculateTotalCieMarks() {
            let attendance = parseFloat(document.getElementById('attendance').value) || 0;
            let guide_marks = parseFloat(document.getElementById('guide_marks').value) || 0;
            let project_report = parseFloat(document.getElementById('project_report').value) || 0;
            let review1_total_marks = parseFloat(document.getElementById('review1_total_marks').value) || 0;
            let review2_total_marks = parseFloat(document.getElementById('review2_total_marks').value) || 0;

            let average_review_marks = (review1_total_marks + review2_total_marks) / 2;
            let final_cie_mark = average_review_marks + guide_marks + attendance + project_report;
            final_cie_mark = Math.min(final_cie_mark, 75);

            document.getElementById('average_review_marks').value = average_review_marks.toFixed(2);
            document.getElementById('final_cie_mark').value = final_cie_mark.toFixed(2);

            // Show alert with success message and total marks
            let successMessage = "<?php echo $success_message; ?>";
            if (successMessage) {
                alert(successMessage + "\nTotal Marks: " + final_cie_mark.toFixed(2));
            }
        }
    </script>
</head>
<body>
    <nav class="navbar navbar-light bg-light">
        <a class="navbar-brand" href="#">
            <img src="logo.png" width="40" height="40" class="d-inline-block align-top" alt="">
            OnTrackify
        </a>
        <a href="display_project.php" class="btn btn-secondary back-button">Back</a>
    </nav>
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-8">
                <div class="card">
                    <div class="card-body">
                        <h5 class="card-title">Evaluate Student: <?php echo htmlspecialchars($student['name']); ?></h5>
                        <h6>Group ID: <?php echo isset($student['group_id']) ? htmlspecialchars($student['group_id']) : 'Not Assigned'; ?></h6>
                        <form action="" method="POST" class="mt-4" oninput="calculateTotalCieMarks()">
                            <div class="form-group row">
                                <label for="attendance" class="col-sm-4 col-form-label">Attendance (10 Marks)</label>
                                <div class="col-sm-8">
                                    <input type="number" class="form-control" id="attendance" name="attendance" max="10" value="<?php echo $attendance; ?>">
                                </div>
                            </div>
                            <div class="form-group row">
                                <label for="guide_marks" class="col-sm-4 col-form-label">Guide Marks (15 Marks)</label>
                                <div class="col-sm-8">
                                    <input type="number" class="form-control" id="guide_marks" name="guide_marks" max="15" value="<?php echo $guide_marks; ?>" <?php echo $disable_guide_marks; ?>>
                                </div>
                            </div>
                            <div class="form-group row">
                                <label for="project_report" class="col-sm-4 col-form-label">Project Report (10 Marks)</label>
                                <div class="col-sm-8">
                                    <input type="number" class="form-control" id="project_report" name="project_report" max="10" value="<?php echo $project_report; ?>">
                                </div>
                            </div>
                            <div class="form-group row">
                                <label for="review1_total_marks" class="col-sm-4 col-form-label">Review 1 Total Marks</label>
                                <div class="col-sm-8">
                                    <input type="number" class="form-control" id="review1_total_marks" name="review1_total_marks" max="40" value="<?php echo $review1_total_marks; ?>">
                                </div>
                            </div>
                            <div class="form-group row">
                                <label for="review2_total_marks" class="col-sm-4 col-form-label">Review 2 Total Marks</label>
                                <div class="col-sm-8">
                                    <input type="number" class="form-control" id="review2_total_marks" name="review2_total_marks" max="40" value="<?php echo $review2_total_marks; ?>">
                                </div>
                            </div>
                            <div class="form-group row">
                                <label for="average_review_marks" class="col-sm-4 col-form-label">Average Review Marks</label>
                                <div class="col-sm-8">
                                    <input type="number" class="form-control" id="average_review_marks" name="average_review_marks" value="<?php echo $average_review_marks; ?>" readonly>
                                </div>
                            </div>
                            <div class="form-group row">
                                <label for="final_cie_mark" class="col-sm-4 col-form-label">Final CIE Mark (Total: 75)</label>
                                <div class="col-sm-8">
                                    <input type="number" class="form-control" id="final_cie_mark" name="final_cie_mark" value="<?php echo $final_cie_mark; ?>" readonly>
                                </div>
                            </div>
                            <div class="form-group row">
                                <div class="col-sm-12 text-right">
                                    <button type="submit" class="btn btn-primary">Save</button>
                                    <a href="display_project.php" class="btn btn-secondary">Cancel</a>
                                </div>
                            </div>
                        </form>
                        <?php if ($success_message): ?>
                            <div class="alert alert-success mt-4"><?php echo $success_message; ?></div>
                        <?php elseif ($error_message): ?>
                            <div class="alert alert-danger mt-4"><?php echo $error_message; ?></div>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>
    </div>
</body>
</html>