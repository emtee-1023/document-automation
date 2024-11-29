<?php include 'php/header.php'; ?>

<?php
if (!isset($_SESSION['userid']) && !isset($_SESSION['fid'])) {
    header('location: firm-login');
} elseif (!isset($_SESSION['userid']) && isset($_SESSION['fid'])) {
    header('location: login');
}

$error_msg = '';
$success_msg = '';
$redirect = '';

?>

<div id="layoutSidenav">
    <?php include 'php/sidebar.php'; ?>
    <div id="layoutSidenav_content">
        <main">
            <div class="container-fluid px-4 d-flex flex-column align-items-start">
                <h1 class="mt-4">Add New Update</h1>
                <ol class="breadcrumb mb-4">
                    <li class="breadcrumb-item"><a href="case-updates">Case Updates</a></li>
                    <li class="breadcrumb-item active">Add Updates</li>
                </ol>
                <div class="row justify-content-end">
                </div>

                <div class="card shadow-sm border-0 rounded-lg mt-3 md-6 col-md-10 align-self-center d-flex flex-column">
                    <?php
                    if ($error_msg != '') {
                        echo '
                        <div class="alert alert-danger" role="alert">
                            ' . $error_msg . '
                        </div>';
                    }
                    ?>

                    <?php
                    if ($success_msg != '') {
                        echo '
                        <div class="alert alert-success" role="alert">
                            ' . $success_msg . '
                        </div>';
                    }
                    ?>
                    <div class="card-header">
                        <h3 class="text-center font-weight-light my-4">Add New Update</h3>
                    </div>
                    <div class="card-body">
                        <form method="post" action="processes.php" enctype="multipart/form-data">
                            <!--choose case and upload document on the same row -->
                            <div class="row mb-3">
                                <div class="col-md-6">
                                    <div class="form-floating mb-3">
                                        <select class="form-select" id="inputCaseID" name="caseid" required>
                                            <option value="" disabled selected>Select Case</option>
                                            <?php
                                            $user = $_SESSION['userid'];
                                            $firm = $_SESSION['fid'];

                                            // Prepare the SQL statement
                                            $stmt = $conn->prepare("
                                                                    SELECT
                                                                        c1.caseid,
                                                                        CONCAT(c2.courtname,' ',c1.casenumber, ' ', c1.casename) AS case_name 
                                                                    FROM cases c1
                                                                    JOIN courts c2 ON c1.courtid = c2.courtid 
                                                                    WHERE c1.firmid = ?");
                                            $stmt->bind_param("i", $firm); // "i" specifies the variable type as integer
                                            $stmt->execute();
                                            $caseResult = $stmt->get_result();

                                            // Fetch results and generate options
                                            while ($row = $caseResult->fetch_assoc()) {
                                                echo '<option value="' . htmlspecialchars($row['caseid']) . '">' . htmlspecialchars($row['case_name']) . '</option>';
                                            }

                                            // Close the statement
                                            $stmt->close();
                                            ?>

                                        </select>
                                        <label for="inputCaseID">Case</label>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-floating mb-3">
                                        <input class="form-control" id="document" type="file" name="Document" />
                                        <label for="deadline">Document (if needed)</label>
                                    </div>
                                </div>
                            </div>

                            <div class="row mb-3">
                                <div class="col-md-12">
                                    <div class="form-floating mb-3">
                                        <input class="form-control" id="title" type="text" name="title" required />
                                        <label for="title">Update Title</label>
                                    </div>
                                </div>
                            </div>

                            <div class="row ">
                                <div class="col-md-12">
                                    <div class="form-floating mb-3">
                                        <textarea class="form-control" id="details" name="details" rows="3" placeholder="Details" style="height: 200px;" required></textarea>
                                        <label for="details">Update Details</label>
                                    </div>
                                </div>
                            </div>


                            <div class="mt-3 mb-0 d-flex justify-content-center">
                                <div class="d-grid">
                                    <input type="submit" class="btn btn-primary" name="submit-case-update" value="Submit">
                                </div>
                            </div>
                        </form>
                    </div>
                    </main>
                    <?php include 'php/footer.php'; ?>