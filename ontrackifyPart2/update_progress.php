<?php
session_start();

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

// Include database configuration
include 'config.php';

// Initialize variables
$group_id = '';
$progress = [];

// Function to sanitize checkbox values
function sanitizeCheckboxValue($value) {
    return isset($value) && $value == 'on' ? 1 : 0;
}

// Check if group_id is provided via GET parameter
if (isset($_GET['group_id'])) {
    $group_id = $_GET['group_id'];

    // Fetch group details (optional, for display purposes)
    $groupQuery = "SELECT group_id FROM groups WHERE group_id = '$group_id'";
    $groupResult = mysqli_query($conn, $groupQuery);

    if ($groupResult && mysqli_num_rows($groupResult) > 0) {
        $group = mysqli_fetch_assoc($groupResult);
        $group_name = $group['group_id'];
    } else {
        $group_name = "Group Name Not Found";
    }

    // Fetch current progress from the database for display and update
    $selectQuery = "SELECT * FROM progress WHERE group_id = '$group_id'";
    $result = mysqli_query($conn, $selectQuery);

    if ($result && mysqli_num_rows($result) > 0) {
        $progress = mysqli_fetch_assoc($result);
    } else {
        // Initialize $progress with default values if no progress found
        $progress = [
            'idea_submission' => 0,
            'idea_confirmation' => 0,
            'design_phase' => 0,
            'task_appointment' => 0,
            'zeroth_phase_presentation' => 0,
            'objectives' => 0,
            'system_architecture' => 0,
            'ppt_making_first_presentation' => 0,
            'first_presentation' => 0,
            'fifty_percent_project_completed' => 0,
            'seventy_five_percent_project_completed' => 0,
            'hundred_percent_project_completed' => 0,
            'internal_presentation' => 0,
            'verified_report' => 0,
            'external_presentation' => 0
        ];
    }
}

// Define the column names from your progress table
$columns = [
    'idea_submission',
    'idea_confirmation',
    'design_phase',
    'task_appointment',
    'zeroth_phase_presentation',
    'objectives',
    'system_architecture',
    'ppt_making_first_presentation',
    'first_presentation',
    'fifty_percent_project_completed',
    'seventy_five_percent_project_completed',
    'hundred_percent_project_completed',
    'internal_presentation',
    'verified_report',
    'external_presentation'
];

// Handle form submission to update or insert progress
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['group_id'])) {
    $group_id = $_POST['group_id'];

    // Initialize an array to store column updates
    $updateColumns = [];

    foreach ($columns as $column) {
        if (isset($_POST[$column])) {
            $updateColumns[] = "$column = " . sanitizeCheckboxValue($_POST[$column]);
        } else {
            $updateColumns[] = "$column = 0"; // Ensure unchecked boxes are set to 0
        }
    }

    // Check if progress already exists for this group_id
    $selectQuery = "SELECT * FROM progress WHERE group_id = '$group_id'";
    $result = mysqli_query($conn, $selectQuery);

    if ($result && mysqli_num_rows($result) > 0) {
        // Progress exists, perform UPDATE
        $updateQuery = "UPDATE progress SET " . implode(", ", $updateColumns) . " WHERE group_id = '$group_id'";
    } else {
        // Progress does not exist, perform INSERT
        $insertColumns = ['group_id'];
        $insertValues = ["'$group_id'"];

        foreach ($columns as $column) {
            if (isset($_POST[$column])) {
                $insertColumns[] = $column;
                $insertValues[] = sanitizeCheckboxValue($_POST[$column]);
            } else {
                // Default value for unchecked boxes
                $insertColumns[] = $column;
                $insertValues[] = 0;
            }
        }

        // Prepare the SQL insert query
        $insertQuery = "INSERT INTO progress (" . implode(", ", $insertColumns) . ") VALUES (" . implode(", ", $insertValues) . ")";
    }

    // Perform the database operation (UPDATE or INSERT)
    $queryToExecute = isset($updateQuery) ? $updateQuery : $insertQuery;

    if (mysqli_query($conn, $queryToExecute)) {
        $response = [
            'success' => true,
            'message' => isset($updateQuery) ? 'Progress updated successfully!' : 'Progress inserted successfully!'
        ];
    } else {
        $response = [
            'success' => false,
            'error' => isset($updateQuery) ? 'Failed to update progress: ' . mysqli_error($conn) : 'Failed to insert progress: ' . mysqli_error($conn)
        ];
    }

    echo json_encode($response);
    exit;
}

// Close database connection
mysqli_close($conn);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Group Progress</title>
    <style>
        body {
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
            background-color: #f0f0f0;
            margin: 0;
            font-family: Arial, sans-serif;
        }

        .progress-container {
            width: 60%;
            text-align: center;
        }

        .progress-circle {
            width: 120px;
            height: 120px;
            border-radius: 50%;
            background: conic-gradient(
                #4d5bf9 0%,
                #4d5bf9 65%,
                #cadcff 65%
            );
            display: inline-block;
            margin: 20px;
            position: relative;
        }

        .progress-circle span {
            position: absolute;
            font-size: 24px;
            font-weight: bold;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%);
        }

        .form-group {
            margin-bottom: 10px;
        }
    </style>
</head>
<body>
    <div class="progress-container">
        <div class="progress-circle" id="progress">
            <span id="number">0%</span>
        </div>
    </div>

    <form id="progressForm" action="update_progress.php" method="post">
        <input type="hidden" name="group_id" value="<?php echo htmlspecialchars($group_id); ?>">

        <?php foreach ($columns as $column): ?>
            <div class="form-group">
                <label><input type="checkbox" name="<?php echo $column; ?>" <?php echo $progress[$column] == 1 ? 'checked' : ''; ?>> <?php echo ucwords(str_replace('_', ' ', $column)); ?></label>
            </div>
        <?php endforeach; ?>

        <button type="submit">Update Progress</button>
    </form>

    <script>
    document.addEventListener('DOMContentLoaded', () => {
        updateProgressBar(<?php echo json_encode($progress); ?>);
        // Add event listener to form submission
        const form = document.getElementById('progressForm');
        form.addEventListener('submit', handleFormSubmit);
    });

    function handleFormSubmit(event) {
        event.preventDefault(); // Prevent default form submission

        // Update progress asynchronously
        updateProgress();
    }

    function updateProgress() {
        const checkboxes = document.querySelectorAll('input[type="checkbox"]');
        let completedPhases = 0;

        checkboxes.forEach(checkbox => {
            if (checkbox.checked) {
                completedPhases++;
            }
        });

        const percentage = (completedPhases / checkboxes.length) * 100;

        // Update progress bar
        document.getElementById('progress').style.background = `conic-gradient(
            #4d5bf9 0%,
            #4d5bf9 ${percentage}%,
            #cadcff ${percentage}%
        )`;

        // Update progress text
        document.getElementById('number').textContent = `${percentage.toFixed(0)}%`;

        // Submit the form asynchronously to update database
        const form = document.getElementById('progressForm');
        fetch(form.action, {
            method: 'POST',
            body: new FormData(form)
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                // Display success message as a popup after form submission
                alert(data.message);
                console.log('Progress updated successfully!');
                window.location.href = 'progress.php';
                // Optionally update UI or perform other actions
            } else {
                alert(data.message);
                console.error('Failed to update progress:', data.error);
                window.location.href = 'progress.php';
                // Handle error cases or display error messages
            }
        })
        .catch(error => console.error('Error updating progress:', error));
    }

    function updateProgressBar(progress) {
        const checkboxes = document.querySelectorAll('input[type="checkbox"]');
        let completedPhases = 0;

        checkboxes.forEach(checkbox => {
            if (checkbox.checked) {
                completedPhases++;
            }
        });

        const percentage = (completedPhases / checkboxes.length) * 100;

        // Display progress bar immediately without animation on page load
        document.getElementById('progress').style.background = `conic-gradient(
            #4d5bf9 0%,
            #4d5bf9 ${percentage}%,
            #cadcff ${percentage}%
        )`;

        // Update progress text
        document.getElementById('number').textContent = `${percentage.toFixed(0)}%`;
    }
</script>

</body>
</html>