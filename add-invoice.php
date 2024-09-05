<?php include 'php/header.php';?>


<?php
$error_msg = '';
$success_msg = '';
$today = date('Y-m-d');


// Check if caseid is set in the GET method
if (isset($_GET['caseid'])) {
    $caseid = $_GET['caseid'];
    
    // Query to get the client and case details based on caseid
    $query = $conn->prepare("SELECT c.CaseID, cl.ClientID, c.CaseName, cl.Prefix, cl.FName, cl.LName, cl.Email, cl.Phone, cl.Address
                             FROM cases c
                             JOIN clients cl ON c.ClientID = cl.ClientID
                             WHERE c.CaseID = ?");
    $query->bind_param("i", $caseid);
    $query->execute();
    $result = $query->get_result();

    if ($result->num_rows > 0) {
        $formData = $result->fetch_assoc(); // Populate formData array with results
    }
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    if (isset($_POST['upload-invoice'])) {
    // Variables
    $fileName = $_POST['InvoiceName'];
    $invoiceNum = $_POST['InvoiceNumber'];
    $clientID = $_POST['ClientID'];
    $caseID = $_POST['CaseID'];
    $userID = $_SESSION['userid'];
    $firmID = $_SESSION['fid'];
    $uploadDir = 'assets/files/submitted/'; 

    // File upload handling
    $file = $_FILES['Invoice'];
    $originalFileName = $file['name'];
    $fileTmpPath = $file['tmp_name'];
    $fileExtension = pathinfo($originalFileName, PATHINFO_EXTENSION);

    // Check if the file is a PDF
    if (strtolower($fileExtension) !== 'pdf') {
        $error_msg = "Only PDF files are allowed.";
        exit;
    }

    // Create a unique file name
    $newFileName = uniqid() . '-' . $fileName . '.' . $fileExtension;
    $destPath = $uploadDir . $newFileName;

    // Move the file to the server
    if (move_uploaded_file($fileTmpPath, $destPath)) {
        // Insert into invoice_uploads
        $sqlUpload = "INSERT INTO invoice_uploads (InvoiceNumber, FileName, FilePath, Extension, Status, UploadedAt, CaseID, ClientID, UserID, FirmID)
                      VALUES (?, ?, ?, ?, 'pending', NOW(), ?, ?, ?, ?)";
        $stmtUpload = $conn->prepare($sqlUpload);
        $stmtUpload->bind_param("ssssiiii", $invoiceNum, $newFileName, $destPath, $fileExtension, $caseID, $clientID, $userID, $firmID);

        if ($stmtUpload->execute()) {
            // Get the ID of the last inserted invoice
            $lastInvoiceID = $conn->insert_id;

            // Create a notification message
            $notifSubject = "New Invoice Uploaded";
            $notifText = "Invoice number $invoiceNum has been uploaded.";

            // Insert into notifications table
            $sqlNotif = "INSERT INTO notifications (NotifSubject, NotifText, IsRead, CreatedAt, UserID, ClientID)
                         VALUES (?, ?, 0, NOW(), ?, ?)";
            $stmtNotif = $conn->prepare($sqlNotif);
            $stmtNotif->bind_param("ssii", $notifSubject, $notifText, $userID, $clientID);

            if ($stmtNotif->execute()) {
                $success_msg = "Invoice uploaded successfully.";
            } else {
                $error_msg = "Notification Error.";
            }
        } else {
            $error_msg = "Failed to upload invoice.";
        }
    } else {
        $error_msg = "Failed to move the uploaded file.";
    }

    // Close statements and connection
    $stmtUpload->close();
    $stmtNotif->close();       
    }
}

?>

<div id="layoutSidenav">
    <?php include 'php/sidebar.php';?>
    <div id="layoutSidenav_content">
        <main>
            <div class="container-fluid px-4 col-md-10">
                <h1 class="mt-4">Create Invoice</h1>
                <ol class="breadcrumb mb-4">
                    <li class="breadcrumb-item"><a href="index">Bill Clients</a></li>
                    <li class="breadcrumb-item active">Create Invoice</li>
                </ol>
                <div class="row justify-content-end">
                <?php 
                if($error_msg != ''){
                    echo '<div class="alert alert-danger" role="alert">'.$error_msg.'</div>';
                }
                if($success_msg != ''){
                    echo '<div class="alert alert-success" role="alert">'.$success_msg.'</div>';
                }
                ?>
                    <form method="post" action="" enctype="multipart/form-data">
                        <!-- Input Filename and Invoice Number on same row -->
                        <div class="row mb-3">
                            <div class="col-md-6">
                                <div class="form-floating">
                                <input type="text" class="form-control" id="inputInvoiceName" name="InvoiceName" placeholder="Enter File Name" required>
                                <label for="inputInvoiceName">Enter File Name</label>
                                </div>
                            </div>

                            <div class="col-md-6">
                                <div class="form-floating">
                                <input type="text" class="form-control" id="inputInvoiceNumber" name="InvoiceNumber" placeholder="Enter Invoice Number" required>
                                <label for="inputInvoiceNumber">Enter Invoice Number</label>
                                </div>
                            </div>  
                        </div>

                        <!-- Select Case and Select Client on the same row -->
                        <div class="row mb-3">
                            <div class="col-md-6">
                                <div class="form-floating">
                                <select class="form-select" id="inputClientID" name="ClientID" aria-label="client" required>
                                    <option value="" disabled <?php echo empty($formData['ClientID']) ? 'selected' : ''; ?>>Choose Client</option>
                                    <?php
                                    $firm = $_SESSION['fid'];
                                    $clientQuery = "SELECT ClientID, CONCAT(prefix,' ',fname,' ',mname,' ',lname) AS ClientName FROM clients WHERE FirmID = $firm";
                                    $clientResult = mysqli_query($conn, $clientQuery);
                                    if ($clientResult) {
                                        while ($row = mysqli_fetch_assoc($clientResult)) {
                                            $selected = (isset($formData['ClientID']) && $row['ClientID'] == $formData['ClientID']) ? 'selected' : '';
                                            echo '<option value="' . htmlspecialchars($row['ClientID']) . '" ' . $selected . '>' . htmlspecialchars($row['ClientName']) . '</option>';
                                        }
                                    }
                                    ?>
                                </select>
                                <label for="inputClientID">Choose Client</label>
                                </div>
                            </div>

                            <div class="col-md-6">
                                <div class="form-floating">
                                <select class="form-select" id="inputCaseID" name="CaseID" required>
                                    <option value="" disabled <?php echo empty($formData['CaseID']) ? 'selected' : ''; ?>>Choose Case</option>
                                    <?php
                                    $firm = $_SESSION['fid'];
                                    $caseQuery = "SELECT CaseID, CONCAT('(',CaseNumber,')  -  ',CaseName) AS matter FROM cases WHERE FirmID = $firm";
                                    $caseResult = mysqli_query($conn, $caseQuery);
                                    if ($caseResult) {
                                        while ($row = mysqli_fetch_assoc($caseResult)) {
                                            $selected = (isset($formData['CaseID']) && $row['CaseID'] == $formData['CaseID']) ? 'selected' : '';
                                            echo '<option value="' . htmlspecialchars($row['CaseID']) . '" ' . $selected . '>' . htmlspecialchars($row['matter']) . '</option>';
                                        }
                                    }
                                    ?>
                                </select>
                                <label for="inputCaseID">Choose Case</label>
                                </div>
                            </div>
                        </div>

                        <!-- Upload the File -->
                        <div class="row mb-3">
                            <div class="col-md-6">
                                <div class="form-floating">
                                    <input class="form-control" id="uploadInvoice" type="file" name="Invoice" accept=".pdf" />
                                    <label for="uploadInvoice">Upload Invoice</label>
                                </div>
                            </div>
                        </div>

                        <div class="mt-4 mb-0 d-flex justify-content-center">
                            <button type="submit" class="btn btn-primary" name="upload-invoice">Upload Invoice</button>
                        </div>
                        <div class="mt-4 mb-0 d-flex justify-content-center">
                            <a href="bill-clients">Go to Invoices</a>
                        </div>
                    </form>
                </div>
            </div>
            </div>
        </main>
        <?php include 'php/footer.php';?>
    </div>
</div>
