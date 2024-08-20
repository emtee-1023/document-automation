<?php
$success_msg = '';
$error_msg = '';

if(isset($_GET['clientid'])){
    $clientid = $_GET['clientid'];
    $owner =  $_SESSION['userid'];

    // Use a prepared statement to avoid SQL injection
    $stmt = $conn->prepare("SELECT 
                                *
                            FROM 
                                clients
                            WHERE 
                                clientid = ? and belong_to = ?
    ");

    $stmt->bind_param("ii",$clientid,$owner);
    $stmt->execute();
    $res = $stmt->get_result();
    $row = $res->fetch_assoc();

    $clientname = $row['FName'].' '.$row['MName'].' '.$row['LName'];
    $table_title = "Showing pending invoices by ".$clientname;

}else {
    header('location: bill-clients');
    exit();
}

?>
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

<div class="card mb-4">
    <div class="card-header">
        <i class="fas fa-table me-1"></i>
        <?php echo $table_title;?>
    </div>
    <div class="card-body">
        <table id="datatablesSimple">
            <thead>
                <tr>
                    <th>Case Name</th>
                    <th>Invoice Number</th>
                    <th>Created On</th>
                    <th>Mark Cleared</th>
                    <th>Download Invoice</th>
                    <th>Email Invoice</th>
                </tr>
            </thead>
            <tbody>
            <?php
            $owner = $_SESSION['userid'];
            // Use a prepared statement to avoid SQL injection
            $stmt = $conn->prepare("SELECT 
                                        i.*, 
                                        c.casename
                                    FROM 
                                        invoices i
                                    JOIN 
                                        cases c ON c.caseid = i.caseid
                                    WHERE 
                                        c.userid = ? 
                                        AND c.clientid = ?
                    ");
            $stmt->bind_param("ii",$owner,$clientid);
            $stmt->execute();
            $res = $stmt->get_result();

            while ($row = $res->fetch_assoc()) {
                echo '<tr>
                        <td>'.$row['casename'].'</td>
                        <td>'.$row['InvoiceNumber'].'</td>
                        <td>'.$row['Created_on'].'</td>
                        <td><a href="edit-doc?id='.$row['InvoiceID'].'" class="btn btn-primary btn-sm">Mark as Cleared</a></td>
                        <td><a href="fpff" class="btn btn-success btn-sm">Download Invoice</a></td>
                        <td><a href="?fileid='.$row['InvoiceID'].'" class="btn btn-success btn-sm">Email Invoice</a></td>
                                                    
                    </tr>';
            }
            ?>

            </tbody>
        </table>
    </div>
</div>