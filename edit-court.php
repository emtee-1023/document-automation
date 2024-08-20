<?php include 'php/header.php';?>

<?php
if(!isset($_SESSION['userid']) && !isset($_SESSION['fid'])){
    header('location: firm-login');
} elseif(!isset($_SESSION['userid']) && isset($_SESSION['fid'])){
    header('location: login');
}

$error_msg = '';
$success_msg = '';

if (!isset($_GET['id'])) {
    header('location: courts');
}

$court_id = $_GET['id'];
$user = $_SESSION['userid'];
$firm = $_SESSION['fid'];

// Fetch existing court details
$fetchStmt = mysqli_prepare($conn, "SELECT courtname FROM courts WHERE courtid = ? AND firmid = ?");
if ($fetchStmt) {
    mysqli_stmt_bind_param($fetchStmt, "ii", $court_id, $firm);
    mysqli_stmt_execute($fetchStmt);
    mysqli_stmt_bind_result($fetchStmt, $courtname);
    mysqli_stmt_fetch($fetchStmt);
    mysqli_stmt_close($fetchStmt);
} else {
    $error_msg = 'Error fetching court details.';
}

if (isset($_POST['submit'])) {
    $court = $_POST['court'];

    // Check if court name already exists for another court
    $checkStmt = mysqli_prepare($conn, "SELECT COUNT(*) FROM courts WHERE courtname = ? AND firmid = ? AND courtid != ?");
    if ($checkStmt) {
        mysqli_stmt_bind_param($checkStmt, "sii", $court, $firm, $court_id);
        mysqli_stmt_execute($checkStmt);
        mysqli_stmt_bind_result($checkStmt, $count);
        mysqli_stmt_fetch($checkStmt);
        mysqli_stmt_close($checkStmt);

        if ($count > 0) {
            // Court name exists
            $error_msg = 'Court name already exists within your firm.';
        } else {
            // Prepare and execute the update statement
            $stmt = mysqli_prepare($conn, "UPDATE courts SET courtname = ?, userid = ?, firmid = ? WHERE courtid = ? AND firmid = ?");
            if ($stmt) {
                mysqli_stmt_bind_param($stmt, "siiii", $court, $user, $firm, $court_id, $firm);
                mysqli_stmt_execute($stmt);
                mysqli_stmt_close($stmt);
                $success_msg = 'Court name updated successfully!';
                $courtname = $court;
            } else {
                // Error preparing the statement
                $error_msg = 'Error preparing the SQL statement.';
            }
        }
    } else {
        // Error preparing the check statement
        $error_msg = 'Error preparing the SQL statement to check for duplicates.';
    }
}
?>

<div id="layoutSidenav">
            <?php include 'php/sidebar.php';?>
            <div id="layoutSidenav_content">
                <main>
                    <div class="container-fluid px-4 d-flex flex-column align-items-start">
                        <h1 class="mt-4">Edit Courts</h1>
                        <ol class="breadcrumb mb-4">
                            <li class="breadcrumb-item"><a href="courts">Courts</a></li>
                            <li class="breadcrumb-item active">Edit Court</li>
                        </ol>
                        <div class="row justify-content-end">
                        </div>

                        <div class="card shadow-lg border-0 rounded-lg mt-3 md-6 col-md-6 align-self-center d-flex flex-column">
                            <?php 
                            if ($error_msg != '') {
                                echo '
                                <div class="alert alert-danger" role="alert">
                                    '.$error_msg.'
                                </div>';
                            }
                            ?>

                            <?php 
                            if ($success_msg != '') {
                                echo '
                                <div class="alert alert-success" role="alert">
                                    '.$success_msg.'
                                </div>';
                            }
                            ?>
                            <div class="card-header"><h3 class="text-center font-weight-light my-4">Change Court Name</h3></div>
                            <div class="card-body">
                                <form class="d-flex flex-column " method="post" action="">
                                    <div class="form-floating mb-3">
                                        <input class="form-control" id="inputCourtName" type="text" placeholder="Edit court name" name="court" value="<?php echo htmlspecialchars($courtname); ?>" />
                                        <label for="inputCourtName">Edit Court Name</label>
                                    </div>
                                    <div class=" mb-0 align-self-center">
                                        <input type="submit" class="btn btn-primary" name="submit" value="Update">
                                    </div>
                                </form>
                            </div>
                            <div class="card-footer text-center py-3">
                                <div class="small"><a href="courts">Back to Courts</a></div>
                            </div>
                        </div>

                    
                    </div>
                </main>
                <?php include 'php/footer.php';?>

                                    
      
