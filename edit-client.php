<?php
include 'php/dbconn.php';
session_start();
if (!isset($_SESSION['username'])) {
    header('location: login');
    exit;
}

$error_msg = '';
$success_msg = '';

// Ensure client ID is passed in the URL
if (!isset($_GET['id'])) {
    header('location: clients');
    exit;
}

$client_id = $_GET['id'];
$user = $_SESSION['userid'];

// Fetch existing client details
$fetchStmt = mysqli_prepare($conn, "SELECT ClientType, Prefix, FName, MName, LName, Email, Phone, Address FROM clients WHERE clientid = ? AND Belong_to = ?");
if ($fetchStmt) {
    mysqli_stmt_bind_param($fetchStmt, "ii", $client_id, $user);
    mysqli_stmt_execute($fetchStmt);
    mysqli_stmt_bind_result($fetchStmt, $ClientType, $Prefix, $FName, $MName, $LName, $Email, $Phone, $Address);
    mysqli_stmt_fetch($fetchStmt);
    mysqli_stmt_close($fetchStmt);
} else {
    $error_msg = 'Error fetching client details.';
}

if (isset($_POST['submit'])) {
    $ClientType = $_POST['ClientType'];
    $Prefix = $_POST['Prefix'];
    $FName = $_POST['FName'];
    $MName = $_POST['MName'];
    $LName = $_POST['LName'];
    $Email = $_POST['Email'];
    $Phone = $_POST['Phone'];
    $Address = $_POST['Address'];

    // Prepare and execute the update statement
    $stmt = mysqli_prepare($conn, "UPDATE clients SET ClientType = ?, Prefix = ?, FName = ?, MName = ?, LName = ?, Email = ?, Phone = ?, Address = ? WHERE clientid = ? AND Belong_to = ?");
    if ($stmt) {
        mysqli_stmt_bind_param($stmt, "ssssssssii", $ClientType, $Prefix, $FName, $MName, $LName, $Email, $Phone, $Address, $client_id, $user);
        mysqli_stmt_execute($stmt);
        mysqli_stmt_close($stmt);
        $success_msg = 'Client updated successfully!';
        $ClientType = $ClientType;
        $Prefix = $Prefix;
        $FName = $FName;
        $MName = $MName;
        $LName = $LName;
        $Email = $Email;
        $Phone = $Phone;
        $Address = $Address;

        
    } else {
        $error_msg = 'Error preparing the SQL statement.';
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
    <title>Edit Client | DocAuto</title>
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
                                <div class="card-header"><h3 class="text-center font-weight-light my-4">Edit Client</h3></div>
                                <div class="card-body">
                                    <form method="post" action="">
                                        <!-- Client Type and Prefix in the same row -->
                                        <div class="row mb-3">
                                            <div class="col-md-6">
                                                <div class="form-floating mb-3 mb-md-0">
                                                    <select class="form-select" id="inputClientType" name="ClientType" aria-label="Client Type">
                                                        <option value="" disabled>Choose Client type</option>
                                                        <option value="individual" <?php if($ClientType == 'individual') echo 'selected'; ?>>Individual</option>
                                                        <option value="company" <?php if($ClientType == 'company') echo 'selected'; ?>>Company</option>
                                                    </select>
                                                    <label for="inputClientType">Client Type</label>
                                                </div>
                                            </div>
                                            <div class="col-md-6">
                                                <div class="form-floating">
                                                    <select class="form-select" id="inputPrefix" name="Prefix" aria-label="Prefix">
                                                        <option value="" disabled>Select prefix (optional)</option>
                                                        <option value="Mr." <?php if($Prefix == 'Mr.') echo 'selected'; ?>>Mr.</option>
                                                        <option value="Mrs." <?php if($Prefix == 'Mrs.') echo 'selected'; ?>>Mrs.</option>
                                                        <option value="Ms." <?php if($Prefix == 'Ms.') echo 'selected'; ?>>Ms.</option>
                                                        <option value="Sir." <?php if($Prefix == 'Sir.') echo 'selected'; ?>>Sir.</option>
                                                        <option value="Dr." <?php if($Prefix == 'Dr.') echo 'selected'; ?>>Dr.</option>
                                                    </select>
                                                    <label for="inputPrefix">Prefix</label>
                                                </div>
                                            </div>
                                        </div>

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

                                        <!-- Middle Name (optional) and Email in the same row -->
                                        <div class="row mb-3">
                                            <div class="col-md-6">
                                                <div class="form-floating">
                                                    <input class="form-control" id="inputMiddleName" type="text" placeholder="Enter middle name" name="MName" value="<?php echo htmlspecialchars($MName); ?>" />
                                                    <label for="inputMiddleName">Middle Name (Optional)</label>
                                                </div>
                                            </div>
                                            <div class="col-md-6">
                                                <div class="form-floating">
                                                    <input class="form-control" id="inputEmail" type="email" placeholder="name@example.com" name="Email" value="<?php echo htmlspecialchars($Email); ?>" />
                                                    <label for="inputEmail">Email Address</label>
                                                </div>
                                            </div>
                                        </div>

                                        <!-- Phone Number in its own row -->
                                        <div class="row mb-3">
                                            <div class="col-md-12">
                                                <div class="form-floating">
                                                    <input class="form-control" id="inputPhone" type="text" placeholder="Enter phone number" name="Phone" value="<?php echo htmlspecialchars($Phone); ?>" />
                                                    <label for="inputPhone">Phone Number</label>
                                                </div>
                                            </div>
                                        </div>

                                        <!-- Address in its own row -->
                                        <div class="row mb-3">
                                            <div class="col-md-12">
                                                <div class="form-floating">
                                                    <textarea class="form-control" id="inputAddress" placeholder="Enter address" name="Address" style="height: 100px;"><?php echo htmlspecialchars($Address); ?></textarea>
                                                    <label for="inputAddress">Address</label>
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
                                    <div class="small"><a href="clients">Back to Clients</a></div>
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
