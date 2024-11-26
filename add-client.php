<?php include 'php/header.php'; ?>

<?php
if (!isset($_SESSION['userid']) && !isset($_SESSION['fid'])) {
    header('location: firm-login');
} elseif (!isset($_SESSION['userid']) && isset($_SESSION['fid'])) {
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
    $firm = $_SESSION['fid'];

    //check whether the email exists
    $checkEmailStmt = mysqli_prepare($conn, "SELECT COUNT(*) FROM clients WHERE Email = ? AND firmid = ?");
    if ($checkEmailStmt) {
        mysqli_stmt_bind_param($checkEmailStmt, "si", $Email, $firm);
        mysqli_stmt_execute($checkEmailStmt);
        mysqli_stmt_bind_result($checkEmailStmt, $emailCount);
        mysqli_stmt_fetch($checkEmailStmt);
        mysqli_stmt_close($checkEmailStmt);

        if ($emailCount > 0) {
            // Email exists
            $error_msg = "Client's Email already exists.";
        } else {
            // Prepare and execute the insert statement
            $stmt = mysqli_prepare($conn, "INSERT INTO clients (ClientType, Prefix, FName, MName, LName, Email, Phone, Address, userid, firmid) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");
            if ($stmt) {
                mysqli_stmt_bind_param($stmt, "ssssssssii", $ClientType, $Prefix, $FName, $MName, $LName, $Email, $Phone, $Address, $user, $firm);
                mysqli_stmt_execute($stmt);
                mysqli_stmt_close($stmt);
                $success_msg = 'Client added successfully!';
            } else {
                // Error preparing the statement
                $error_msg = 'Error preparing the SQL statement.';
            }
        }
    }
}
?>

<div id="layoutSidenav">
    <?php include 'php/sidebar.php'; ?>
    <div id="layoutSidenav_content">
        <main">
            <div class="container-fluid px-4 d-flex flex-column align-items-start">
                <!-- <h1 class="mt-4">New Client</h1>
                <ol class="breadcrumb mb-4">
                    <li class="breadcrumb-item"><a href="clients">Clients</a></li>
                    <li class="breadcrumb-item active">New Client</li>
                </ol>
                <div class="row justify-content-end">
                </div> -->

                <div class="card shadow-sm border-0 rounded-lg mt-3 col-md-10 align-self-center d-flex flex-column">
                    <?php
                    if ($error_msg != '') {
                        echo '
                        <div class="alert alert-danger" role="alert">
                            ' . $error_msg . '
                        </div>';
                    }
                    ?>

                    <?php
                    if ($success_msg != '') {
                        echo '
                        <div class="alert alert-success" role="alert">
                            ' . $success_msg . '
                        </div>';
                    }
                    ?>
                    <div class="card-header">
                        <h3 class="text-center font-weight-light my-4">Add New Client</h3>
                    </div>
                    <div class="card-body">
                        <form class="d-flex flex-column" method="post" action="">
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
            </main>
            <?php include 'php/footer.php'; ?>