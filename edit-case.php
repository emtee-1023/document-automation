<?php
include 'php/dbconn.php';
session_start();

if (!isset($_SESSION['username'])) {
    header('location: login');
    exit;
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

// Default values
$CaseName = '';
$ClientName = '';
$CaseDescription = '';
$CaseStatus = '';
$OpenDate = '';
$CloseDate = '';
$CourtName = '';

// Fetch existing case details
if ($stmt = mysqli_prepare($conn, "SELECT casename, clientid, casedescription, casestatus, opendate, closedate, courtid FROM cases WHERE caseid = ? AND userid = ?")) {
    mysqli_stmt_bind_param($stmt, "ii", $case_id, $user);
    mysqli_stmt_execute($stmt);
    mysqli_stmt_bind_result($stmt, $CaseName, $ClientName, $CaseDescription, $CaseStatus, $OpenDate, $CloseDate, $CourtName);
    mysqli_stmt_fetch($stmt);
    mysqli_stmt_close($stmt);
} else {
    $error_msg = 'Error fetching case details.';
}

// Process form submission
if (isset($_POST['submit'])) {
    $CaseName = $_POST['CaseName'];
    $ClientName = $_POST['ClientName'];
    $CaseDescription = $_POST['CaseDescription'];
    $CaseStatus = $_POST['CaseStatus'];
    $OpenDate = $_POST['OpenDate'];
    $CloseDate = $_POST['CloseDate'];
    $CourtName = $_POST['CourtName'];

    // Check if the new case name already exists (excluding the current case)
    $checkStmt = mysqli_prepare($conn, "SELECT COUNT(*) FROM cases WHERE casename = ? AND caseid != ? AND userid = ?");
    if ($checkStmt) {
        mysqli_stmt_bind_param($checkStmt, "sii", $CaseName, $case_id, $user);
        mysqli_stmt_execute($checkStmt);
        mysqli_stmt_bind_result($checkStmt, $count);
        mysqli_stmt_fetch($checkStmt);
        mysqli_stmt_close($checkStmt);

        if ($count > 0) {
            $error_msg = 'Case name already exists.';
        } else {
            // Update case details
            $stmt = mysqli_prepare($conn, "UPDATE cases SET casename = ?, clientid = ?, casedescription = ?, casestatus = ?, opendate = ?, closedate = ?, courtid = ? WHERE caseid = ? AND userid = ?");
            if ($stmt) {
                mysqli_stmt_bind_param($stmt, "ssssssiii", $CaseName, $ClientName, $CaseDescription, $CaseStatus, $OpenDate, $CloseDate, $CourtName, $case_id, $user);
                mysqli_stmt_execute($stmt);
                mysqli_stmt_close($stmt);
                $success_msg = 'Case updated successfully!';
                $CaseName = $CaseName;
                $ClientName = $ClientName;
                $CaseDescription = $CaseDescription;
                $CaseStatus = $CaseStatus;
                $OpenDate = $OpenDate;
                $CloseDate = $CloseDate;
                $CourtName = $CourtName;

            } else {
                $error_msg = 'Error preparing the SQL statement for update.';
            }
        }
    } else {
        $error_msg = 'Error preparing the SQL statement to check for duplicates.';
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
    <title>Edit Case | DocAuto</title>
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
                                if ($error_msg != '') {
                                    echo '<div class="alert alert-danger" role="alert">' . htmlspecialchars($error_msg) . '</div>';
                                }
                                if ($success_msg != '') {
                                    echo '<div class="alert alert-success" role="alert">' . htmlspecialchars($success_msg) . '</div>';
                                }
                                ?>
                            </div>
                            <div class="card shadow-lg border-0 rounded-lg mt-5">
                                <div class="card-header"><h3 class="text-center font-weight-light my-4">Edit Case</h3></div>
                                <div class="card-body">
                                    <form method="post" action="">
                                        <!-- Case Name and Client Name in the same row -->
                                        <div class="row mb-3">
                                            <div class="col-md-6">
                                                <div class="form-floating mb-3 mb-md-0">
                                                    <input class="form-control" id="inputCaseName" type="text" placeholder="Enter case name" name="CaseName" value="<?php echo htmlspecialchars($CaseName); ?>" />
                                                    <label for="inputCaseName">Case Name (eg.HKK/2024:122)</label>
                                                </div>
                                            </div>
                                            <div class="col-md-6">
                                                <div class="form-floating">
                                                    <select class="form-select" id="inputClientName" name="ClientName" aria-label="Client Name">
                                                        <option value="" disabled selected>Choose Client</option>
                                                        <?php
                                                        $stmt = $conn->prepare("SELECT clientid, CONCAT(c.prefix, ' ', c.fname, ' ', c.lname) as clientname FROM clients c WHERE belong_to = ?");
                                                        $stmt->bind_param("i", $user);
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

                                        <!-- Case Description and Case Status in the same row -->
                                        <div class="row mb-3">
                                            <div class="col-md-6">
                                                <div class="form-floating mb-3 mb-md-0">
                                                    <input class="form-control" id="inputCaseDescription" type="text" placeholder="Enter case description" name="CaseDescription" value="<?php echo htmlspecialchars($CaseDescription); ?>" />
                                                    <label for="inputCaseDescription">Case Description (eg.Mark vs Mark)</label>
                                                </div>
                                            </div>
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

                                        <!-- Court Name in its own row -->
                                        <div class="row mb-3">
                                            <div class="col-md-12">
                                                <div class="form-floating">
                                                    <select class="form-select" id="inputCourtName" name="CourtName" aria-label="Court Name">
                                                        <option value="" disabled selected>Choose Court</option>
                                                        <?php
                                                        $stmt = $conn->prepare("SELECT courtid, courtname FROM courts WHERE added_by = ?");
                                                        $stmt->bind_param("i", $user);
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

                                        <div class="mt-4 mb-0 d-flex justify-content-center">
                                            <div class="d-grid">
                                                <input type="submit" class="btn btn-primary btn-block" name="submit" value="Update Case">
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
            <?php include 'php/footer.php'; ?>
        </div>
    </div>
</body>
</html>
