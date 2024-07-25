<?php
session_start();
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

include 'config.php';

$user_id = $_SESSION['user_id'];
$role = $_SESSION['role'];

// Initialize or retrieve notification count from session
if (!isset($_SESSION['notification_count'])) {
    $_SESSION['notification_count'] = 0;
}

$notification_count = $_SESSION['notification_count'];
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Student Dashboard Panel</title>

    <!-- CSS Stylesheets -->
    <link rel="stylesheet" href="stdstyle.css">
    <link rel="stylesheet" href="https://unicons.iconscout.com/release/v4.0.0/css/line.css">

    <!-- jQuery Library -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>

    <!-- Custom Styles -->
    <style>
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
        }

        .box:hover {
            background-color: #f0f0f0;
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

        .notification {
            position: relative;
            font-size: 24px;
            cursor: pointer;
        }

        .notification .badge {
            position: absolute;
            top: -5px;
            right: -5px;
            padding: 2px 6px;
            border-radius: 50%;
            background: red;
            color: white;
            font-size: 12px;
            display: none;
        }

        .notification i {
            font-size: 36px;
            line-height: 36px;
        }

        .notification-item {
            padding: 10px;
            border-bottom: 1px solid #ccc;
            cursor: pointer;
        }

        .notification-item:last-child {
            border-bottom: none;
        }

        .notification-item .message {
            display: block;
            font-size: 14px;
            margin-bottom: 5px;
        }

        .notification-item .timestamp {
            font-size: 12px;
            color: #999;
        }

        #notificationsList {
            display: none;
            position: absolute;
            right: 0;
            top: 50px;
            background: white;
            border: 1px solid #ccc;
            box-shadow: 0 2px 5px rgba(0, 0, 0, 0.1);
            width: 300px;
            max-height: 400px;
            overflow-y: auto;
            z-index: 1000;
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
                <li><a href="get_guides1.php">
                    <i class="uil uil-user-square"></i>
                    <span class="link-name">Guide</span>
                </a></li>
                <li><a href="studentprojects.php">
                    <i class="uil uil-chart"></i>
                    <span class="link-name">Enroll</span>
                </a></li>
                <li><a href="" id="joinGroupLink">
                    <i class="uil uil-users-alt"></i>
                    <span class="link-name">Join Group</span>
                </a></li>
                <li><a href="calendar.html">
                    <i class="uil uil-calendar-alt"></i>
                    <span class="link-name">Project Calendar</span>
                </a></li>
                <li><a href="fileupload.php">
                    <i class="uil uil-calendar-alt"></i>
                    <span class="link-name">file Uploads</span>
                </a></li>
                <li><a href="#">
                    <i class="uil uil-angle-double-up"></i>
                    <span class="link-name">Previous Projects</span>
                </a></li>
                <li><a href="#">
                    <i class="uil uil-trophy"></i>
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
            <div class="search-box">
                <i class="uil uil-search"></i>
                <input type="text" placeholder="Search here...">
            </div>
            <div class="notification" id="notificationBell">
                <i class="uil uil-bell"></i>
                <span class="badge" id="notificationBadge"><?php echo $notification_count; ?></span>
                <div id="notificationsList"></div>
            </div>
            <img src="images/profile.jpg" alt="Profile Picture">
        </div>

        <div class="dash-content">
            <div class="overview">
                <div class="title">
                    <i class="uil uil-tachometer-fast-alt"></i>
                    <span class="text">Dashboard</span>
                </div>

                <div class="boxes">
                    <div class="box" id="profilesBox">
                        <i class="uil uil-user-square"></i>
                        <span class="text">Profiles</span>
                    </div>
                </div>

                <div class="item-list" id="itemList">
                    <!-- Item list will be dynamically populated -->
                </div>
            </div>

            <div class="activity">
                <div class="title">
                    <i class="uil uil-clock-three"></i>
                    <span class="text">Recent Activity</span>
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
            var joinGroupLink = document.getElementById("joinGroupLink");
            var notificationBell = document.getElementById("notificationBell");
            var notificationBadge = document.getElementById("notificationBadge");
            var notificationsList = document.getElementById("notificationsList");

            profilesBox.addEventListener("click", function() {
                window.location.href = "profile.php";
            });

            joinGroupLink.addEventListener("click", function() {
                $.ajax({
                    url: 'check_group_membership.php',
                    type: 'GET',
                    success: function(response) {
                        var result = JSON.parse(response);
                        if (result.status === 'member') {
                            window.location.href = 'group_panel.php?group_id=' + result.group_id;
                        } else {
                            alert('You are not a member of any group yet.');
                        }
                    },
                    error: function(xhr, status, error) {
                        console.error('Error checking group membership:', error);
                    }
                });
            });

            // Function to fetch and display notifications
            function fetchNotifications() {
                $.ajax({
                    url: 'get_notifications.php',
                    type: 'GET',
                    success: function(response) {
                        var notifications = JSON.parse(response);

                        // Clear previous notifications
                        notificationsList.innerHTML = '';

                        // Display each notification
                        notifications.forEach(function(notification) {
                            var notificationItem = `
                                <div class="notification-item" data-id="${notification.id}">
                                    <span class="message">${notification.message}</span>
                                    <span class="timestamp">${notification.created_at}</span>
                                </div>`;
                            notificationsList.innerHTML += notificationItem;
                        });

                        // Display notifications list
                        notificationsList.style.display = 'block';

                        // Add click event to mark notification as read
                        $('.notification-item').on('click', function() {
                            var notificationId = $(this).data('id');
                            markNotificationAsRead(notificationId);
                            $(this).remove(); // Hide the notification from the list
                        });
                    },
                    error: function(xhr, status, error) {
                        console.error('Error fetching notifications:', error);
                    }
                });
            }

            // Function to mark a notification as read
            function markNotificationAsRead(notificationId) {
                $.ajax({
                    url: 'mark_notification.php',
                    type: 'POST',
                    data: { notification_id: notificationId },
                    success: function(response) {
                        var result = JSON.parse(response);
                        if (result.success) {
                            // Update notification count in session and badge
                            if (notification_count > 0) {
                                notification_count--;
                                updateNotificationBadge(notification_count);
                            }
                        } else {
                            console.error('Failed to mark notification as read.');
                        }
                    },
                    error: function(xhr, status, error) {
                        console.error('Error marking notification as read:', error);
                    }
                });
            }

            // Function to update notification badge count
            function updateNotificationBadge(count) {
                notificationBadge.innerText = count;
                if (count === 0) {
                    notificationBadge.style.display = 'none';
                } else {
                    notificationBadge.style.display = 'inline-block';
                }
            }

            // Initial fetch of notifications on page load
            fetchNotifications();

            // Periodically update notifications list (e.g., every 30 seconds)
            setInterval(fetchNotifications, 30000); // Adjust interval as needed
        });
    </script>
</body>
</html>