<?php
// Include the database connection file
include 'db-connect.php';

// Fetch events from the database
$query = "SELECT * FROM events";
$result = mysqli_query($conn, $query);

// Initialize an array to store events
$events = array();

// Fetch events and store them in the array
while ($row = mysqli_fetch_assoc($result)) {
    $events[] = $row;
}

// Convert the events array to JSON format and output it
echo json_encode($events);
?>
