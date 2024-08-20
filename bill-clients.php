<?php include 'php/header.php';?>

<?php
if(isset($_GET['clientid'])){
    $clientid = $_GET['clientid'];
    $owner =  $_SESSION['userid'];

    // Use a prepared statement to avoid SQL injection
    //motifications for client -- email
    
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

    $title = "Invoice Clients";
    $clientname = $row['FName'].' '.$row['MName'].' '.$row['LName'];
    $nav_back = "Invoice Clients";
    $nav_path = "bill-clients";
    $nav_current = "Bill ".$clientname;
    $subt = "Showing pending invoices by ".$clientname;
    $btn = '<a href="add-invoice?id='.$clientid.'" class="btn btn-secondary"><i class="fa-solid fa-receipt"></i> Create New Invoice</a>';
    $table = "php/bill-client-table.php";
}else {
    $title = "Invoice Clients";
    $clientname = "Clients";
    $nav_back = "Home";
    $nav_path = "index";
    $nav_current = "Billing";
    $subt = "Showing clients with pending invoices";
    $btn = '<a href="add-invoice" class="btn btn-secondary"><i class="fa-solid fa-receipt"></i> Create New Invoice</a>';
    $table = "php/bill-table.php";
}

if($_SESSION['user_type']=='client'){
    $btn = '<a href="add-invoice" class="btn btn-secondary"><i class="fa-solid fa-receipt"></i> Upload Proof Of Payment</a>';
}


?>

<div id="layoutSidenav">
            <?php include 'php/sidebar.php';?>
            <div id="layoutSidenav_content">
                <main>
                    <div class="container-fluid px-4">
                        <h1 class="mt-4"><?php echo $title?></h1>
                        <ol class="breadcrumb mb-2">
                            <li class="breadcrumb-item"><a href="<?php echo $nav_path?>"><?php echo $nav_back?></a></li>
                            <li class="breadcrumb-item active"><?php echo $nav_current?></li>
                        </ol>
                        <ol class="breadcrumb mb-4">
                            <li class="breadcrumb-item active"><?php echo $subt?></li>
                        </ol>
                        <div class="row justify-content-end">
                            <div class="col-xl-3 col-md-6">
                                <div class="mt-4 mb-5 float-end">
                                    <?php echo $btn;?>
                                    
                                </div>
                            </div>
                        </div>
                    <?php include $table;?>
                    </div>
                </main>
                <?php include 'php/footer.php';?>
