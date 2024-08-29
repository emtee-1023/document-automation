<?php include 'php/header.php';?>

<?php
if(!isset($_SESSION['userid']) && !isset($_SESSION['fid'])){
    header('location: firm-login');
} elseif(!isset($_SESSION['userid']) && isset($_SESSION['fid'])){
    header('location: login');
}

$error_msg = '';
$success_msg = '';
$redirect = '';

$user = $_SESSION['userid'];
$firm = $_SESSION['fid'];

if (isset($_POST['submit'])) {
    $caseNumber = $_POST['CaseNumber'];
    $caseName = $_POST['CaseName'];
    $clientId = $_POST['ClientName'];
    $caseDescription = $_POST['CaseDescription'];
    $caseStatus = $_POST['CaseStatus'];
    $openDate = $_POST['OpenDate'];
    $closeDate = $_POST['CloseDate'];
    $courtId = $_POST['CourtName'];
    $advocate = $_POST['AdvocateAssigned'];

    $user = $_SESSION['userid'];
    $firm = $_SESSION['fid'];
    

    // Check if case number already exists
    $checkStmt = mysqli_prepare($conn, "SELECT COUNT(*) FROM cases WHERE casenumber = ? AND firmid = ?");
    if ($checkStmt) {
        mysqli_stmt_bind_param($checkStmt, "si", $caseNumber, $firm);
        mysqli_stmt_execute($checkStmt);
        mysqli_stmt_bind_result($checkStmt, $count);
        mysqli_stmt_fetch($checkStmt);
        mysqli_stmt_close($checkStmt);

        if ($count > 0) {
            // Case name exists
            $error_msg = 'Case number already exists within this firm. If you have issues accessing it, contact administrator';
        } else {
            $user = $_SESSION['userid'];
            $firm = $_SESSION['fid'];
            // Prepare and execute the insert statement
            $stmt = mysqli_prepare($conn, "INSERT INTO cases (casenumber, casename, clientid, casedescription, casestatus, opendate, closedate, courtid, userid, firmid) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");
            if ($stmt) {
                mysqli_stmt_bind_param($stmt, "sssssssiii", $caseNumber, $caseName, $clientId, $caseDescription, $caseStatus, $openDate, $closeDate, $courtId, $advocate, $firm);
                mysqli_stmt_execute($stmt);
                mysqli_stmt_close($stmt);
                $success_msg = 'Case created successfully!';
            } else {
                // Error preparing the statement
                $error_msg = 'Error preparing the SQL statement.';
            }
        }
    } else {
        // Error preparing the check statement
        $error_msg = 'Error preparing the SQL statement to check for duplicates.';
    }
}
?>

<div id="layoutSidenav">
    <?php include 'php/sidebar.php';?>
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
                            '.$error_msg.'
                        </div>';
                    }
                    ?>

                    <?php 
                    if ($success_msg != '') {
                        echo '
                        <div class="alert alert-success" role="alert">
                            '.$success_msg.'
                        </div>';
                    }
                    ?>
                    <div class="card-header"><h3 class="text-center font-weight-light my-4">Add New Case</h3></div>
                    <div class="card-body">
                        <form class="d-flex flex-column" method="post" action="">
                            <!-- Case Number and Case Name in the same row -->
                            <div class="row mb-3">
                                <div class="col-md-6">
                                    <div class="form-floating mb-3 mb-md-0">
                                        <input class="form-control" id="inputCaseNumber" type="text" placeholder="Enter case number" name="CaseNumber" />
                                        <label for="inputCaseNumber">Case Number</label>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-floating mb-3 mb-md-0">
                                        <input class="form-control" id="inputCaseName" type="text" placeholder="Enter case name" name="CaseName" />
                                        <label for="inputCaseName">Case Name</label>
                                    </div>
                                </div>
                            </div>

                            <!-- Case Description and Client Name in the same row -->
                            <div class="row mb-3">
                                <div class="col-md-6">
                                    <div class="form-floating mb-3 mb-md-0">
                                        <input class="form-control" id="inputCaseDescription" type="text" placeholder="Enter case description" name="CaseDescription" />
                                        <label for="inputCaseDescription">Case Description</label>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-floating">
                                        <select class="form-select" id="inputClientName" name="ClientName" aria-label="Client Name">
                                            <option value="" disabled selected>Choose Client</option>
                                            <?php
                                            $user = $_SESSION['userid'];
                                            $firm = $_SESSION['fid'];
                                            // Prepare SQL query with parameterized statement
                                            $stmt = $conn->prepare("SELECT clientid, CONCAT(c.prefix,' ',c.fname,' ',c.lname) as clientname FROM clients c WHERE firmid = ?");
                                            $stmt->bind_param("i", $firm); // "i" specifies the variable type as integer
                                            $stmt->execute();
                                            $result = $stmt->get_result();

                                            // Fetch and display options
                                            while ($row = $result->fetch_assoc()) {
                                                echo '<option value="' . htmlspecialchars($row["clientid"]) . '">' . htmlspecialchars($row["clientname"]) . '</option>';
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

                                            // Fetch and display options
                                            while ($row = $result->fetch_assoc()) {
                                                echo '<option value="' . htmlspecialchars($row["userid"]) . '">' . htmlspecialchars($row["advocatename"]) . '</option>';
                                            }

                                            $stmt->close();
                                            ?>
                                        </select>
                                        <label for="inputClientName">Advocate in Charge</label>
                                    </div>
                                </div>
                            </div>
                                

                            <!-- Court Name and Case status on same row -->
                            <div class="row mb-3">
                                <div class="col-md-6">
                                    <div class="form-floating">
                                        <select class="form-select" id="inputCaseStatus" name="CaseStatus" aria-label="Case Status">
                                            <option value="" disabled selected>Choose Case Status</option>
                                            <option value="open">Open</option>
                                            <option value="closed">Closed</option>
                                            <option value="pending">Pending</option>
                                        </select>
                                        <label for="inputCaseStatus">Case Status</label>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-floating">
                                        <select class="form-select" id="inputCourtName" name="CourtName" aria-label="Court Name">
                                            <option value="" disabled selected>Choose Court</option>
                                                <?php
                                                $user = $_SESSION['userid'];
                                                // Prepare SQL query with parameterized statement
                                                $stmt = $conn->prepare("SELECT courtid, courtname FROM courts WHERE firmid = ?");
                                                $stmt->bind_param("i", $firm); // "i" specifies the variable type as integer
                                                $stmt->execute();
                                                $result = $stmt->get_result();

                                                // Fetch and display options
                                                while ($row = $result->fetch_assoc()) {
                                                    echo '<option value="' . htmlspecialchars($row["courtid"]) . '">' . htmlspecialchars($row["courtname"]) . '</option>';
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
                                        <input class="form-control" id="inputOpenDate" type="date" name="OpenDate" />
                                        <label for="inputOpenDate">Open Date</label>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-floating">
                                        <input class="form-control" id="inputCloseDate" type="date" name="CloseDate" />
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
        <?php include 'php/footer.php';?>


