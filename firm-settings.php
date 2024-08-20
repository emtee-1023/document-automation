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



$user = 1;
$firm = $_SESSION['fid'];
$current_user = $_SESSION['userid'];

// Fetch existing user details
$fetchStmt = mysqli_prepare($conn, "SELECT FirmName, FirmMail, Address, FirmPass, FirmLogo, Status FROM firms WHERE FirmID = ?");
if ($fetchStmt) {
    mysqli_stmt_bind_param($fetchStmt, "i", $firm);
    mysqli_stmt_execute($fetchStmt);
    mysqli_stmt_bind_result($fetchStmt, $FirmName, $FirmMail, $Address, $Password, $FirmLogo, $Status);
    mysqli_stmt_fetch($fetchStmt);
    mysqli_stmt_close($fetchStmt);
    $newPhoto = $FirmLogo;
} else {
    $error_msg = 'Error fetching firm details.';
}

if (isset($_POST['submit'])) {
    $FirmName = $_POST['FirmName'];
    $FirmMail = $_POST['FirmMail'];
    $Address = $_POST['Address'];
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
        $checkEmailStmt = mysqli_prepare($conn, "SELECT COUNT(*) FROM firms WHERE FirmMail = ? AND FirmID != ?");
        mysqli_stmt_bind_param($checkEmailStmt, "si", $FirmMail, $firm);
        mysqli_stmt_execute($checkEmailStmt);
        mysqli_stmt_bind_result($checkEmailStmt, $emailCount);
        mysqli_stmt_fetch($checkEmailStmt);
        mysqli_stmt_close($checkEmailStmt);

        if ($emailCount > 0) {
            $error_msg = 'Email Address already in use.';
        } else {
            // Handle file upload
            $newPhoto = $FirmLogo;
            if (isset($_FILES['Logo']) && $_FILES['Logo']['error'] == UPLOAD_ERR_OK) {
                $fileTmpPath = $_FILES['Logo']['tmp_name'];
                $fileName = time() . '-' . $_FILES['Logo']['name'];
                $fileDest = 'assets/img/submitted/' . $fileName;
                move_uploaded_file($fileTmpPath, $fileDest);
                $newPhoto = $fileName;
            }

             // Prepare update statement
             $updateStmt = mysqli_prepare($conn, "UPDATE firms SET FirmName = ?, FirmMail = ?, Address = ?, FirmLogo = ? " . ($NewPassword ? ", FirmPass = ?" : "") . " WHERE FirmID = ?");
             if ($updateStmt) {
                 if ($NewPassword) {
                     //$NewPassword = password_hash($NewPassword, PASSWORD_DEFAULT);
                     mysqli_stmt_bind_param($updateStmt, "sssssi", $FirmName, $FirmMail, $Address, $newPhoto, $NewPassword, $firm);
                 } else {
                     mysqli_stmt_bind_param($updateStmt, "ssssi", $FirmName, $FirmMail, $Address, $newPhoto, $firm);
                 }
                 
                 mysqli_stmt_execute($updateStmt);
                 mysqli_stmt_close($updateStmt);
                 $success_msg = 'Firm Details updated successfully!';
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
    <title>Firm Settings | DocAuto</title>
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
                                <div class="card-header"><h3 class="text-center font-weight-light my-4">Edit Firm Details</h3></div>
                                <div class="card-body d-flex flex-column justify-content-center">
                                    <!-- Displaying Current Profile Photo -->
                                    <div class="text-center mb-4">
                                        <img src="assets/img/submitted/<?php echo $newPhoto?>" alt="Description" class="img-fluid" style="width: 250px; height: auto;">
                                    </div>
                                    <form method="post" action="" enctype="multipart/form-data">
                                        <!-- Firm Name and Email in the same row -->
                                        <div class="row mb-3">
                                            <div class="col-md-6">
                                                <div class="form-floating mb-3 mb-md-0">
                                                    <input class="form-control" id="inputFirmName" type="text" placeholder="Enter firm name" name="FirmName" value="<?php echo htmlspecialchars($FirmName); ?>" />
                                                    <label for="inputFirmName">Firm Name</label>
                                                </div>
                                            </div>
                                            <div class="col-md-6">
                                                <div class="form-floating">
                                                    <input class="form-control" id="inputFirmMail" type="email" placeholder="Enter firm email" name="FirmMail" value="<?php echo htmlspecialchars($FirmMail); ?>" />
                                                    <label for="inputFirmMail">Firm Email Address</label>
                                                </div>
                                            </div>
                                        </div>

                                        <!-- Address and Logo in the same row -->
                                        <div class="row mb-3">
                                            <div class="col-md-6">
                                                <div class="form-floating">
                                                    <textarea class="form-control" name="Address" id="inputAddress" placeholder="Enter Firm's Physical Address" style="height: 120px;"><?php echo htmlspecialchars($Address); ?></textarea>
                                                    <label for="inputAddress">Physical Address</label>
                                                </div>
                                            </div>
                                            <div class="col-md-6">
                                                <div class="form-floating">
                                                    <input class="form-control" id="inputLogo" type="file" name="Logo" />
                                                    <label for="inputLogo">Change Logo</label>
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
