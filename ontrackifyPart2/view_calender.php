<?php
// Include the database connection file
include 'db-connect.php';

// Fetch added events from the database (where updated flag is set to 0 or not present)
$query = "SELECT * FROM schedule_list WHERE updated = 0 OR updated IS NULL";
$result = mysqli_query($conn, $query);

// Initialize an empty array to store added events
$added_events = array();

// Fetch added events and store them in the array
while ($row = mysqli_fetch_assoc($result)) {
    $added_events[] = $row;
}

// Convert the added events array to JSON format
$json_events = json_encode($added_events);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Added Events Calendar</title>
    <!-- Include FullCalendar CSS -->
    <link href='https://cdnjs.cloudflare.com/ajax/libs/fullcalendar/3.10.2/fullcalendar.min.css' rel='stylesheet' />
</head>
<body>
    <h1>Added Events Calendar</h1>
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
                events: <?php echo $json_events; ?> // Load added events from PHP
            });
        });
    </script>
</body>
</html>
