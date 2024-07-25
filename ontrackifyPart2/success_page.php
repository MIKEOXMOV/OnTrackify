<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Profile Saved Successfully</title>
    <!-- Include Bootstrap CSS or your preferred CSS -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/twitter-bootstrap/4.1.3/css/bootstrap.min.css" rel="stylesheet">
    <!-- Include Font Awesome CSS -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.12.1/css/all.min.css">
    <!-- Include your custom CSS -->
    <link rel="stylesheet" href="css/style.css">
</head>
<body>
    <div class="ScriptTop">
        <div class="rt-container">
            <div class="col-rt-4" id="float-right">
                <!-- Ad Here -->
            </div>
            <div class="col-rt-2">
                <ul>
                    <!-- Link back to the home or dashboard page -->
                    <li><a href="student_panel.php" title="Back to Home">Back to Home</a></li>
                </ul>
            </div>
        </div>
    </div>

    <header class="ScriptHeader">
        <div class="rt-container">
            <div class="col-rt-12">
                <div class="rt-heading">
                    <h1>Profile Saved Successfully</h1>
                    <p>Your profile details have been saved.</p>
                </div>
            </div>
        </div>
    </header>

    <section>
        <div class="rt-container">
            <div class="col-rt-12">
                <div class="Scriptcontent">
                    <div class="student-profile py-4">
                        <div class="container">
                            <div class="row">
                                <div class="col-lg-4">
                                    <div class="card shadow-sm">
                                        <div class="card-header bg-transparent text-center">
                                            <!-- Use your profile image or placeholder -->
                                            <img class="profile_img" src="https://via.placeholder.com/300x200" alt="Profile Picture">
                                            <h3><?php echo $_GET['name']; ?></h3>
                                        </div>
                                        <div class="card-body">
                                            <p class="mb-0"><strong class="pr-1">Email:</strong><?php echo $_GET['email']; ?></p>
                                            <p class="mb-0"><strong class="pr-1">Register Number/Faculty ID:</strong><?php echo $_GET['register_or_faculty_id']; ?></p>
                                            <p class="mb-0"><strong class="pr-1">Role:</strong><?php echo $_GET['role']; ?></p>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-lg-8">
                                    <div class="card shadow-sm">
                                        <div class="card-header bg-transparent border-0">
                                            <h3 class="mb-0"><i class="far fa-clone pr-1"></i>Additional Details</h3>
                                        </div>
                                        <div class="card-body pt-0">
                                            <table class="table table-bordered">
                                                <tr>
                                                    <th width="30%">Department</th>
                                                    <td width="2%">:</td>
                                                    <td><?php echo $_GET['department']; ?></td>
                                                </tr>
                                                <tr>
                                                    <th width="30%">Semester</th>
                                                    <td width="2%">:</td>
                                                    <td><?php echo $_GET['semester']; ?></td>
                                                </tr>
                                                <tr>
                                                    <th width="30%">College Name</th>
                                                    <td width="2%">:</td>
                                                    <td><?php echo $_GET['college_name']; ?></td>
                                                </tr>
                                                <tr>
                                                    <th width="30%">Batch</th>
                                                    <td width="2%">:</td>
                                                    <td><?php echo $_GET['batch']; ?></td>
                                                </tr>
                                                <!-- Guide details -->
                                                <tr>
                                                    <th width="30%">Guide Department</th>
                                                    <td width="2%">:</td>
                                                    <td><?php echo $_GET['guide_department']; ?></td>
                                                </tr>
                                                <tr>
                                                    <th width="30%">Guide College Name</th>
                                                    <td width="2%">:</td>
                                                    <td><?php echo $_GET['guide_college_name']; ?></td>
                                                </tr>
                                            </table>
                                            <!-- Update button to go to profile update form -->
                                            <a href="profile.php" class="btn btn-primary">Update Profile</a>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Include necessary scripts here -->

</body>
</html>
