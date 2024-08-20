<?php include 'php/header.php';?>

<?php
$owner = $_SESSION['userid'];
$firm = $_SESSION['fid'];

if(isset($_GET['status'])&& isset($_GET['courtid'])){
    $status = $_GET['status']; //1=open 2=pending 3=closed
    $courtid = $_GET['courtid'];

    // Use a prepared statement to avoid SQL injection
    $stmt = $conn->prepare("SELECT CourtName FROM courts WHERE firmid = ? AND courtid = ?");
    if (!$stmt) {
        die('Prepare failed: ' . $conn->error);
    }
    $stmt->bind_param("ii",$firm,$courtid);
    $stmt->execute();
    $res = $stmt->get_result();
    $row = $res->fetch_assoc();
    switch ($status) {
        case 1:
            $title = $row['CourtName'];
            $subt = "Showing Open Cases in ".$row['CourtName'];
            break;
        case 2:
            $title = $row['CourtName'];
            $subt = "Showing Pending Cases in ".$row['CourtName'];
            break;
        case 3:
            $title = $row['CourtName'];
            $subt = "Showing Closed Cases in ".$row['CourtName'];
            break;
        
        default:
            $title = 'Cases';
            $subt = "Showing all Cases";
            break;
    }
} elseif(isset($_GET['status'])&& !isset($_GET['courtid'])){
    $status = $_GET['status']; //1=open 2=pending 3=closed
    switch ($status) {
        case 1:
            $title = 'Open Cases';
            $subt = "Showing all cases marked Open";
            break;
        case 2:
            $title = 'Pending Cases';
            $subt = "Showing all cases marked Pending";
            break;
        case 3:
            $title = 'Closed Cases';
            $subt = "Showing all Cases marked Closed";
            break;
        
        default:
            $title = 'Cases';
            $subt = "Showing all Cases";
            break;
    }
} else {
    $title = "Cases";
    $subt = "Showing all Cases";
}
?>

<div id="layoutSidenav">
            <?php include 'php/sidebar.php';?>
            <div id="layoutSidenav_content">
                <main>
                    <div class="container-fluid px-4">
                        <h1 class="mt-4"><?php echo $title?></h1>
                        <ol class="breadcrumb mb-2">
                            <li class="breadcrumb-item"><a href="index">Home</a></li>
                            <li class="breadcrumb-item active"><?php echo $title;;?></li>
                        </ol>
                        <ol class="breadcrumb mb-4">
                            <li class="breadcrumb-item active"><?php echo $subt;?></li>
                        </ol>
                        <div class="row justify-content-end">
                            <div class="col-xl-3 col-md-6">
                                <div class="mt-4 mb-5 float-end">
                                    <a href="add-case" class="btn btn-secondary"><i class="fa-solid fa-briefcase"></i> Create New Case</a>
                                </div>
                            </div>
                        </div>
                    <?php include 'php/cases-table.php';?>
                    </div>
                </main>
                <?php include 'php/footer.php';?>

