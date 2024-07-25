<?php
$host = 'localhost';
$db   = 'project_management';
$user = 'root';
$pass = '';
$charset = 'utf8mb4';

$dsn = "mysql:host=$host;dbname=$db;charset=$charset";
$options = [
    PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
    PDO::ATTR_EMULATE_PREPARES   => false,
];

try {
    $pdo = new PDO($dsn, $user, $pass, $options);
} catch (\PDOException $e) {
    throw new \PDOException($e->getMessage(), (int)$e->getCode());
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $id = $_POST['id'] ?? null;
    $courseCode = $_POST['courseCode'];
    $courseName = $_POST['courseName'];
    $coordinatorName = $_POST['coordinatorName'];
    $batch = $_POST['batch'];
    $semester = $_POST['semester'];

    try {
        if (isset($_POST['submit'])) {
            // Insert new project
            $stmt = $pdo->prepare('INSERT INTO projects (course_code, course_name, coordinator_name, batch, semester) VALUES (?, ?, ?, ?, ?)');
            $stmt->execute([$courseCode, $courseName, $coordinatorName, $batch, $semester]);
            $id = $pdo->lastInsertId(); // Get the auto-generated ID after insert

            // Fetch the inserted project details
            $output = fetchProjectById($pdo, $id);
            echo $output;
        } elseif (isset($_POST['edit'])) {
            if ($id) {
                // Update existing project
                $stmt = $pdo->prepare('UPDATE projects SET course_code = ?, course_name = ?, coordinator_name = ?, batch = ?, semester = ? WHERE id = ?');
                $stmt->execute([$courseCode, $courseName, $coordinatorName, $batch, $semester, $id]);

                // Fetch the updated project details
                $output = fetchProjectById($pdo, $id);
                echo $output;
            }
        } elseif (isset($_POST['delete'])) {
            if ($id) {
                // Delete project
                $stmt = $pdo->prepare('DELETE FROM projects WHERE id = ?');
                $stmt->execute([$id]);

                echo "Project deleted.";
            }
        }
    } catch (\PDOException $e) {
        echo "Database operation failed: " . $e->getMessage();
    }
}

// Helper function to fetch project details by ID and return as HTML
function fetchProjectById($pdo, $id) {
    $stmt = $pdo->prepare('SELECT * FROM projects WHERE id = ?');
    $stmt->execute([$id]);
    $project = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($project) {
        return "<div class='item' data-id='{$project['id']}'>
                <strong>Course Code:</strong> {$project['course_code']}<br>
                <strong>Course Name:</strong> {$project['course_name']}<br>
                <strong>Coordinator Name:</strong> {$project['coordinator_name']}<br>
                <strong>Batch:</strong> {$project['batch']}<br>
                <strong>Semester:</strong> {$project['semester']}<br>
            </div>";
    } else {
        return "";
    }
}
?>



