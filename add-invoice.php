<?php include 'php/header.php';?>

<?php
    // Initialize item count for adding new items
    if (!isset($_SESSION['item_count'])) {
        $_SESSION['item_count'] = 1; // Start with 1 item
    }

    // Handle form submission for saving the invoice
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        if (isset($_POST['save-invoice'])) {
            // Retrieve form data
            $InvoiceNumber = "Inv/".date('Y')."/".round(microtime(true)*1000);
            $CreatedOn = date('d/m/Y');
            $LastUpdate = date('d/m/Y');
            $caseID = $_POST['CaseID'];
            $clientID = $_POST['ClientID'];
            $userid = $_SESSION['userid'];
            $serviceIDs = $_POST['ServiceID'];
            $quantities = $_POST['Quantity'];

            // Start transaction
            mysqli_begin_transaction($conn);

            try {
                // Insert the invoice into the invoices table
                $stmt = $conn->prepare("
                    INSERT INTO invoices (InvoiceNumber, Created_on, Last_update, File, CaseID, ClientID, UserID, FirmID)
                    VALUES (?, ?, ?, ?, ?, ?, ?, ?)
                ");
                $dummyFile = 'x'; // Dummy file value
                $firmID = 1; // Dummy firm ID
                $stmt->bind_param("ssssiiii", $InvoiceNumber, $CreatedOn, $LastUpdate, $dummyFile, $caseID, $clientID, $userid, $firmID);
                $stmt->execute();

                // Get the last inserted invoice ID
                $invoiceID = $conn->insert_id;

                // Insert each invoice item into the invoice_items table
                $stmt = $conn->prepare("
                    INSERT INTO invoice_items (InvoiceID, ServiceID, Quantity)
                    VALUES (?, ?, ?)
                ");
                
                foreach ($serviceIDs as $index => $serviceID) {
                    $quantity = $quantities[$index];
                    $stmt->bind_param("iii", $invoiceID, $serviceID, $quantity);
                    $stmt->execute();
                }

                // Commit the transaction
                mysqli_commit($conn);

                $success_msg = "Invoice generated successfully";

                // Reset item count after saving the invoice
                unset($_SESSION['item_count']);
                unset($_SESSION['form_data']);
            } catch (Exception $e) {
                // Rollback the transaction if something failed
                mysqli_rollback($conn);
                $error_msg = "Failed to generate invoice: " . $e->getMessage();
            }
        } elseif (isset($_POST['add-item'])) {
            // Preserve form data
            $_SESSION['form_data'] = [
                'CaseID' => $_POST['CaseID'] ?? '',
                'ClientID' => $_POST['ClientID'] ?? '',
                'ServiceID' => $_POST['ServiceID'] ?? [],
                'Quantity' => $_POST['Quantity'] ?? []
            ];

            // Increment item count when 'Add Another Item' is clicked
            $_SESSION['item_count']++;
        }
    } else {
        // Initialize form data if not POST request
        $formData = $_SESSION['form_data'] ?? [
            'CaseID' => '',
            'ClientID' => '',
            'ServiceID' => [],
            'Quantity' => []
        ];
        $itemCount = $_SESSION['item_count'] ?? 1;
    }

    // Get the item count
    $itemCount = $_SESSION['item_count'] ?? 1;
?>

<div id="layoutSidenav">
    <?php include 'php/sidebar.php';?>
    <div id="layoutSidenav_content">
        <main>
            <div class="container-fluid px-4">
                <h1 class="mt-4">Create Invoice</h1>
                <ol class="breadcrumb mb-4">
                    <li class="breadcrumb-item"><a href="index">Bill Clients</a></li>
                    <li class="breadcrumb-item active">Create Invoice</li>
                </ol>
                <div class="row justify-content-end">
                    <form method="post" action="">
                        <!-- Select Case and Select Client on same row -->
                        <div class="row mb-3">
                            <div class="col-md-6">
                                <div class="form-floating">
                                    <select class="form-select" id="inputClientID" name="ClientID" aria-label="client" required>
                                        <option value="" disabled>Choose Client</option>
                                        <?php
                                        $caseQuery = "SELECT ClientID, concat(prefix,' ',fname,' ',mname,' ',lname) as ClientName FROM clients";
                                        $caseResult = mysqli_query($conn, $caseQuery);
                                        if ($caseResult) {
                                            while ($row = mysqli_fetch_assoc($caseResult)) {
                                                $selected = ($row['ClientID'] == $formData['ClientID']) ? 'selected' : '';
                                                echo '<option value="' . htmlspecialchars($row['ClientID']) . '" ' . $selected . '>' . htmlspecialchars($row['ClientName']) . '</option>';
                                            }
                                        }
                                        ?>
                                    </select>
                                    <label for="inputClientID">Client</label>
                                </div>
                            </div>

                            <div class="col-md-6">
                                <div class="form-floating">
                                    <select class="form-select" id="inputCaseID" name="CaseID">
                                        <option value="" disabled>Choose Case</option>
                                        <?php
                                        $caseQuery = "SELECT CaseID, CaseName FROM cases";
                                        $caseResult = mysqli_query($conn, $caseQuery);
                                        if ($caseResult) {
                                            while ($row = mysqli_fetch_assoc($caseResult)) {
                                                $selected = ($row['CaseID'] == $formData['CaseID']) ? 'selected' : '';
                                                echo '<option value="' . htmlspecialchars($row['CaseID']) . '" ' . $selected . '>' . htmlspecialchars($row['CaseName']) . '</option>';
                                            }
                                        }
                                        ?>
                                    </select>
                                    <label for="inputCase">Case</label>
                                </div>
                            </div>
                        </div>

                        <h2>Invoice Items</h2>

                        <?php
                        for ($i = 0; $i < $itemCount; $i++) {
                            $serviceID = $formData['ServiceID'][$i] ?? '';
                            $quantity = $formData['Quantity'][$i] ?? 1;
                            ?>
                            <div class="row mb-3">
                                <div class="col-md-6">
                                    <div class="form-floating mb-3 mb-md-0">
                                        <select class="form-select" id="inputService<?= $i ?>" name="ServiceID[]" aria-label="Service" required>
                                            <option value="" disabled>Choose Services</option>
                                            <?php
                                            $serviceQuery = "SELECT ServiceID, ServiceDescription FROM services";
                                            $serviceResult = mysqli_query($conn, $serviceQuery);
                                            if ($serviceResult) {
                                                while ($row = mysqli_fetch_assoc($serviceResult)) {
                                                    $selected = ($row['ServiceID'] == $serviceID) ? 'selected' : '';
                                                    echo '<option value="' . htmlspecialchars($row['ServiceID']) . '" ' . $selected . '>' . htmlspecialchars($row['ServiceDescription']) . '</option>';
                                                }
                                            }
                                            ?>
                                        </select>
                                        <label for="inputService<?= $i ?>">Service</label>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-floating">
                                        <input class="form-control" id="inputQuantity<?= $i ?>" type="number" placeholder="Enter quantity" name="Quantity[]" value="<?= htmlspecialchars($quantity) ?>" required />
                                        <label for="inputQuantity<?= $i ?>">Quantity</label>
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
