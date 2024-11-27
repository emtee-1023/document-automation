<?php
include 'php/header.php';
include '../php/mail.php';

$success_msg = '';
$error_msg = '';

if (isset($_POST['submit'])) {
    $DocumentName = $_POST['DocumentName'];
    $CaseID = $_POST['CaseID'];
    $firm = $_SESSION['fid'];

    //fetch some details we'll use for notifications and emails.
    $stmtc = $conn->prepare("
                            SELECT 
                                c.casename, 
                                CONCAT(c2.fname,' ',c2.mname,' ',c2.lname) AS clientName, 
                                c2.email, 
                                f.firmname, 
                                f.firmmail,
                                ct.courtname,
                                c3.casenum
                                FROM cases c 
                                JOIN clients c2 ON c2.clientid = c.clientid 
                                JOIN firms f ON f.firmid = c.firmid  
                                JOIN cases c3 ON c3.caseid = c.caseid
                                JOIN courts ct ON ct.courtid = c3.courtid

                                WHERE c.caseid = ?");
    $stmtc->bind_param('s', $CaseID);
    $stmtc->execute();
    $stmtc->bind_result($caseName, $clientName, $clientEmail, $firmName, $firmEmail, $courtName, $caseNum);
    $stmtc->fetch();
    $stmtc->close();


    $subject = 'New Document Uploaded to your Case';
    $message = mailAddedDoc($clientName, $firmName, $courtName, $caseNum, $caseName);

    if (!noReplyMail($clientEmail, $subject, $message)) {
        $error_msg = "Problem encountered when mailing the client";
    }

    // Handle file upload
    if (isset($_FILES['Document']) && $_FILES['Document']['error'] === UPLOAD_ERR_OK) {
        $fileTmpPath = $_FILES['Document']['tmp_name'];
        $fileName = $_FILES['Document']['name'];
        $fileSize = $_FILES['Document']['size'];
        $fileType = $_FILES['Document']['type'];
        $fileNameCmps = explode(".", $fileName);
        $fileExtension = strtolower(end($fileNameCmps));
        $newFileName = time() . '.' . $fileExtension;
        $Extension = pathinfo($fileName, PATHINFO_EXTENSION);
        $uploadFileDir = '../assets/files/submitted/';
        $dest_path = $uploadFileDir . $newFileName;

        // Move the uploaded file
        if (move_uploaded_file($fileTmpPath, $dest_path)) {
            // Handle file space optimization
            $oldFileQuery = "SELECT FilePath FROM case_docs WHERE CaseID = ? ORDER BY CreatedAt DESC LIMIT 1 OFFSET 1";
            $stmt = mysqli_prepare($conn, $oldFileQuery);
            mysqli_stmt_bind_param($stmt, "i", $CaseID);
            mysqli_stmt_execute($stmt);
            mysqli_stmt_bind_result($stmt, $oldFile);
            mysqli_stmt_fetch($stmt);
            mysqli_stmt_close($stmt);

            if ($oldFile && file_exists($uploadFileDir . $oldFile)) {
                unlink($uploadFileDir . $oldFile);
            }

            // Insert new record into the database
            $insertStmt = mysqli_prepare($conn, "INSERT INTO case_docs (DocName, CaseID, FilePath, Extension, FirmID) VALUES (?, ?, ?, ?, ?)");
            if ($insertStmt) {
                mysqli_stmt_bind_param($insertStmt, "sissi", $DocumentName, $CaseID, $newFileName, $Extension, $firm);
                mysqli_stmt_execute($insertStmt);
                mysqli_stmt_close($insertStmt);
                $success_msg = 'Document uploaded successfully!';
            } else {
                $error_msg = 'Error preparing the SQL statement.';
            }
        } else {
            $error_msg = 'Error moving the uploaded file.';
        }
    } else {
        $error_msg = 'No file uploaded or there was an upload error.';
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
                        <h3 class="text-center font-weight-light my-4">Upload New Document</h3>
                    </div>
                    <div class="card-body">
                        <form method="post" action="" enctype="multipart/form-data">
                            <!-- Document Name and Case ID in the same row -->
                            <div class="row mb-3">
                                <div class="col-md-6">
                                    <div class="form-floating mb-3 mb-md-0">
                                        <input class="form-control" id="inputDocumentName" type="text" placeholder="Enter document name" name="DocumentName" required />
                                        <label for="inputDocumentName">Document Name</label>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-floating">
                                        <select class="form-select" id="inputCaseID" name="CaseID" required>
                                            <option value="" disabled selected>Choose Case</option>
                                            <?php
                                            $clientid = $_SESSION['clientid'];
                                            $firm = $_SESSION['fid'];

                                            // Prepare the SQL statement
                                            $stmt = $conn->prepare("SELECT CaseID, CONCAT(casenumber, ' - ', casename) AS identifier FROM cases WHERE clientid = ?");
                                            $stmt->bind_param("i", $clientid); // "i" specifies the variable type as integer
                                            $stmt->execute();
                                            $caseResult = $stmt->get_result();

                                            // Fetch results and generate options
                                            while ($row = $caseResult->fetch_assoc()) {
                                                echo '<option value="' . htmlspecialchars($row['CaseID']) . '">' . htmlspecialchars($row['identifier']) . '</option>';
                                            }

                                            // Close the statement
                                            $stmt->close();
                                            ?>

                                        </select>
                                        <label for="inputCaseID">Case</label>
                                    </div>
                                </div>
                            </div>

                            <!-- Document Upload -->
                            <div class="row mb-3">
                                <div class="col-md-6">
                                    <div class="form-floating">
                                        <input class="form-control" id="inputDocument" type="file" name="Document" accept=".pdf,.docx" required />
                                        <label for="inputDocument">Upload Document</label>
                                    </div>
                                </div>
                            </div>


                            <div class="mt-5 mb-0 d-flex justify-content-center">
                                <div class="d-grid">
                                    <input type="submit" class="btn btn-primary btn-block" name="submit" value="Upload">
                                </div>
                            </div>
                        </form>

                    </div>
                    <div class="card-footer text-center py-3">
                        <div class="small"><a href="case-docs">Back to Documents</a></div>
                    </div>
                </div>
            </div>
            </main>
            <?php include 'php/footer.php'; ?>