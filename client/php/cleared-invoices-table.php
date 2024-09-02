<?php
$success_msg = '';
$error_msg = '';

if(isset($_GET['caseid'])){
    $caseid = $_GET['caseid'];
    $client = $_SESSION['clientid'];
    $firm = $_SESSION['fid'];

    // Use a prepared statement to avoid SQL injection
    $stmt = $conn->prepare("SELECT 
                                *
                            FROM 
                                cases
                            WHERE 
                                caseid = ?
    ");

    $stmt->bind_param("i", $caseid);
    $stmt->execute();
    $res = $stmt->get_result();
    $row = $res->fetch_assoc();

    $casename = $row['CaseName'];
    $pending_title = "Showing pending invoices for case: ".$casename;
    $cleared_title = "Showing cleared invoices for case: ".$casename;

}else {
    header('location: invoices'); // Redirect to the invoices page
    exit();
}

?>
<div class="mt-3">
    <?php 
    if($error_msg != ''){
        echo
        '
        <div class="alert alert-danger" role="alert">
            '.$error_msg.'
        </div>
        ';}
    ?>
    <?php 
    if($success_msg != ''){
        echo
        '
        <div class="alert alert-success" role="alert">
            '.$success_msg.'
        </div>
        ';}
    ?>
</div>

<div class="card mb-4">
    <div class="card-header">
        <i class="fas fa-table me-1"></i>
        <?php echo $cleared_title;?>
    </div>
    <div class="table-responsive">
        <table class="table">
            <thead>
                <tr>
                    <th>Case Name</th>
                    <th>Invoice Number</th>
                    <th>Uploaded On</th>
                    <th>Download Invoice</th>
                </tr>
            </thead>
            <tbody>
                <?php
                $client = $_SESSION['clientid'];
                $firm = $_SESSION['fid'];

                // Use a prepared statement to avoid SQL injection
                $stmt = $conn->prepare("SELECT 
                                            i.*, 
                                            c.CaseName
                                        FROM 
                                            invoice_uploads i
                                        JOIN 
                                            cases c ON c.CaseID = i.CaseID
                                        WHERE 
                                            i.clientid = ? 
                                            AND i.CaseID = ?
                                            AND i.Status = 'cleared'
                            ");
                $stmt->bind_param("ii", $client, $caseid);
                $stmt->execute();
                $res = $stmt->get_result();

                if ($res->num_rows > 0) {
                    while ($row = $res->fetch_assoc()) {
                        echo '<tr>
                                <td>'.$row['CaseName'].'</td>
                                <td>'.$row['InvoiceNumber'].'</td>
                                <td>'.$row['UploadedAt'].'</td>
                                <td><a href = "../assets/files/submitted/'.$row['FileName'].'" target="_blank" class="btn btn-primary btn-sm">View Invoice</a></td>

                                <td><a href="invoice?invoiceid='.$row['InvoiceID'].'" class="btn btn-success btn-sm">Download Invoice</a></td>
                            </tr>';
                    }
                } else {
                    echo '<tr><td colspan="7" class="text-center">No records found</td></tr>';
                }
                ?>
            </tbody>
        </table>

    </div>
</div>
