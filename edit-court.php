<?php
include 'php/dbconn.php';
session_start();
if (!isset($_SESSION['username'])) {
    header('location: login');
}

$error_msg = '';
$success_msg = '';

if (!isset($_GET['id'])) {
    header('location: courts');
}

$court_id = $_GET['id'];
$user = $_SESSION['userid'];

// Fetch existing court details
$fetchStmt = mysqli_prepare($conn, "SELECT courtname FROM courts WHERE courtid = ? AND added_by = ?");
if ($fetchStmt) {
    mysqli_stmt_bind_param($fetchStmt, "ii", $court_id, $user);
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
    $checkStmt = mysqli_prepare($conn, "SELECT COUNT(*) FROM courts WHERE courtname = ? AND added_by = ? AND courtid != ?");
    if ($checkStmt) {
        mysqli_stmt_bind_param($checkStmt, "sii", $court, $user, $court_id);
        mysqli_stmt_execute($checkStmt);
        mysqli_stmt_bind_result($checkStmt, $count);
        mysqli_stmt_fetch($checkStmt);
        mysqli_stmt_close($checkStmt);

        if ($count > 0) {
            // Court name exists
            $error_msg = 'Court name already exists.';
        } else {
            // Prepare and execute the update statement
            $stmt = mysqli_prepare($conn, "UPDATE courts SET courtname = ? WHERE courtid = ? AND added_by = ?");
            if ($stmt) {
                mysqli_stmt_bind_param($stmt, "sii", $court, $court_id, $user);
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

<!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="utf-8" />
        <meta http-equiv="X-UA-Compatible" content="IE=edge" />
        <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no" />
        <meta name="description" content="" />
        <meta name="author" content="" />
        <title>Edit Court | DocAuto</title>
        <link href="css/styles.css" rel="stylesheet" />
        <script src="https://use.fontawesome.com/releases/v6.3.0/js/all.js" crossorigin="anonymous"></script>
    </head>
    <body class="bg-dark">
        <div id="layoutAuthentication">
            <div id="layoutAuthentication_content">
                <main>
                    <div class="container">
                        <div class="row justify-content-center">
                            <div class="col-lg-5">
                                <div class="mt-3">
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
                                </div>
                                <div class="card shadow-lg border-0 rounded-lg mt-5">
                                    <div class="card-header"><h3 class="text-center font-weight-light my-4">Edit Court</h3></div>
                                    <div class="card-body">
                                        <form method="post" action="">
                                            <div class="form-floating mb-3">
                                                <input class="form-control" id="inputCourtName" type="text" placeholder="Edit court name" name="court" value="<?php echo htmlspecialchars($courtname); ?>" />
                                                <label for="inputCourtName">Edit Court Name</label>
                                            </div>
                                            <div class="d-flex align-items-center justify-content-between mt-4 mb-0">
                                                <input type="submit" class="btn btn-primary" name="submit" value="Update">
                                            </div>
                                        </form>
                                    </div>
                                    <div class="card-footer text-center py-3">
                                        <div class="small"><a href="courts">Back to Courts</a></div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </main>
            </div>
            <div id="layoutAuthentication_footer">
                <?php include 'php/footer.php'; ?>
            </div>
        </div>
    </body>
</html>
