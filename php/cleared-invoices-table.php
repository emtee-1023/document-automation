<?php
$success_msg = '';
$error_msg = '';

if(isset($_GET['caseid'])){
    $caseid = $_GET['caseid'];
    $owner = $_SESSION['userid'];
    $firm = $_SESSION['fid'];

    // Use a prepared statement to avoid SQL injection
    $stmt = $conn->prepare("SELECT 
                                *
                            FROM 
                                cases
                            WHERE 
                                caseid = ? AND firmid = ?
    ");

    $stmt->bind_param("ii", $caseid, $firm);
    $stmt->execute();
    $res = $stmt->get_result();
    $row = $res->fetch_assoc();

    $casename = $row['CaseName'];
    $pending_title = "Showing pending invoices for case: ".$casename;
    $cleared_title = "Showing cleared invoices for case: ".$casename;

}else {
    header('location: bill-cases'); // Redirect to the cases page
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
    <div class="card-body">
        <table id="datatablesSimple">
            <thead>
                <tr>
                    <th>Case Name</th>
                    <th>Invoice Number</th>
                    <th>Created On</th>
                    <th>Mark Pending</th>
                    <th>Download Invoice</th>
                    <th>Email Invoice</th>
                    <th>Delete Invoice</th>
                </tr>
            </thead>
            <tbody>
            <?php
            $owner = $_SESSION['userid'];
            $firm = $_SESSION['fid'];
            // Use a prepared statement to avoid SQL injection
            $stmt = $conn->prepare("SELECT 
                                        i.*, 
                                        c.CaseName
                                    FROM 
                                        invoices i
                                    JOIN 
                                        cases c ON c.CaseID = i.CaseID
                                    WHERE 
                                        c.FirmID = ? 
                                        AND i.CaseID = ?
                                        AND i.Status = 'cleared'
                    ");
            $stmt->bind_param("ii", $firm, $caseid);
            $stmt->execute();
            $res = $stmt->get_result();

            while ($row = $res->fetch_assoc()) {
                echo '<tr>
                        <td>'.$row['CaseName'].'</td>
                        <td>'.$row['InvoiceNumber'].'</td>
                        <td>'.$row['CreatedAt'].'</td>
                        <td><a href="pending-invoice?invid='.$row['InvoiceID'].'&caseid='.$caseid.'" class="btn btn-primary btn-sm">Mark as Pending</a></td>
                        <td><a href="invoice?invoiceid='.$row['InvoiceID'].'" class="btn btn-success btn-sm">Download Invoice</a></td>
                        <td><a href="?fileid='.$row['InvoiceID'].'" class="btn btn-success btn-sm">Email Invoice</a></td>
                        <td><a href="delete?invoiceid='.$row['InvoiceID'].'&caseid='.$caseid.'" class="btn btn-danger btn-sm">Delete Invoice</a></td>          
                    </tr>';
            }
            ?>
            </tbody>
        </table>
    </div>
</div>
