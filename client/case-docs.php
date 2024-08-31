<?php include 'php/header.php';?>

<?php include 'notifications.php';?>

<?php
    if (isset($_GET['caseid'])){
        $caseid = $_GET['caseid'];
        $client = $_SESSION['clientid'];
        $firm = $_SESSION['fid'];
        
        // Use a prepared statement to avoid SQL injection
        $stmt = $conn->prepare("SELECT * FROM cases c WHERE clientid = ? AND caseid = ?");
        if (!$stmt) {
            die('Prepare failed: ' . $conn->error);
        }
        $stmt->bind_param("ii",$client,$caseid);
        $stmt->execute();
        $res = $stmt->get_result();
        $row = $res->fetch_assoc();
        
        $casenumber = $row['CaseNumber'];
        $casename = $row['CaseName'];


        $title = $casenumber;
        $nav_back = "Cases";
        $nav_path = "cases"; 
        $nav_current = $casename;
        $subt = "Showing all uploaded case documents under <strong>case: ".$casename."</strong>";
       

    } else {
        $title = "Case Documents";
        $nav_back = "Home";
        $nav_path = "index"; 
        $nav_current = "Cases";
        $subt = "Displaying all case Documents";
        
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
                            <li class="breadcrumb-item active"><?php echo $nav_current;?></li>
                        </ol>
                        <ol class="breadcrumb mb-4">
                            <li class="breadcrumb-item active"><?php echo $subt?></li>
                        </ol>
                        <div class="row justify-content-end">
                            <div class="col-xl-3 col-md-6">
                            </div>
                        </div>
                    <?php include 'php/docs-table.php';?>
                    </div>
                </main>
                <?php include 'php/footer.php';?>
