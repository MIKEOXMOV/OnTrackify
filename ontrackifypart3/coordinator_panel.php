<?php
session_start();
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

include 'config.php';

$user_id = $_SESSION['user_id'];
$role = $_SESSION['role'];


// Fetch username from users table
$sql = "SELECT name FROM users WHERE id = '$user_id'";
$result = $conn->query($sql);
if ($result && $result->num_rows > 0) {
    $row = $result->fetch_assoc();
    $username = $row['name'];
} else {
    // Handle if username not found (optional)
    $username = "Guest";
}
$conn->close();
 // Get first letter of username
 $firstLetter = strtoupper(substr($username, 0, 1));
 ?>
?>



<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Coordinator Dashboard Panel</title>

    <!-- CSS Stylesheets -->
    <link rel="stylesheet" href="stdstyle.css">
    <link rel="stylesheet" href="https://unicons.iconscout.com/release/v4.0.0/css/line.css">

    <!-- jQuery Library -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>
    <!-- jQuery Circle Progress Plugin -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery-circle-progress/1.2.2/circle-progress.min.js"></script>

    <!-- Custom Styles -->
    <style>
        /* Add your custom styles here */
        .boxes {
            display: flex;
            justify-content: space-around;
            align-items: center;
            margin-top: 20px;
        }

        .box {
            cursor: pointer;
            text-align: center;
            padding: 20px;
            border: 1px solid #ccc;
            border-radius: 5px;
            transition: background-color 0.3s ease;
            width: 150px; /* Adjust width as needed */
        }

        .box:hover {
            background-color: #f0f0f0;
        }

        /* Additional styles for the notification box */
        #notificationsBox {
            background-color: #3498db; /* Example background color */
            color: #fff; /* Example text color */
        }

        #notificationsBox i {
            color: #fff; /* Example icon color */
        }

        .item-list {
            margin-top: 20px;
            text-align: left;
        }

        .item {
            border: 1px solid #ccc;
            padding: 10px;
            margin-bottom: 10px;
            border-radius: 5px;
        }
    </style>
</head>
<body>
    <nav>
        <div class="logo-name">
            <div class="logo-image">
                <img src="logo.png" alt="Logo">
            </div>
            <span class="logo_name">OnTrackify</span>
        </div>

        <div class="menu-items">
            <ul class="nav-links">
                <li><a href="#">
                    <i class="uil uil-estate"></i>
                    <span class="link-name">Dashboard</span>
                </a></li>
               
                <li><a href="projects.php">
                <i class="uil uil-folder-check"></i>
                    <span class="link-name">Projects</span>
                </a></li>

                <li><a href="create_groups1.php">
                <i class="uil uil-users-alt"></i>
                    <span class="link-name"> Groups</span>
                </a></li>
                <li><a href="view_createdgroup.php">
                <i class="uil uil-users-alt"></i>
                    <span class="link-name"> View Groups</span>
                </a></li>

                <li><a href="view projects.php">
                <i class="uil uil-user-square"></i>
                    <span class="link-name">View Students</span>
                </a></li>
                <li><a href="view_group_progress.php">
                <i class="uil uil-spinner progress-icon"></i>
                    <span class="link-name">View Progress</span>
                </a></li>
                <li><a href="http://localhost/ontrackify/dashboard,signup,login/index.php">
                    <i class="uil uil-calendar-alt"></i>
                    <span class="link-name">Project Calendar</span>
                </a></li>
                <li><a href="showprevtable.php">
                <i class="uil uil-folder-check"></i>
                    <span class="link-name">Previous Projects</span>
                </a></li>
                <li><a href="display_project.php">
                <i class="uil uil-clipboard-notes progress-icon"></i>


                    <span class="link-name">Evaluation</span>
                </a></li>
                <li><a href="marks.php">
                    <i class="uil uil-award"></i>
                    <span class="link-name">Marks</span>
                </a></li>
            </ul>
            
            <ul class="logout-mode">
                <li><a href="logout.php">
                    <i class="uil uil-signout"></i>
                    <span class="link-name">Logout</span>
                </a></li>
                <li class="mode">
                    <a href="#">
                        <i class="uil uil-moon"></i>
                        <span class="link-name">Dark Mode</span>
                    </a>
                    <div class="mode-toggle">
                        <span class="switch"></span>
                    </div>
                </li>
            </ul>
        </div>
    </nav>

    <section class="dashboard">
   
<div class="top">
    <i class="uil uil-bars sidebar-toggle"></i>
    <img src="images/profile.jpg" alt="Profile Picture" class="profile-img" data-initial="<?php echo $firstLetter; ?>">
</div>

        <div class="dash-content">
            <div class="overview">
                <div class="title">
                    <i class="uil uil-tachometer-fast-alt"></i>
                    <span class="text"> Coordinator Dashboard</span>
                </div>

                <div class="boxes">
                    <div class="box" id="profilesBox">
                        <i class="uil uil-user-square"></i>
                        <span class="text">Profiles</span>
                    </div>
                    <div class="box" id="videoBox">
                    <i class="uil uil-video"></i>
                        <span class="text">Video Call</span>
                    </div>

                    <!-- Notification Box -->
                    <div class="box" id="notificationsBox">
                        <i class="uil uil-bell"></i>
                        <span class="text">Notifications</span>
                    </div>
                </div>

              

                <div class="item-list" id="itemList">
                    <!-- Item list will be dynamically populated -->
                </div>
            </div>

            <div class="activity">
                <div class="title">
                  
                </div>

                <div class="activity-data">
                    <!-- Activity data will be displayed here -->
                </div>
            </div>
        </div>
    </section>

    <script>
        document.addEventListener("DOMContentLoaded", function() {
            var profilesBox = document.getElementById("profilesBox");
            var notificationsBox = document.getElementById("notificationsBox");
            var itemList = document.getElementById("itemList");

            // Click event on Profiles box
            profilesBox.addEventListener("click", function() {
                window.location.href = "profile.php";
            });

            // Click event on Notifications box
            notificationsBox.addEventListener("click", function() {
               window.location.href = "compose_notification.php";
            });
            videoBox.addEventListener("click", function() {
                window.location.href = "https://ontrackify.daily.co/hjLlSLBvIS9ZlV3KaZ0u";
            });
            function displayItems(category) {
                itemList.innerHTML = ""; // Clear current items

                // Fetch items from the server
                fetch(`get_items.php?category=${category}`)
                    .then(response => response.json())
                    .then(items => {
                        items.forEach(item => {
                            var itemElement = document.createElement("div");
                            itemElement.classList.add("item");
                            itemElement.dataset.id = item.id;
                            itemElement.innerHTML = `
                                <span>${item.courseName}</span>
                                <button class="edit-btn">Edit</button>
                                <button class="delete-btn">Delete</button>
                            `;
                            itemList.appendChild(itemElement);
                        });
                    });
            }
        });
    </script>
</body>
</html>
