<?php include 'php/header.php';?>

<?php
$error_msg = '';
$success_msg = '';
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    if (isset($_POST['save-invoice'])) {
        // Start transaction
        mysqli_begin_transaction($conn);

        try {
            // Insert the invoice into the `invoices` table
            $clientID = $_POST['ClientID'];
            $caseID = $_POST['CaseID'];
            $createdAt = $_POST['CreatedAt'];
            $expiresAt = $_POST['ExpiresAt']; 
            $userID = $_SESSION['userid'];  
            $firmID = $_SESSION['fid'];  
            $invoiceNumber = generateInvoiceNumber();  // Custom function to generate invoice number
            

            $insertInvoiceQuery = "INSERT INTO invoices (InvoiceNumber, CreatedAt, ExpiresAt, Status, CaseID, ClientID, UserID, FirmID) 
                                   VALUES ('$invoiceNumber', '$createdAt', '$expiresAt', 'pending', '$caseID', '$clientID', '$userID', '$firmID')";
            mysqli_query($conn, $insertInvoiceQuery);
            $invoiceID = mysqli_insert_id($conn);

            // Insert each invoice item into the `invoice_items` table
            foreach ($_POST['Description'] as $index => $description) {
                $amount = $_POST['Amount'][$index];
                $insertItemQuery = "INSERT INTO invoice_items (InvoiceID, Description, Amount) 
                                    VALUES ('$invoiceID', '" . mysqli_real_escape_string($conn, $description) . "', '" . mysqli_real_escape_string($conn, $amount) . "')";
                mysqli_query($conn, $insertItemQuery);
            }

            // Commit transaction
            mysqli_commit($conn);

            // Redirect or display a success message
            $success_msg = "invoice generated successfuly";
        } catch (Exception $e) {
            // Rollback transaction on error
            mysqli_rollback($conn);
            $error_msg = "Failed to save invoice: " . $e->getMessage();
        }
    } elseif (isset($_POST['add-item'])) {
        // Logic to handle adding another item by redisplaying the form with the current data
        $formData = $_POST;
        $formData['Description'][] = '';  // Add a new blank item
        $formData['Amount'][] = '';
    }
}

function generateInvoiceNumber() {
    return 'INV-' . strtoupper(uniqid());
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
                    <form method="post" action="">
                        <!-- Select Case and Select Client on the same row -->
                        <div class="row mb-3">
                            <div class="col-md-6">
                                <div class="form-floating">
                                    <select class="form-select" id="inputClientID" name="ClientID" aria-label="client" required>
                                        <option value="" disabled selected>Choose Client</option>
                                        <?php
                                        $firm = $_SESSION['fid'];
                                        $clientQuery = "SELECT ClientID, concat(prefix,' ',fname,' ',mname,' ',lname) as ClientName FROM clients where FirmID = $firm";
                                        $clientResult = mysqli_query($conn, $clientQuery);
                                        if ($clientResult) {
                                            while ($row = mysqli_fetch_assoc($clientResult)) {
                                                $selected = ($row['ClientID'] == $formData['ClientID']) ? 'selected' : '';
                                                echo '<option value="' . htmlspecialchars($row['ClientID']) . '">' . htmlspecialchars($row['ClientName']) . '</option>';
                                            }
                                        }
                                        ?>
                                    </select>
                                    <label for="inputClientID">Choose Client</label>
                                </div>
                            </div>

                            <div class="col-md-6">
                                <div class="form-floating">
                                    <select class="form-select" id="inputCaseID" name="CaseID">
                                        <option value="" disabled selected>Choose Case</option>
                                        <?php
                                        $firm = $_SESSION['fid'];
                                        $caseQuery = "SELECT CaseID, CONCAT('(',CaseNumber,')  -  ',CaseName) as matter FROM cases where FirmID =  $firm";
                                        $caseResult = mysqli_query($conn, $caseQuery);
                                        if ($caseResult) {
                                            while ($row = mysqli_fetch_assoc($caseResult)) {
                                                echo '<option value="' . htmlspecialchars($row['CaseID']) . '">' . htmlspecialchars($row['matter']) . '</option>';
                                            }
                                        }
                                        ?>
                                    </select>
                                    <label for="inputCaseID">Choose Case</label>
                                </div>
                            </div>
                        </div>

                        <!-- Select creation date and expiry date on the same row -->
                        <div class="row mb-3">
                            <div class="col-md-6">
                                <div class="form-floating">
                                    <input type="date" class="form-control" id="inputCreatedAt" name="CreatedAt" required>
                                    <label for="inputCreatedAt">Date Created</label>
                                </div>
                            </div>

                            <div class="col-md-6">
                                <div class="form-floating">
                                    <input type="date" class="form-control" id="inputExpiresAt" name="ExpiresAt" required>
                                    <label for="inputExpiresAt">Expiry Date</label>
                                </div>
                            </div>
                        </div>


                        <h2>Invoice Items</h2>

                        <?php
                        $itemCount = isset($formData['Description']) ? count($formData['Description']) : 1;
                        for ($i = 0; $i < $itemCount; $i++) {
                            $description = $formData['Description'][$i] ?? '';
                            $amount = $formData['Amount'][$i] ?? '';
                        ?>
                            <div class="row mb-3">
                                <div class="col-md-6">
                                    <div class="form-floating mb-3 mb-md-0">
                                        <input type="text" class="form-control" id="inputDescription<?= $i ?>" name="Description[]" placeholder="Item Description" value="<?= htmlspecialchars($description) ?>" required>
                                        <label for="inputDescription<?= $i ?>">Item Description</label>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-floating">
                                        <input type="text" class="form-control" id="inputAmount<?= $i ?>" name="Amount[]" placeholder="Amount" value="<?= htmlspecialchars($amount) ?>" required>
                                        <label for="inputAmount<?= $i ?>">Amount</label>
                                    </div>
                                </div>
                            </div>
                        <?php
                        }
                        ?>

                        <div class="mt-4 mb-0 d-flex justify-content-center">
                            <button type="submit" class="btn btn-secondary me-2" name="add-item">Add Another Item</button>
                            <button type="submit" class="btn btn-primary" name="save-invoice">Save Invoice</button>
                        </div>
                    </form>
 
                </div>
            </div>
        </main>
        <?php include 'php/footer.php';?>
    </div>
</div>
