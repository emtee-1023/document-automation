<?php include 'php/header.php';?>

<?php
if (!isset($_SESSION['userid']) && !isset($_SESSION['fid'])) {
    header('location: firm-login');
} elseif (!isset($_SESSION['userid']) && isset($_SESSION['fid'])) {
    header('location: login');
}

$error_msg = '';
$success_msg = '';



$user = $_SESSION['userid'];
$firm = $_SESSION['fid'];

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

<div id="layoutSidenav">
    <?php include 'php/sidebar.php';?>
    <div id="layoutSidenav_content">
        <main>
            <div class="container-fluid px-4">
                <h1 class="mt-4">Firm Settings</h1>
                <ol class="breadcrumb mb-4">
                    <li class="breadcrumb-item"><a href="index">Home</a></li>
                    <li class="breadcrumb-item active">Firm Settings</li>
                </ol>
                <div class="row justify-content-end">
                    <!-- Displaying Current Profile Photo -->
                    <div class="text-center mb-4">
                        <img src="assets/img/submitted/<?php echo $newPhoto?>" alt="Firm Logo" class="img-fluid" style="width: 250px; height: auto;">
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
            </div>
        </main>
        <?php include 'php/footer.php';?>
    </div>
</div>



