<?php include 'php/header.php'; ?>

<?php
if(!isset($_SESSION['userid']) && !isset($_SESSION['fid'])){
    header('location: firm-login');
} elseif(!isset($_SESSION['userid']) && isset($_SESSION['fid'])){
    header('location: login');
}

if(!isset($_GET['fileid'])){
    header('location: case-docs');
}

$fileid = $_GET['fileid'];
$user = $_SESSION['userid'];

//default values
$docName = '';
$caseid = '';
$caseName = '';

//fetch existing document details
if ($stmt = mysqli_prepare($conn, "SELECT cd.docname, cd.caseid, c.casename FROM case_docs cd JOIN cases c ON c.caseid = cd.caseid WHERE docid = ?")) {
    mysqli_stmt_bind_param($stmt, "i", $fileid);
    mysqli_stmt_execute($stmt);
    mysqli_stmt_bind_result($stmt, $docName, $caseid, $caseName);
    mysqli_stmt_fetch($stmt);
    mysqli_stmt_close($stmt);
} else {
    $error_msg = 'Error fetching case details.';
}

$success_msg = '';
$error_msg = '';

//process form submission
if (isset($_POST['submit'])) {
    $DocumentName = $_POST['DocumentName'];
    $CaseID = $_POST['CaseID'];
    $uploadTime = date('Y-m-d H:i:s');

        // Check if a file was uploaded
    if (isset($_FILES['Document']) && $_FILES['Document']['error'] === UPLOAD_ERR_OK) {
        $fileTmpPath = $_FILES['Document']['tmp_name'];
        $fileName = $_FILES['Document']['name'];
        $fileSize = $_FILES['Document']['size'];
        $fileType = $_FILES['Document']['type'];
        $fileNameCmps = explode(".", $fileName);
        $fileExtension = strtolower(end($fileNameCmps));
        $newFileName = time() . '.' . $fileExtension;
        $Extension = pathinfo($fileName, PATHINFO_EXTENSION);
        $uploadFileDir = 'assets/files/submitted/';
        $dest_path = $uploadFileDir . $newFileName;

        // Move the uploaded file
        if (move_uploaded_file($fileTmpPath, $dest_path)) {
            // Handle file space optimization
            $oldFileQuery = "SELECT FilePath FROM case_docs WHERE CaseID = ? ORDER BY Created_on DESC LIMIT 1 OFFSET 1";
            $stmt = mysqli_prepare($conn, $oldFileQuery);
            mysqli_stmt_bind_param($stmt, "i", $CaseID);
            mysqli_stmt_execute($stmt);
            mysqli_stmt_bind_result($stmt, $oldFile);
            mysqli_stmt_fetch($stmt);
            mysqli_stmt_close($stmt);

            if ($oldFile && file_exists($uploadFileDir . $oldFile)) {
                unlink($uploadFileDir . $oldFile);
            }

            // Update existing record in the database
            $updateStmt = mysqli_prepare($conn, "UPDATE case_docs SET DocName = ?, FilePath = ?, Extension = ?, CreatedAt = ?, CaseID = ? WHERE DocID = ?");
            if ($updateStmt) {
                mysqli_stmt_bind_param($updateStmt, "ssssii", $DocumentName, $newFileName, $Extension, $uploadTime, $CaseID, $fileid);
                mysqli_stmt_execute($updateStmt);
                mysqli_stmt_close($updateStmt);
                $success_msg = 'Document details updated successfully';
                
                //reset input values
                $docName = $DocumentName;
                $caseid = $CaseID;
            } else {
                $error_msg = 'Error preparing the update SQL statement.';
            }

        } else {
            $error_msg = 'Error moving the uploaded file.';
        }
    } else {
        // No file uploaded, so keep the current file
        $updateStmt = mysqli_prepare($conn, "UPDATE case_docs SET DocName = ?, CreatedAt = ?, CaseID = ? WHERE DocID = ?");
        if ($updateStmt) {
            mysqli_stmt_bind_param($updateStmt, "ssii", $DocumentName, $uploadTime, $CaseID, $fileid);
            mysqli_stmt_execute($updateStmt);
            mysqli_stmt_close($updateStmt);
            $success_msg = 'Document details updated successfully';
            
            //reset input values
            $docName = $DocumentName;
            $caseid = $CaseID;
            
        } else {
            $error_msg = 'Error preparing the update SQL statement.';
        }

    }
    // Fetch the updated case name based on the new CaseID
    if ($stmt = mysqli_prepare($conn, "SELECT casename FROM cases WHERE caseid = ?")) {
        mysqli_stmt_bind_param($stmt, "i", $CaseID);
        mysqli_stmt_execute($stmt);
        mysqli_stmt_bind_result($stmt, $caseName);
        mysqli_stmt_fetch($stmt);
        mysqli_stmt_close($stmt);
    } else {
        $error_msg = 'Error fetching the updated case name.';
    }

    // Reset input value for CaseName
    $caseName = $caseName;

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
                    <div class="card-header"><h3 class="text-center font-weight-light my-4">Edit Document Submission</h3></div>
                    <div class="card-body">
                        <form method="post" action="" enctype="multipart/form-data">
                            <!-- Document Name and Case ID in the same row -->
                            <div class="row mb-3">
                                <div class="col-md-6">
                                    <div class="form-floating mb-3 mb-md-0">
                                        <input class="form-control" id="inputDocumentName" type="text" placeholder="Enter document name" name="DocumentName" value="<?php echo $docName;?>"/>
                                        <label for="inputDocumentName">Document Name</label>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-floating">
                                        <select class="form-select" id="inputCaseID" name="CaseID">
                                            <option value="<?php echo $caseid?>"selected><?php echo $caseName?></option>
                                            <?php
                                            $caseQuery = "SELECT CaseID, CaseName FROM cases WHERE caseid!=$caseid";
                                            $caseResult = mysqli_query($conn, $caseQuery);
                                            if ($caseResult) {
                                                while ($row = mysqli_fetch_assoc($caseResult)) {
                                                    echo '<option value="' . htmlspecialchars($row['CaseID']) . '">' . htmlspecialchars($row['CaseName']) . '</option>';
                                                }
                                            }
                                            ?>
                                        </select>
                                        <label for="inputCaseID">Case Name</label>
                                    </div>
                                </div>
                            </div>

                            <!-- Document Upload -->
                            <div class="row mb-3">
                                <div class="col-md-12">
                                    <div class="form-floating">
                                        <input class="form-control" id="inputDocument" type="file" name="Document" />
                                        <label for="inputDocument">Change uploaded Document</label>
                                    </div>
                                </div>
                            </div>
                            

                            <div class="mt-5 mb-0 d-flex justify-content-center">
                                <div class="d-grid">
                                    <input type="submit" class="btn btn-primary btn-block" name="submit" value="Edit">
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
        <?php include 'php/footer.php';?>