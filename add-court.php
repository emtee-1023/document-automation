<?php
include 'php/dbconn.php';
session_start();
if(!isset($_SESSION['username'])){
    header('location: login');
}

$error_msg = '';
$success_msg = '';
$redirect = '';

if (isset($_POST['submit'])) {
    $court = $_POST['court'];
    $user = $_SESSION['userid'];

    // Check if court name already exists
    $checkStmt = mysqli_prepare($conn, "SELECT COUNT(*) FROM courts WHERE courtname = ? and added_by = ?");
    if ($checkStmt) {
        mysqli_stmt_bind_param($checkStmt, "si", $court,$user);
        mysqli_stmt_execute($checkStmt);
        mysqli_stmt_bind_result($checkStmt, $count);
        mysqli_stmt_fetch($checkStmt);
        mysqli_stmt_close($checkStmt);

        if ($count > 0) {
            // Court name exists
            $error_msg = 'Court name already exists.';
        } else {
            // Prepare and execute the insert statement
            $stmt = mysqli_prepare($conn, "INSERT INTO courts (courtname,added_by) VALUES (?,?)");
            if ($stmt) {
                mysqli_stmt_bind_param($stmt, "si", $court,$user);
                mysqli_stmt_execute($stmt);
                mysqli_stmt_close($stmt);
                $success_msg = 'Court name added successfully!';
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
        <title>Add Court | DocAuto</title>
        <link href="css/styles.css" rel="stylesheet" />
        <script src="https://use.fontawesome.com/releases/v6.3.0/js/all.js" crossorigin="anonymous"></script>
    </head>
    <body class="bg-dark">
        <div id="layoutAuthentication">
            <div id="layoutAuthentication_content">
                <main>
                    <div class="container">
                        <div class="row justify-content-center">
                            <div class="col-lg-5">
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
                                    <div class="card-header"><h3 class="text-center font-weight-light my-4">New Court</h3></div>
                                    <div class="card-body">
                                        <form method="post" action="">
                                            <div class="form-floating mb-3">
                                                <input class="form-control" id="inputEmail" type="text" placeholder="new court name" name="court"/>
                                                <label for="inputEmail">New Court Name</label>
                                            </div>
                                            <div class="d-flex align-items-center justify-content-between mt-4 mb-0">
                                                <input type="submit" class="btn btn-primary" name="submit" value="submit">
                                            </div>
                                        </form>
                                    </div>
                                    <div class="card-footer text-center py-3">
                                        <div class="small"><a href="courts">Back to Courts</a></div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </main>
                </div>
            <div id="layoutAuthentication_footer">
           <?php include 'php/footer.php';?>
