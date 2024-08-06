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
    $caseName = $_POST['CaseName'];
    $clientId = $_POST['ClientName'];
    $caseDescription = $_POST['CaseDescription'];
    $caseStatus = $_POST['CaseStatus'];
    $openDate = $_POST['OpenDate'];
    $closeDate = $_POST['CloseDate'];
    $courtId = $_POST['CourtName'];
    $user = $_SESSION['userid'];

    // Check if case name already exists
    $checkStmt = mysqli_prepare($conn, "SELECT COUNT(*) FROM cases WHERE casename = ? AND userid = ?");
    if ($checkStmt) {
        mysqli_stmt_bind_param($checkStmt, "si", $caseName, $user);
        mysqli_stmt_execute($checkStmt);
        mysqli_stmt_bind_result($checkStmt, $count);
        mysqli_stmt_fetch($checkStmt);
        mysqli_stmt_close($checkStmt);

        if ($count > 0) {
            // Case name exists
            $error_msg = 'Case name already exists.';
        } else {
            // Prepare and execute the insert statement
            $stmt = mysqli_prepare($conn, "INSERT INTO cases (casename, clientid, casedescription, casestatus, opendate, closedate, courtid, userid) VALUES (?, ?, ?, ?, ?, ?, ?, ?)");
            if ($stmt) {
                mysqli_stmt_bind_param($stmt, "ssssssii", $caseName, $clientId, $caseDescription, $caseStatus, $openDate, $closeDate, $courtId, $user);
                mysqli_stmt_execute($stmt);
                mysqli_stmt_close($stmt);
                $success_msg = 'Case added successfully!';
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
                                    <div class="card-header"><h3 class="text-center font-weight-light my-4">New Case</h3></div>
                                    <div class="card-body">
                                        <form method="post" action="">
                                            <!-- Case Name and Client Name in the same row -->
                                            <div class="row mb-3">
                                                <div class="col-md-6">
                                                    <div class="form-floating mb-3 mb-md-0">
                                                        <input class="form-control" id="inputCaseName" type="text" placeholder="Enter case name" name="CaseName" />
                                                        <label for="inputCaseName">Case Name (eg.HKK/2024:122)</label>
                                                    </div>
                                                </div>
                                                <div class="col-md-6">
                                                    <div class="form-floating">
                                                        <select class="form-select" id="inputClientName" name="ClientName" aria-label="Client Name">
                                                            <option value="" disabled selected>Choose Client</option>
                                                            <?php
                                                            $user = $_SESSION['userid'];
                                                            // Prepare SQL query with parameterized statement
                                                            $stmt = $conn->prepare("SELECT clientid, CONCAT(c.prefix,' ',c.fname,' ',c.lname) as clientname FROM clients c WHERE belong_to = ?");
                                                            $stmt->bind_param("i", $user); // "i" specifies the variable type as integer
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

                                            <!-- Case Description and Case Status in the same row -->
                                            <div class="row mb-3">
                                                <div class="col-md-6">
                                                    <div class="form-floating mb-3 mb-md-0">
                                                        <input class="form-control" id="inputCaseDescription" type="text" placeholder="Enter case description" name="CaseDescription" />
                                                        <label for="inputCaseDescription">Case Description (eg.Mark vs Mark)</label>
                                                    </div>
                                                </div>
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

                                            <!-- Court Name in its own row -->
                                            <div class="row mb-3">
                                                <div class="col-md-12">
                                                    <div class="form-floating">
                                                        <select class="form-select" id="inputCourtName" name="CourtName" aria-label="Court Name">
                                                            <option value="" disabled selected>Choose Court</option>
                                                                <?php
                                                                $user = $_SESSION['userid'];
                                                                // Prepare SQL query with parameterized statement
                                                                $stmt = $conn->prepare("SELECT courtid, courtname FROM courts WHERE added_by = ?");
                                                                $stmt->bind_param("i", $user); // "i" specifies the variable type as integer
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
                        </div>
                    </div>
                </main>
                </div>
            <div id="layoutAuthentication_footer">
           <?php include 'php/footer.php';?>
