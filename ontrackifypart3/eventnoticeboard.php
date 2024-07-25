<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>OnTrackify Student Calendar</title>
    
    <!-- Style for the table -->
    <style>
        /* Global styles */
        html, body {
            height: 100%;
            width: 100%;
            font-family: Arial, sans-serif; /* Changed font to Arial */
            background: linear-gradient(to right, #aed9e0, #e9ecef); /* Gradient background */
        }

        /* Table styles */
        .event-table {
            margin: 20px auto;
            max-width: 900px;
            border-collapse: collapse;
            width: 100%;
        }

        .event-table th, .event-table td {
            border: 1px solid #ddd;
            padding: 8px;
            text-align: left;
            font-size: 16px; /* Changed font size to 16px */
        }

        .event-table th {
            background-color: #f2f2f2; /* Light gray background for table headers */
        }
    </style>
</head>
<body>
    <!-- Event Table -->
    <h1 style="text-align: center; margin-top: 20px;">Upcoming Events</h1>
    <table class="event-table">
        <tr>
            <th>Title</th>
            <th>Start Date</th>
            <th>End Date</th>
            <th>Status</th> <!-- Edited Status column -->
        </tr>
        <!-- PHP code to populate table rows with event data -->
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

        if ($result->num_rows > 0) {
            while($row = $result->fetch_assoc()) {
                echo "<tr>";
                echo "<td>" . $row['title'] . "</td>";
                echo "<td>" . $row['start'] . "</td>";
                echo "<td>" . $row['end'] . "</td>";

                // Check if the event is upcoming or done based on the end date
                $status = (strtotime($row['end']) > time()) ? 'Upcoming' : 'Done';
                echo "<td>" . $status . "</td>";

                echo "</tr>";
            }
        } else {
            echo "<tr><td colspan='4'>No upcoming events</td></tr>";
        }
        $conn->close();
        ?>
    </table>

    <!-- JavaScript for the alert -->
    <script>
        // Get the table rows
        const rows = document.querySelectorAll('.event-table tr');

        // Loop through each row starting from the second row (index 1)
        for (let i = 1; i < rows.length; i++) {
            // Get the end date from the table cell in the current row
            const endDateStr = rows[i].querySelector('td:nth-child(3)').innerText;
            const endDate = new Date(endDateStr);

            // Calculate the time difference between today and the event end date
            const timeDiff = endDate.getTime() - Date.now();

            // If the time difference is positive, the event is upcoming
            if (timeDiff > 0) {
                rows[i].style.backgroundColor = '#ffc107'; // Yellow background for upcoming events
                alert('Upcoming Event: ' + rows[i].querySelector('td:first-child').innerText);
            }
        }
    </script>
</body>
</html>
