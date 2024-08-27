<?php include 'php/header.php';?>

<?php
if(isset($_GET['caseid'])){
    $caseid = $_GET['caseid'];
    $owner = $_SESSION['userid'];
    $firm = $_SESSION['fid'];

    // Use a prepared statement to avoid SQL injection
    $stmt = $conn->prepare("SELECT 
                                c.CaseName,
                                cl.prefix, cl.FName, cl.MName, cl.LName
                            FROM 
                                cases c
                            JOIN
                                clients cl ON c.ClientID = cl.ClientID
                            WHERE 
                                c.CaseID = ? AND c.FirmID = ?
    ");

    $stmt->bind_param("ii", $caseid, $firm);
    $stmt->execute();
    $res = $stmt->get_result();
    $row = $res->fetch_assoc();

    $title = "Case Invoices";
    $casename = $row['CaseName'];
    $clientname = $row['Prefix'].' '.$row['FName'].' '.$row['MName'].' '.$row['LName'];
    $nav_back = "Case Invoices";
    $nav_path = "bill-clients"; 
    $nav_current = "Case ".$casename;
    $subt = "Showing pending invoices for case: ".$casename;
    $btn = '<a href="add-invoice?caseid='.$caseid.'" class="btn btn-secondary"><i class="fa-solid fa-receipt"></i> Create New Invoice</a>';
    $table = "php/bill-client-table.php";  
}else {
    $title = "Case Invoices";
    $casename = "Cases";
    $nav_back = "Home";
    $nav_path = "index";
    $nav_current = "Billing";
    $subt = "Showing cases with pending invoices";
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
                <?php if(isset($_GET['caseid'])){ include 'php/cleared-invoices-table.php';}?>
            </div>
        </main>
        <?php include 'php/footer.php';?>
    </div>
</div>
