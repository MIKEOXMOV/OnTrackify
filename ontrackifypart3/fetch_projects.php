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

$stmt = $pdo->query('SELECT * FROM projects');
while ($row = $stmt->fetch()) {
    echo "<div class='item' data-id='{$row['id']}'>
            <strong>Course Code:</strong> {$row['course_code']}<br>
            <strong>Course Name:</strong> {$row['course_name']}<br>
            <strong>Coordinator Name:</strong> {$row['coordinator_name']}<br>
            <strong>Batch:</strong> {$row['batch']}<br>
            <strong>Semester:</strong> {$row['semester']}<br>
        </div>";
}
?>
