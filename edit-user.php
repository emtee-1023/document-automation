<?php
include 'php/dbconn.php';
session_start();

if (!isset($_SESSION['userid']) && !isset($_SESSION['fid'])) {
    header('location: firm-login');
} elseif (!isset($_SESSION['userid']) && isset($_SESSION['fid'])) {
    header('location: login');
}

if ($_SESSION['user_type'] != 'admin' && $_SESSION['user_type'] != 'super admin') {
    header('location: 401');
    exit();
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

// Fetch existing user details
$fetchStmt = mysqli_prepare($conn, "SELECT FName, LName, Email, Password, Photo, User_type, Status FROM users WHERE UserID = ? AND FirmID = ?");
if ($fetchStmt) {
    mysqli_stmt_bind_param($fetchStmt, "ii", $user, $firm);
    mysqli_stmt_execute($fetchStmt);
    mysqli_stmt_bind_result($fetchStmt, $FName, $LName, $Email, $Password, $Photo, $User_type, $Status);
    mysqli_stmt_fetch($fetchStmt);
    mysqli_stmt_close($fetchStmt);
} else {
    $error_msg = 'Error fetching user details.';
}

if (isset($_POST['submit'])) {
    $FName = $_POST['FName'];
    $LName = $_POST['LName'];
    $Email = $_POST['Email'];
    $User_type = $_POST['User_type'];
    $Status = $_POST['Status'];
    //$Password = $_POST['Password'];
    
    // Check if email already exists
    $emailCheck = mysqli_prepare($conn, "SELECT COUNT(*) FROM users WHERE Email = ? AND UserID != ?");
    mysqli_stmt_bind_param($emailCheck, "si", $Email, $user);
    mysqli_stmt_execute($emailCheck);
    mysqli_stmt_bind_result($emailCheck, $emailCount);
    mysqli_stmt_fetch($emailCheck);
    mysqli_stmt_close($emailCheck);

    if ($emailCount > 0) {
        $error_msg = 'Email is already in use.';
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

        // Prepare and execute the update statement
        $stmt = mysqli_prepare($conn, "UPDATE users SET FName = ?, LName = ?, Email = ?, User_type = ?, Photo = ?, Status = ? WHERE UserID = ? AND FirmID = ?");
        if ($stmt) {
            mysqli_stmt_bind_param($stmt, "sssssiii", $FName, $LName, $Email, $User_type, $newPhoto, $Status, $user, $firm);
            mysqli_stmt_execute($stmt);
            mysqli_stmt_close($stmt);
            $success_msg = 'User Info updated successfully!';

            $FName = $_POST['FName'];
            $LName = $_POST['LName'];
            $Email = $_POST['Email'];
            $User_type = $_POST['User_type'];
            $Status = $_POST['Status'];
            $Photo = $newPhoto; 

        } else {
            $error_msg = 'Error preparing the SQL statement.';
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
                                <div class="card-header"><h3 class="text-center font-weight-light my-4">Edit User Details</h3></div>
                                <div class="card-body">
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

                                        <!-- Email and User Type in the same row -->
                                        <div class="row mb-3">
                                            <div class="col-md-6">
                                                <div class="form-floating">
                                                    <input class="form-control" id="inputEmail" type="email" placeholder="name@example.com" name="Email" value="<?php echo htmlspecialchars($Email); ?>" />
                                                    <label for="inputEmail">Email Address</label>
                                                </div>
                                            </div>
                                            <div class="col-md-6">
                                                <div class="form-floating">
                                                    <select class="form-select" id="inputUserType" name="User_type" aria-label="User Type">
                                                        <option value="" disabled>Select user type</option>
                                                        <option value="admin" <?php if($User_type == 'admin') echo 'selected'; ?>>Admin</option>
                                                        <option value="advocate" <?php if($User_type == 'advocate') echo 'selected'; ?>>Advocate</option>
                                                        <option value="clerk" <?php if($User_type == 'clerk') echo 'selected'; ?>>Clerk</option>
                                                        <option value="intern" <?php if($User_type == 'intern') echo 'selected'; ?>>Intern</option>
                                                    </select>
                                                    <label for="inputUserType">User Type</label>
                                                </div>
                                            </div>
                                        </div>

                                        <!-- Activity Status and Photo in the same row -->
                                        <div class="row mb-3">
                                            <div class="col-md-6">
                                                <div class="form-floating">
                                                    <input class="form-control" id="inputPhoto" type="file" name="Photo" />
                                                    <label for="inputPhoto">Profile Photo</label>
                                                </div>
                                                <div class="form-floating mt-3 form-control">
                                                    <img src="assets/img/submitted/<?php echo $Photo?>" alt="profile picture" style="width: 40px;">
                                                    Current Photo
                                                </div>
                                            </div>

                                            <div class="col-md-6">
                                                <div class="form-floating">
                                                    <select class="form-select" id="inputUserStatus" name="Status" aria-label="Status">
                                                        <option value="" disabled>Change Activity Status</option>
                                                        <option value="1" <?php if($User_type == 'admin') echo 'selected'; ?>>Active</option>
                                                        <option value="0" <?php if($User_type == 'standard user') echo 'selected'; ?>>Inactive</option>
                                                    </select>
                                                    <label for="inputUserType">Activity Status</label>
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
                                    <div class="small"><a href="firm-users">Back to Users</a></div>
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
