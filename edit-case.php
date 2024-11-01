<?php include 'php/header.php'; ?>

<?php
if (!isset($_SESSION['userid']) && !isset($_SESSION['fid'])) {
    header('location: firm-login');
} elseif (!isset($_SESSION['userid']) && isset($_SESSION['fid'])) {
    header('location: login');
}

$error_msg = '';
$success_msg = '';

// Ensure case ID is passed in the URL
if (!isset($_GET['id'])) {
    header('location: cases');
    exit;
}

$case_id = intval($_GET['id']);
$user = $_SESSION['userid'];
$firm = $_SESSION['fid'];

// Default values
$CaseNumber = '';
$CaseName = '';
$ClientName = '';
$CaseDescription = '';
$CaseStatus = '';
$OpenDate = '';
$CloseDate = '';
$CourtName = '';
$AdvocateName = '';

// Fetch existing case details
if ($stmt = mysqli_prepare($conn, "SELECT casenumber, casename, clientid, casedescription, casestatus, opendate, closedate, courtid, userid FROM cases WHERE caseid = ?")) {
    mysqli_stmt_bind_param($stmt, "i", $case_id);
    mysqli_stmt_execute($stmt);
    mysqli_stmt_bind_result($stmt, $CaseNumber, $CaseName, $ClientName, $CaseDescription, $CaseStatus, $OpenDate, $CloseDate, $CourtName, $AdvocateName);
    mysqli_stmt_fetch($stmt);
    mysqli_stmt_close($stmt);
} else {
    $error_msg = 'Error fetching case details.';
}

// Process form submission
if (isset($_POST['submit'])) {
    $CaseNumber = $_POST['CaseNumber'];
    $CaseName = $_POST['CaseName'];
    $ClientName = $_POST['ClientName'];
    $CaseDescription = $_POST['CaseDescription'];
    $CaseStatus = $_POST['CaseStatus'];
    $OpenDate = $_POST['OpenDate'];
    $CloseDate = $_POST['CloseDate'];
    $CourtName = $_POST['CourtName'];
    $AdvocateName = $_POST['AdvocateAssigned'];

    // Check if the new case number already exists (excluding the current case)
    $checkStmt = mysqli_prepare($conn, "SELECT COUNT(*) FROM cases WHERE casenumber = ? AND caseid != ? AND firmid = ?");
    if ($checkStmt) {
        mysqli_stmt_bind_param($checkStmt, "sii", $CaseNumber, $case_id, $firm);
        mysqli_stmt_execute($checkStmt);
        mysqli_stmt_bind_result($checkStmt, $count);
        mysqli_stmt_fetch($checkStmt);
        mysqli_stmt_close($checkStmt);

        if ($count > 0) {
            $error_msg = 'Case name already exists within firm. If you cannot access it, contact admin';
        } else {
            // Update case details
            $stmt = mysqli_prepare($conn, "UPDATE cases SET casenumber = ?, casename = ?, clientid = ?, casedescription = ?, casestatus = ?, opendate = ?, closedate = ?, courtid = ?, userid = ?, firmid = ? WHERE caseid = ?");
            if ($stmt) {
                mysqli_stmt_bind_param($stmt, "sssssssiiii", $CaseNumber, $CaseName, $ClientName, $CaseDescription, $CaseStatus, $OpenDate, $CloseDate, $CourtName, $AdvocateName, $firm, $case_id);
                mysqli_stmt_execute($stmt);
                mysqli_stmt_close($stmt);
                $success_msg = 'Case Details updated successfully!';

                $CaseNumber = $CaseNumber;
                $CaseName = $CaseName;
                $ClientName = $ClientName;
                $CaseDescription = $CaseDescription;
                $CaseStatus = $CaseStatus;
                $OpenDate = $OpenDate;
                $CloseDate = $CloseDate;
                $CourtName = $CourtName;
                $AdvocateName = $AdvocateName;
            } else {
                $error_msg = 'Error preparing the SQL statement for update.';
            }
        }
    } else {
        $error_msg = 'Error preparing the SQL statement to check for duplicates.';
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
                        <h3 class="text-center font-weight-light my-4">Edit Case Details</h3>
                    </div>
                    <div class="card-body">
                        <form class="d-flex flex-column" method="post" action="">
                            <!-- Case Number and Case Name in the same row -->
                            <div class="row mb-3">
                                <div class="col-md-6">
                                    <div class="form-floating mb-3 mb-md-0">
                                        <input class="form-control" id="inputCaseNumber" type="text" placeholder="Enter case number" name="CaseNumber" value="<?php echo htmlspecialchars($CaseNumber); ?> " />
                                        <label for="inputCaseNumber">Case Number</label>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-floating mb-3 mb-md-0">
                                        <input class="form-control" id="inputCaseName" type="text" placeholder="Enter case name" name="CaseName" value="<?php echo htmlspecialchars($CaseName); ?>" />
                                        <label for="inputCaseName">Case Name</label>
                                    </div>
                                </div>
                            </div>

                            <!-- Case Description and Client Name in the same row -->
                            <div class="row mb-3">
                                <div class="col-md-6">
                                    <div class="form-floating mb-3 mb-md-0">
                                        <input class="form-control" id="inputCaseDescription" type="text" placeholder="Enter case description" name="CaseDescription" value="<?php echo htmlspecialchars($CaseDescription); ?>" />
                                        <label for="inputCaseDescription">Case Description</label>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-floating">
                                        <select class="form-select" id="inputClientName" name="ClientName" aria-label="Client Name">
                                            <option value="" disabled selected>Choose Client</option>
                                            <?php
                                            $stmt = $conn->prepare("SELECT clientid, CONCAT(c.prefix, ' ', c.fname, ' ', c.lname) as clientname FROM clients c WHERE firmid = ?");
                                            $stmt->bind_param("i", $firm);
                                            $stmt->execute();
                                            $result = $stmt->get_result();

                                            while ($row = $result->fetch_assoc()) {
                                                $selected = ($row["clientid"] == $ClientName) ? 'selected' : '';
                                                echo '<option value="' . htmlspecialchars($row["clientid"]) . '" ' . $selected . '>' . htmlspecialchars($row["clientname"]) . '</option>';
                                            }

                                            $stmt->close();
                                            ?>
                                        </select>
                                        <label for="inputClientName">Client Name</label>
                                    </div>
                                </div>
                            </div>

                            <!-- Advocate Assigned-->
                            <div class="row mb-3">
                                <div class="col-md-6">
                                    <div class="form-floating">
                                        <select class="form-select" id="inputAdvocate" name="AdvocateAssigned" aria-label="AdvocateAssigned">
                                            <option value="" disabled selected>Choose Advocate</option>
                                            <?php
                                            $user = $_SESSION['userid'];
                                            $firm = $_SESSION['fid'];
                                            // Prepare SQL query with parameterized statement
                                            $stmt = $conn->prepare("SELECT userid, CONCAT(fname,' ',lname) as advocatename FROM users WHERE firmid = ? AND User_type = 'advocate'");
                                            $stmt->bind_param("i", $firm); // "i" specifies the variable type as integer
                                            $stmt->execute();
                                            $result = $stmt->get_result();

                                            while ($row = $result->fetch_assoc()) {
                                                $selected = ($row["userid"] == $AdvocateName) ? 'selected' : '';
                                                echo '<option value="' . htmlspecialchars($row["userid"]) . '" ' . $selected . '>' . htmlspecialchars($row["advocatename"]) . '</option>';
                                            }

                                            $stmt->close();
                                            ?>
                                        </select>
                                        <label for="inputClientName">Advocate in Charge</label>
                                    </div>
                                </div>
                            </div>

                            <!-- Court Name and Case Status on same row -->
                            <div class="row mb-3">
                                <div class="col-md-6">
                                    <div class="form-floating">
                                        <select class="form-select" id="inputCaseStatus" name="CaseStatus" aria-label="Case Status">
                                            <option value="" disabled selected>Choose Case Status</option>
                                            <option value="open" <?php echo ($CaseStatus == 'open') ? 'selected' : ''; ?>>Open</option>
                                            <option value="closed" <?php echo ($CaseStatus == 'closed') ? 'selected' : ''; ?>>Closed</option>
                                            <option value="pending" <?php echo ($CaseStatus == 'pending') ? 'selected' : ''; ?>>Pending</option>
                                        </select>
                                        <label for="inputCaseStatus">Case Status</label>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-floating">
                                        <select class="form-select" id="inputCourtName" name="CourtName" aria-label="Court Name">
                                            <option value="" disabled selected>Choose Court</option>
                                            <?php
                                            $stmt = $conn->prepare("SELECT courtid, courtname FROM courts WHERE firmid = ?");
                                            $stmt->bind_param("i", $firm);
                                            $stmt->execute();
                                            $result = $stmt->get_result();

                                            while ($row = $result->fetch_assoc()) {
                                                $selected = ($row["courtid"] == $CourtName) ? 'selected' : '';
                                                echo '<option value="' . htmlspecialchars($row["courtid"]) . '" ' . $selected . '>' . htmlspecialchars($row["courtname"]) . '</option>';
                                            }

                                            $stmt->close();
                                            ?>
                                        </select>
                                        <label for="inputCourtName">Court Name</label>
                                    </div>
                                </div>
                            </div>

                            <!-- Open Date and Close Date in the same row -->
                            <div class="row mb-3">
                                <div class="col-md-6">
                                    <div class="form-floating mb-3 mb-md-0">
                                        <input class="form-control" id="inputOpenDate" type="date" name="OpenDate" value="<?php echo htmlspecialchars($OpenDate); ?>" />
                                        <label for="inputOpenDate">Open Date</label>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-floating">
                                        <input class="form-control" id="inputCloseDate" type="date" name="CloseDate" value="<?php echo htmlspecialchars($CloseDate); ?>" />
                                        <label for="inputCloseDate">Close Date</label>
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
                        <div class="small"><a href="cases">Back to Cases</a></div>
                    </div>
                </div>
            </div>
            </main>
            <?php include 'php/footer.php'; ?>