<?php
include 'php/dbconn.php';
session_start();

$error_msg = '';
$success_msg = '';

// Check if the form is submitted
if (isset($_POST['submit'])) {
    // Retrieve form data
    $FirmName = $_POST['FirmName'];
    $FirmMail = $_POST['FirmMail'];
    $Address = $_POST['Address'];
    $NewPassword = $_POST['Pass'];
    $ConfirmPassword = $_POST['ConfirmPass'];

    // Validate password match
    if ($NewPassword !== $ConfirmPassword) {
        $error_msg = 'Passwords do not match.';
    } else {
        // Check if email already exists
        $checkEmailStmt = mysqli_prepare($conn, "SELECT COUNT(*) FROM firms WHERE FirmMail = ?");
        mysqli_stmt_bind_param($checkEmailStmt, "s", $FirmMail);
        mysqli_stmt_execute($checkEmailStmt);
        mysqli_stmt_bind_result($checkEmailStmt, $emailCount);
        mysqli_stmt_fetch($checkEmailStmt);
        mysqli_stmt_close($checkEmailStmt);

        if ($emailCount > 0) {
            $error_msg = 'Firm Email Address already in use.';
        } else {
            // Handle file upload
            $newPhoto = '';
            if (isset($_FILES['Logo']) && $_FILES['Logo']['error'] == UPLOAD_ERR_OK) {
                $fileTmpPath = $_FILES['Logo']['tmp_name'];
                $fileName = time() . '-' . $_FILES['Logo']['name'];
                $fileDest = 'assets/img/submitted/' . $fileName;
                if (move_uploaded_file($fileTmpPath, $fileDest)) {
                    $newPhoto = $fileName;
                } else {
                    $error_msg = 'Error uploading the logo.';
                }
            }

            // Hash the password
            $hashedPassword = password_hash($NewPassword, PASSWORD_DEFAULT);

            // Prepare insert statement for firms
            $insertStmt = mysqli_prepare($conn, "INSERT INTO firms (FirmName, FirmMail, Address, FirmPass, FirmLogo) VALUES (?, ?, ?, ?, ?)");
            if ($insertStmt) {
                mysqli_stmt_bind_param($insertStmt, "sssss", $FirmName, $FirmMail, $Address, $hashedPassword, $newPhoto);
                if (mysqli_stmt_execute($insertStmt)) {
                    // Get Firm ID
                    $checkIDStmt = mysqli_prepare($conn, "SELECT firmid FROM firms WHERE FirmMail = ?");
                    if ($checkIDStmt) {
                        mysqli_stmt_bind_param($checkIDStmt, "s", $FirmMail);
                        mysqli_stmt_execute($checkIDStmt);
                        mysqli_stmt_bind_result($checkIDStmt, $firmid);
                        mysqli_stmt_fetch($checkIDStmt);
                        mysqli_stmt_close($checkIDStmt);
                    } else {
                        die("Error preparing SELECT statement: " . mysqli_error($conn));
                    }

                    // Prepare and execute the INSERT statement for users
                    $userStmt = mysqli_prepare($conn, "INSERT INTO users (FName, LName, Email, Password, User_type, Photo FirmID) VALUES (?, ?, ?, ?, ?, ?, ?)");
                    if ($userStmt) {
                        // Use variables here
                        $firstName = 'Administrator';
                        $lastName = '';
                        $userType = 'admin';
                        $photo = 'defaultpfp.png';
                        mysqli_stmt_bind_param($userStmt, "sssssi", $firstName, $lastName, $FirmMail, $hashedPassword, $userType, $firmid);
                        mysqli_stmt_execute($userStmt);
                        mysqli_stmt_close($userStmt);

                        $success_msg = 'Firm added successfully!';
                    } else {
                        $error_msg = 'Error preparing the SQL statement for users.';
                    }
                } else {
                    $error_msg = 'Error executing the SQL statement for firms.';
                }
                mysqli_stmt_close($insertStmt);
            } else {
                $error_msg = 'Error preparing the SQL statement for firms.';
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
    <title>Firm Login - DocAuto</title>
    <link href="css/styles.css" rel="stylesheet" />
    <script src="https://use.fontawesome.com/releases/v6.3.0/js/all.js" crossorigin="anonymous"></script>
    <link rel="shortcut icon" href="assets/img/icon.png" type="image/x-icon">
</head>

<body class="bg-dark">
    <div id="layoutAuthentication">
        <div id="layoutAuthentication_content">
            <main>
                <div class="container">
                    <div class="row justify-content-center">
                        <div class="col-md-7">
                            <div class="mt-3">
                                <?php
                                if ($error_msg != '') {
                                    echo
                                    '
                                        <div class="alert alert-danger" role="alert">
                                            ' . $error_msg . '
                                        </div>
                                        ';
                                }
                                ?>

                                <?php
                                if ($success_msg != '') {
                                    echo
                                    '
                                        <div class="alert alert-success" role="alert">
                                            ' . $success_msg . '
                                        </div>
                                        ';
                                }
                                ?>

                            </div>
                            <div class="card shadow-lg border-0 rounded-lg mt-5">
                                <div class="card-header">
                                    <h3 class="text-center font-weight-light my-4">Create New Firm</h3>
                                </div>
                                <div class="card-body">
                                    <form method="post" action="" enctype="multipart/form-data">
                                        <!-- Firm Name and Email in the same row -->
                                        <div class="row mb-3">
                                            <div class="col-md-6">
                                                <div class="form-floating mb-3 mb-md-0">
                                                    <input class="form-control" id="inputFirmName" type="text" placeholder="Enter firm name" name="FirmName" />
                                                    <label for="inputFirmName">Firm Name</label>
                                                </div>
                                            </div>
                                            <div class="col-md-6">
                                                <div class="form-floating">
                                                    <input class="form-control" id="inputFirmMail" type="email" placeholder="Enter firm email" name="FirmMail" />
                                                    <label for="inputFirmMail">Firm Email Address</label>
                                                </div>
                                            </div>
                                        </div>

                                        <!-- Password and Confirm Password -->
                                        <div class="row mb-3">
                                            <div class="col-md-6">
                                                <div class="form-floating">
                                                    <input class="form-control" id="inputPass" type="password" placeholder="Password" name="Pass" />
                                                    <label for="inputPass">Password</label>
                                                </div>
                                            </div>
                                            <div class="col-md-6">
                                                <div class="form-floating">
                                                    <input class="form-control" id="inputConfirmPass" type="password" placeholder="Confirm Password" name="ConfirmPass" />
                                                    <label for="inputConfirmPass">Confirm Password</label>
                                                </div>
                                            </div>
                                        </div>

                                        <!-- Logo -->
                                        <div class="row mb-3">
                                            <div class="col-md-12">
                                                <div class="form-floating">
                                                    <input class="form-control" id="inputLogo" type="file" name="Logo" />
                                                    <label for="inputLogo">Logo</label>
                                                </div>
                                            </div>
                                        </div>

                                        <!-- Address  -->
                                        <div class="row mb-3">
                                            <div class="col-md-12">
                                                <div class="form-floating">
                                                    <textarea class="form-control" name="Address" id="inputAddress" placeholder="Enter Firm's Physical Address" style="height: 120px;"></textarea>
                                                    <label for="inputAddress">Address</label>
                                                </div>
                                            </div>
                                        </div>

                                        <div class="mt-4 mb-0 d-flex justify-content-center">
                                            <div class="d-grid">
                                                <input type="submit" class="btn btn-primary btn-block" name="submit" value="Create Firm">
                                            </div>
                                        </div>
                                    </form>

                                </div>
                                <div class="card-footer text-center py-3">
                                    <div class="small mb-4"><a href="firm-login">Back to Login</a></div>
                                    <div class="small">By signing up, you agree to our <a href="assets/files/T&C.pdf" target="_blank">Terms and Conditions</a></div>
                                    <div class="small"><a href="assets/files/privacy-policy.pdf" target="_blank">privacy Policy</a></div>

                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </main>
        </div>
        <div id="layoutAuthentication_footer">
            <?php include 'php/footer.php'; ?>