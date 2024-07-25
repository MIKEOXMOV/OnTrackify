<?php
// Connect to the database
$conn = new PDO('mysql:host=localhost;dbname=role_management', 'root', '') or die("An error occurred: Unable to connect to the database.");

// Fetch uploaded file details along with the student's name
$query = $conn->query("
    SELECT upload.*, users.name AS student_name
    FROM upload
    JOIN users ON upload.student_id = users.id
");
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Uploaded Files</title>
    <!-- Include CSS styling -->
    <style>
        /* File item styling */
        .file_item {
            display: flex;
            align-items: center;
            margin-bottom: 20px;
            background-color: #f9f9f9; /* Light gray background */
            padding: 15px;
            border-radius: 8px;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
        }

        .file_icon {
            width: 50px;
            height: 50px;
            margin-right: 15px;
            border-radius: 8px;
            background-color: #e0e0e0; /* Light gray background for icon */
            display: flex;
            justify-content: center;
            align-items: center;
        }

        .file_icon i {
            font-size: 24px;
            color: #555; /* Dark gray color for icon */
        }

        .file_details {
            flex: 1;
        }

        .file_name {
            font-size: 18px;
            font-weight: bold;
            margin-bottom: 5px;
        }

        .student_name {
            font-size: 14px;
            color: #777; /* Gray color for student name */
        }

        .download_link {
            margin-top: 10px;
            color: #3498db; /* Blue color for download link */
            text-decoration: none;
        }

        .download_link:hover {
            text-decoration: underline; /* Underline download link on hover */
        }
    </style>
</head>
<body>
    <h1>Uploaded Files</h1>
    <!-- Display uploaded files -->
    <?php while ($row = $query->fetch(PDO::FETCH_ASSOC)) : ?>
        <div class="file_item">
            <div class="file_icon">
                <?php
                // Get file extension
                $file_extension = pathinfo($row['name'], PATHINFO_EXTENSION);
                // Set icon based on file extension
                $icon_class = '';
                switch (strtolower($file_extension)) {
                    case 'pdf':
                        $icon_class = 'bx bxl-adobe';
                        break;
                    case 'doc':
                    case 'docx':
                        $icon_class = 'bx bxs-file-word';
                        break;
                    case 'xls':
                    case 'xlsx':
                        $icon_class = 'bx bxs-file-excel';
                        break;
                    case 'ppt':
                    case 'pptx':
                        $icon_class = 'bx bxs-file-powerpoint';
                        break;
                    case 'jpg':
                    case 'jpeg':
                    case 'png':
                    case 'gif':
                        $icon_class = 'bx bxs-image';
                        break;
                    default:
                        $icon_class = 'bx bxs-file';
                        break;
                }
                ?>
                <i class="<?php echo $icon_class; ?>"></i>
            </div>
            <div class="file_details">
                <div class="file_name"><?php echo htmlspecialchars($row['name']); ?></div>
                <div class="student_name">Uploaded by: <?php echo htmlspecialchars($row['student_name']); ?></div>
                <a href="download.php?filename=<?php echo urlencode($row['name']); ?>&f=<?php echo urlencode($row['fname']); ?>" class="download_link">
                    <i class="bx bxs-download"></i> Download
                </a>
            </div>
        </div>
    <?php endwhile; ?>
</body>
</html>
