<?php
// Include the database connection file
include 'C:\xampp\htdocs\my_php_project\schedule\db-connect.php'; // Adjust the path as needed

// Check if the connection is successful
if ($conn->connect_error) {
    die("Cannot connect to the database: " . $conn->connect_error);
}

// Fetch events from the database
$sql = "SELECT id, title, start_datetime AS start, end_datetime AS end FROM schedule_list";
$result = $conn->query($sql);

$events = array();

if ($result->num_rows > 0) {
    while($row = $result->fetch_assoc()) {
        $events[] = $row;
    }
}

// Convert the events array to JSON format
$json_events = json_encode($events);



session_start();
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

include 'config.php';

$user_id = $_SESSION['user_id'];
$user_role = $_SESSION['role']; // Assuming you have this session variable set when the user logs in

// Determine the back button URL based on user role
$back_button_url = '';
switch ($user_role) {
    case 'guide':
        $back_button_url = 'guide_panel.php';
        break;
    case 'coordinator':
        $back_button_url = 'coordinator_panel.php';
        break;
    case 'student':
        $back_button_url = 'student_panel.php';
        break;
    default:
        $back_button_url = 'login.php'; // Fallback in case the role is not recognized
}



?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>OnTrackify Project Calendar</title>
    
    <!-- Include FullCalendar CSS -->
    <link href='https://cdnjs.cloudflare.com/ajax/libs/fullcalendar/3.10.2/fullcalendar.min.css' rel='stylesheet' />
    
    <!-- Style for the calendar -->
    <style>
        html, body {
            height: 100%;
            width: 100%;
            font-family: Apple Chancery, cursive;
            background: linear-gradient(to right, #aed9e0, #e9ecef); /* Gradient background */
        }

        #calendar {
            margin: 20px auto;
            max-width: 900px;
        }

        .fc-toolbar.fc-header-toolbar {
            background-color: #007bff; /* Blue background for the calendar header */
            color: white;
        }

        .fc-toolbar h2 {
            font-size: 1.5em;
            margin: 0;
        }

        .fc-unthemed td.fc-today, .fc-unthemed th.fc-today {
            background-color: rgba(255,255,255,0.1); /* Light background for today's date */
        }
        /* Style for the back button */
        .back-button {
            display: block;
            width: 120px;
            margin: 20px auto;
            padding: 10px;
            text-align: center;
            background-color: #007bff;
            color: white;
            text-decoration: none;
            border-radius: 5px;
            transition: background-color 0.3s ease;
        }

        .back-button:hover {
            background-color: #0056b3;
        }.back-button {
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
        <a href="<?php echo htmlspecialchars($back_button_url); ?>" class="back-button">Back</a>
    </div>
    <!-- Calendar -->
    <h1 style="text-align: center; margin-top: 20px;">OnTrackify Project Calendar</h1>
    <div id='calendar'></div>

    <!-- Include FullCalendar JS -->
    <script src='https://cdnjs.cloudflare.com/ajax/libs/jquery/3.6.0/jquery.min.js'></script>
    <script src='https://cdnjs.cloudflare.com/ajax/libs/moment.js/2.29.1/moment.min.js'></script>
    <script src='https://cdnjs.cloudflare.com/ajax/libs/fullcalendar/3.10.2/fullcalendar.min.js'></script>
    <script>
        // Initialize FullCalendar
        $(document).ready(function() {
            $('#calendar').fullCalendar({
                // Set options for FullCalendar
                defaultView: 'month',
                editable: false, // Disable editing
                eventLimit: true,
                events: <?php echo $json_events; ?> // Load events from PHP
            });
        });
    </script>
 
</body>
</html>
