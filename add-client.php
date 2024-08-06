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
    $FName = $_POST['FName'];         
    $MName = $_POST['MName'];          
    $LName = $_POST['LName'];        
    $Email = $_POST['Email'];          
    $Phone = $_POST['Phone'];          
    $Address = $_POST['Address'];     
    $ClientType = $_POST['ClientType'];
    $Prefix = $_POST['Prefix'];
    $user = $_SESSION['userid'];

    // Prepare and execute the insert statement
    $stmt = mysqli_prepare($conn, "INSERT INTO clients (ClientType, Prefix, FName, MName, LName, Email, Phone, Address, Belong_to) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)");
    if ($stmt) {
        mysqli_stmt_bind_param($stmt, "sssssssss", $ClientType, $Prefix, $FName, $MName, $LName, $Email, $Phone, $Address, $user);
        mysqli_stmt_execute($stmt);
        mysqli_stmt_close($stmt);
        $success_msg = 'Client added successfully!';
    } else {
        // Error preparing the statement
       echo 'Error preparing the SQL statement.';
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
        <title>Add Client | DocAuto</title>
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
                                    <div class="card-header"><h3 class="text-center font-weight-light my-4">New Client</h3></div>
                                    <div class="card-body">
                                    <form method="post" action="">
                                        <!-- Client Type and Prefix in the same row -->
                                        <div class="row mb-3">
                                            <div class="col-md-6">
                                                <div class="form-floating mb-3 mb-md-0">
                                                    <select class="form-select" id="inputClientType" name="ClientType" aria-label="Client Type">
                                                        <option value="" disabled selected>Choose Client type</option>
                                                        <option value="individual">Individual</option>
                                                        <option value="company">Company</option>
                                                    </select>
                                                    <label for="inputClientType">Client Type</label>
                                                </div>
                                            </div>
                                            <div class="col-md-6">
                                                <div class="form-floating">
                                                    <select class="form-select" id="inputPrefix" name="Prefix" aria-label="Prefix">
                                                        <option value="" disabled selected>Select prefix (optional)</option>
                                                        <option value="Mr.">Mr.</option>
                                                        <option value="Mrs.">Mrs.</option>
                                                        <option value="Ms.">Ms.</option>
                                                        <option value="Sir.">Sir.</option>
                                                        <option value="Dr.">Dr.</option>
                                                    </select>
                                                    <label for="inputPrefix">Prefix</label>
                                                </div>
                                            </div>
                                        </div>

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

                                        <!-- Middle Name (optional) and Email in the same row -->
                                        <div class="row mb-3">
                                            <div class="col-md-6">
                                                <div class="form-floating">
                                                    <input class="form-control" id="inputMiddleName" type="text" placeholder="Enter middle name" name="MName" />
                                                    <label for="inputMiddleName">Middle Name (Optional)</label>
                                                </div>
                                            </div>
                                            <div class="col-md-6">
                                                <div class="form-floating">
                                                    <input class="form-control" id="inputEmail" type="email" placeholder="name@example.com" name="Email" />
                                                    <label for="inputEmail">Email Address</label>
                                                </div>
                                            </div>
                                        </div>

                                        <!-- Phone Number in its own row -->
                                        <div class="row mb-3">
                                            <div class="col-md-12">
                                                <div class="form-floating">
                                                    <input class="form-control" id="inputPhone" type="text" placeholder="Enter phone number" name="Phone" />
                                                    <label for="inputPhone">Phone Number</label>
                                                </div>
                                            </div>
                                        </div>

                                        <!-- Address in its own row -->
                                        <div class="row mb-3">
                                            <div class="col-md-12">
                                                <div class="form-floating">
                                                    <textarea class="form-control" id="inputAddress" placeholder="Enter address" name="Address" style="height: 100px;"></textarea>
                                                    <label for="inputAddress">Address</label>
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
                                        <div class="small"><a href="clients">Back to Clients</a></div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </main>
                </div>
            <div id="layoutAuthentication_footer">
           <?php include 'php/footer.php';?>
