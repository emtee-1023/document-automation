<?php
include 'php/dbconn.php';
session_start();

if (!isset($_SESSION['userid']) && !isset($_SESSION['fid'])) {
    header('location: firm-login');
} elseif (!isset($_SESSION['userid']) && isset($_SESSION['fid'])) {
    header('location: login');
}

$error_msg = '';
$success_msg = '';

// Ensure user ID is passed in the URL
if (!isset($_GET['id'])) {
    header('location: users');
    exit;
}

$user = $_GET['id'];
$firm = $_SESSION['fid'];
$current_user = $_SESSION['userid'];

// Fetch existing user details
$fetchStmt = mysqli_prepare($conn, "SELECT FName, LName, Email, Password, Photo, User_type FROM users WHERE UserID = ? AND FirmID = ?");
if ($fetchStmt) {
    mysqli_stmt_bind_param($fetchStmt, "ii", $user, $firm);
    mysqli_stmt_execute($fetchStmt);
    mysqli_stmt_bind_result($fetchStmt, $FName, $LName, $Email, $Password, $Photo, $User_type);
    mysqli_stmt_fetch($fetchStmt);
    mysqli_stmt_close($fetchStmt);
    $newPhoto = $Photo;
} else {
    $error_msg = 'Error fetching user details.';
}

if (isset($_POST['submit'])) {
    $FName = $_POST['FName'];
    $LName = $_POST['LName'];
    $Email = $_POST['Email'];
    $NewPassword = $_POST['NewPass'];

    if($_POST['OldPass']==''){
        $OldPassword = $Password;
    } else {
        $OldPassword = $_POST['OldPass'];
    }

    // Verify old password
    if ($OldPassword!=$Password) {
        $error_msg = 'Old password is incorrect.';
    } else {
        // Check if email already exists
        $checkEmailStmt = mysqli_prepare($conn, "SELECT COUNT(*) FROM users WHERE Email = ? AND UserID != ?");
        mysqli_stmt_bind_param($checkEmailStmt, "si", $Email, $user);
        mysqli_stmt_execute($checkEmailStmt);
        mysqli_stmt_bind_result($checkEmailStmt, $emailCount);
        mysqli_stmt_fetch($checkEmailStmt);
        mysqli_stmt_close($checkEmailStmt);

        if ($emailCount > 0) {
            $error_msg = 'Email already in use.';
        } else {
            // Handle file upload
            $newPhoto = $Photo;
            if (isset($_FILES['Photo']) && $_FILES['Photo']['error'] == UPLOAD_ERR_OK) {
                $fileTmpPath = $_FILES['Photo']['tmp_name'];
                $fileName = time() . '-' . $_FILES['Photo']['name'];
                $fileDest = 'assets/img/submitted/' . $fileName;
                move_uploaded_file($fileTmpPath, $fileDest);
                $newPhoto = $fileName;
            }

             // Prepare update statement
             $updateStmt = mysqli_prepare($conn, "UPDATE users SET FName = ?, LName = ?, Email = ?, Photo = ? " . ($NewPassword ? ", Password = ?" : "") . " WHERE UserID = ? AND FirmID = ?");
             if ($updateStmt) {
                 if ($NewPassword) {
                     //$NewPassword = password_hash($NewPassword, PASSWORD_DEFAULT);
                     mysqli_stmt_bind_param($updateStmt, "ssssii", $FName, $LName, $Email, $newPhoto, $NewPassword, $user, $firm);
                 } else {
                     mysqli_stmt_bind_param($updateStmt, "ssssii", $FName, $LName, $Email, $newPhoto, $user, $firm);
                 }
                 
                 mysqli_stmt_execute($updateStmt);
                 mysqli_stmt_close($updateStmt);
                 $success_msg = 'Profile updated successfully!';
             } else {
                 $error_msg = 'Error preparing the SQL statement.';
             }
        }
    }
}
?>


<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8" />
    <meta http-equiv="X-UA-Compatible" content="IE=edge" />
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no" />
    <meta name="description" content="" />
    <meta name="author" content="" />
    <title>Edit User | DocAuto</title>
    <link href="css/styles.css" rel="stylesheet" />
    <script src="https://use.fontawesome.com/releases/v6.3.0/js/all.js" crossorigin="anonymous"></script>
</head>
<body class="bg-dark">
    <div id="layoutAuthentication">
        <div id="layoutAuthentication_content">
            <main>
                <div class="container">
                    <div class="row justify-content-center">
                        <div class="col-lg-8">
                            <div class="mt-3">
                                <?php if ($error_msg != ''): ?>
                                    <div class="alert alert-danger" role="alert">
                                        <?php echo $error_msg; ?>
                                    </div>
                                <?php endif; ?>

                                <?php if ($success_msg != ''): ?>
                                    <div class="alert alert-success" role="alert">
                                        <?php echo $success_msg; ?>
                                    </div>
                                <?php endif; ?>
                            </div>
                            <div class="card shadow-lg border-0 rounded-lg mt-5">
                                <div class="card-header"><h3 class="text-center font-weight-light my-4">Edit Profile</h3></div>
                                <div class="card-body d-flex flex-column justify-content-center">
                                    <!-- Displaying Current Profile Photo -->
                                    <div class="text-center mb-4">
                                        <img src="assets/img/submitted/<?php echo $newPhoto?>" alt="Description" class="img-fluid rounded-circle" style="width: 100px; height:100px;">
                                    </div>
                                    <form method="post" action="" enctype="multipart/form-data">
                                        <!-- First Name and Last Name in the same row -->
                                        <div class="row mb-3">
                                            <div class="col-md-6">
                                                <div class="form-floating mb-3 mb-md-0">
                                                    <input class="form-control" id="inputFirstName" type="text" placeholder="Enter first name" name="FName" value="<?php echo htmlspecialchars($FName); ?>" />
                                                    <label for="inputFirstName">First Name</label>
                                                </div>
                                            </div>
                                            <div class="col-md-6">
                                                <div class="form-floating">
                                                    <input class="form-control" id="inputLastName" type="text" placeholder="Enter last name" name="LName" value="<?php echo htmlspecialchars($LName); ?>" />
                                                    <label for="inputLastName">Last Name</label>
                                                </div>
                                            </div>
                                        </div>

                                        <!-- Email and Profile Photo in the same row -->
                                        <div class="row mb-3">
                                            <div class="col-md-6">
                                                <div class="form-floating">
                                                    <input class="form-control" id="inputEmail" type="email" placeholder="name@example.com" name="Email" value="<?php echo htmlspecialchars($Email); ?>" />
                                                    <label for="inputEmail">Email Address</label>
                                                </div>
                                            </div>
                                            <div class="col-md-6">
                                                <div class="form-floating">
                                                    <input class="form-control" id="inputPhoto" type="file" name="Photo" />
                                                    <label for="inputPhoto">New Profile Photo</label>
                                                </div>
                                            </div>
                                        </div>

                                        <!-- Old and New password -->
                                        <div class="row mb-3">
                                            <div class="col-md-6">
                                                <div class="form-floating">
                                                    <input class="form-control" id="inputOldPass" type="password" placeholder="old password" name="OldPass"/>
                                                    <label for="inputEmail">Old Password</label>
                                                </div>
                                            </div>

                                            <div class="col-md-6">
                                                <div class="form-floating">
                                                    <input class="form-control" id="inputNewPass" type="password" placeholder="new password" name="NewPass"/>
                                                    <label for="inputEmail">New Password</label>
                                                    <div id="emailHelp" class="form-text"></div>
                                                </div>
                                            </div>
                                        </div>

                                        <div class="mt-4 mb-0 d-flex justify-content-center">
                                            <div class="d-grid">
                                                <input type="submit" class="btn btn-primary btn-block" name="submit" value="Update">
                                            </div>
                                        </div>
                                    </form>
                                </div>
                                <div class="card-footer text-center py-3">
                                    <div class="small"><a href="index">Return to Dashboard</a></div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </main>
        </div>
        <div id="layoutAuthentication_footer">
            <?php include 'php/footer.php'; ?>
        </div>
    </div>
</body>
</html>
