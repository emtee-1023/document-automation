<?php
include 'php/dbconn.php';
session_start();
if(!isset($_SESSION['userid']) && !isset($_SESSION['fid'])){
    header('location: firm-login');
} elseif(!isset($_SESSION['userid']) && isset($_SESSION['fid'])){
    header('location: login');
}

if ($_SESSION['user_type'] != 'admin' && $_SESSION['user_type'] != 'super admin') {
    header('location: 401');
    exit();
}


$error_msg = '';
$success_msg = '';
$redirect = '';

if (isset($_POST['submit'])) {
    $FName = $_POST['FName'];
    $LName = $_POST['LName'];
    $Email = $_POST['Email'];
    $Password = $_POST['Password'];
    $User_type = $_POST['User_type'];

    if(isset($_FILES['Photo'])){
        $Photo = $_FILES['Photo'];
        $target_dir = "assets/img/submitted/";
        $file_extension = pathinfo($_FILES["Photo"]["name"], PATHINFO_EXTENSION);
        $new_file_name = time(). "." . $file_extension;
        $target_file = $target_dir . $new_file_name;
        move_uploaded_file($_FILES["Photo"]["tmp_name"], $target_file);
        $Pfp = $new_file_name;
    } else {
        $Pfp = "defaultpfp.png";
    }

    $user = $_SESSION['userid'];
    $firm = $_SESSION['fid']; 

    // Check if email already exists
    $checkEmailStmt = mysqli_prepare($conn, "SELECT COUNT(*) FROM users WHERE Email = ?");
    if ($checkEmailStmt) {
        mysqli_stmt_bind_param($checkEmailStmt, "s", $Email);
        mysqli_stmt_execute($checkEmailStmt);
        mysqli_stmt_bind_result($checkEmailStmt, $emailCount);
        mysqli_stmt_fetch($checkEmailStmt);
        mysqli_stmt_close($checkEmailStmt);

        if ($emailCount > 0) {
            // Email exists
            $error_msg = 'Email already exists.';
        } else {
            // Prepare and execute the insert statement
            $stmt = mysqli_prepare($conn, "INSERT INTO users (FName, LName, Email, Password, Photo, User_type,FirmID) VALUES (?, ?, ?, ?, ?, ?,?)");
            if ($stmt) {
                mysqli_stmt_bind_param($stmt, "ssssssi", $FName, $LName, $Email, $Password, $Pfp, $User_type,$firm);
                mysqli_stmt_execute($stmt);
                mysqli_stmt_close($stmt);
                $success_msg = 'User added successfully!';
            } else {
                // Error preparing the statement
                echo 'Error preparing the SQL statement.';
            }
        }
    } else {
        // Error preparing the check statement
        echo 'Error preparing the SQL statement to check for duplicates.';
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
        <title>New User | DocAuto</title>
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
                                    <?php 
                                    if($error_msg!=''){
                                        echo
                                        '
                                        <div class="alert alert-danger" role="alert">
                                            '.$error_msg.'
                                        </div>
                                        ';}
                                    ?>

                                    <?php 
                                    if($success_msg!=''){
                                        echo
                                        '
                                        <div class="alert alert-success" role="alert">
                                            '.$success_msg.'
                                        </div>
                                        ';}
                                    ?>
                                        
                                </div>
                                <div class="card shadow-lg border-0 rounded-lg mt-5">
                                    <div class="card-header"><h3 class="text-center font-weight-light my-4">Add New User</h3></div>
                                    <div class="card-body">
                                    <form method="post" action="" enctype="multipart/form-data">
                                        <!-- First Name and Last Name in the same row -->
                                        <div class="row mb-3">
                                            <div class="col-md-6">
                                                <div class="form-floating mb-3 mb-md-0">
                                                    <input class="form-control" id="inputFirstName" type="text" placeholder="Enter first name" name="FName" />
                                                    <label for="inputFirstName">First Name</label>
                                                </div>
                                            </div>
                                            <div class="col-md-6">
                                                <div class="form-floating">
                                                    <input class="form-control" id="inputLastName" type="text" placeholder="Enter last name" name="LName" />
                                                    <label for="inputLastName">Last Name</label>
                                                </div>
                                            </div>
                                        </div>

                                        <!-- Email and Password in the same row -->
                                        <div class="row mb-3">
                                            <div class="col-md-6">
                                                <div class="form-floating mb-3 mb-md-0">
                                                    <input class="form-control" id="inputEmail" type="email" placeholder="name@example.com" name="Email" />
                                                    <label for="inputEmail">Email Address</label>
                                                </div>
                                            </div>
                                            <div class="col-md-6">
                                                <div class="form-floating">
                                                    <input class="form-control" id="inputPassword" type="password" placeholder="Enter password" name="Password" />
                                                    <label for="inputPassword">Password</label>
                                                </div>
                                            </div>
                                        </div>

                                        <!-- Photo and User Type in the same row -->
                                        <div class="row mb-3">
                                            <div class="col-md-6">
                                                <div class="form-floating mb-3 mb-md-0">
                                                    <input class="form-control form-control-md" id="inputPhoto" type="file" placeholder="Upload photo" name="Photo" />
                                                    <label for="inputPhoto">Choose Profile Photo</label>
                                                </div>
                                            </div>
                                            <div class="col-md-6">
                                                <div class="form-floating">
                                                    <select class="form-select" id="inputUserType" name="User_type" aria-label="User Type">
                                                        <option value="" disabled selected>Select User Type</option>
                                                        <option value="admin">Admin</option>
                                                        <option value="standard user">Standard User</option>
                                                    </select>
                                                    <label for="inputUserType">User Type</label>
                                                </div>
                                            </div>
                                        </div>

                                        <div class="mt-4 mb-0 d-flex justify-content-center">
                                            <div class="d-grid">
                                                <input type="submit" class="btn btn-primary btn-block" name="submit" value="Submit">
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
           <?php include 'php/footer.php';?>
