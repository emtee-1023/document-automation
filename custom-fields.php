<?php include 'php/header.php'; ?>
<?php include 'notifications.php'; ?>
<?php
$fid = $_SESSION['fid'];

if (!isset($_GET['caseid'])) {
    header('location: doc-automation');
    exit();
}
$cid = $_GET['caseid'];
?>


<div id="layoutSidenav">
    <?php require 'php/sidebar.php' ?>
    <div id="layoutSidenav_content">
        <main>

            <div class="container-fluid px-4">
                <h1 class="mt-4">Custom Fields</h1>
                <ol class="breadcrumb mb-4">
                    <li class="breadcrumb-item"><a href="index">Home</a></li>
                    <li class="breadcrumb-item"><a href="doc-automation">DocAutomation</a></li>
                    <li class="breadcrumb-item active">Custom Fields</li>
                </ol>
                <div class="card mb-4">
                    <div class="card-header">
                        <i class="fas fa-table me-1"></i>
                        Copy the field codes to your word document and later submit it to initiate automation
                    </div>
                </div>

                <div class="container mt-5">
                    <div class="row justify-content-end">
                        <div class="col-xl-12 col-md-12">
                            <div class="mb-2 d-flex justify-content-end">
                                <a href="#" class="btn btn-secondary me-2" data-bs-toggle="modal" data-bs-target="#submitTemplateModal">
                                    <i class="fa-solid fa-briefcase"></i> Submit Document Template
                                </a>

                                <a href="#" class="btn btn-secondary" data-bs-toggle="modal" data-bs-target="#createFieldModal">
                                    <i class="fa-solid fa-briefcase"></i> Create New Field
                                </a>
                            </div>
                        </div>
                    </div>
                </div>

                <?php
                $stmt1 = $conn->prepare('select * from custom_fields where caseid = ?');
                $stmt1->bind_param('i', $cid);
                $stmt1->execute();
                $res = $stmt1->get_result();

                if ($res->num_rows == 0) {
                ?>
                    <div class="card col-12 p-3">
                        <div class="form-group">
                            <div class="border p-2 col-3">No custom fields to display</div>
                        </div>
                        <?php
                    } else {
                        while ($row = $res->fetch_assoc()) {
                        ?>
                            <div class="card col-12 p-3 mb-3">
                                <div class="form-group">
                                    <div class="border p-2 col-3"><?= htmlspecialchars($row['CFName']) ?></div>
                                </div>
                                <div class="row">
                                    <div class="col">
                                        <div class="border p-2" id="cfCodeDiv_<?= $row['CFID'] ?>"><?= htmlspecialchars($row['CFCode']) ?></div>
                                    </div>
                                    <div class="col">
                                        <div class="border p-2"><?= htmlspecialchars($row['CFValue']) ?></div>
                                    </div>
                                    <div class="col-auto">
                                        <button type="button" class="btn btn-secondary fa-regular fa-clipboard" onclick="copyToClipboard('<?= htmlspecialchars($row['CFCode']) ?>')"></button>
                                    </div>
                                </div>
                            </div>
                    <?php
                        }
                    }



                    ?>



                    </div>
        </main>
        <footer class=" py-4 bg-light mt-auto">
            <div class="container-fluid px-4">
                <div class="d-flex align-items-center justify-content-between small">
                    <div class="text-muted">Copyright &copy; Law Project</div>
                    <!-- <div>
                                <a href="#">Privacy Policy</a>
                                &middot;
                                <a href="#">Terms &amp; Conditions</a>
                            </div> -->
                </div>
            </div>
        </footer>
    </div>
</div>
<!-- Modal -->
<div class="modal fade" id="createFieldModal" tabindex="-1" aria-labelledby="createFieldModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="createFieldModalLabel">Create New Field</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <!-- Your form or content here -->
                <form action="add-field.php" method="post">
                    <input type="text" name="caseid" value="<?= $cid ?>" hidden>
                    <div class="mb-3">
                        <label for="fieldName" class="form-label">Field Name (Avoid numeric values eg.1,2,3 etc)</label>
                        <input type="text" class="form-control" id="fieldName" name="fieldname" required>
                    </div>
                    <div class="mb-3">
                        <label for="fieldDescription" class="form-label">Field Value (Data)</label>
                        <textarea class="form-control" id="fieldDescription" rows="3" name="fieldvalue" required></textarea>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                        <input type="submit" value="Save Changes" name="submit" class="btn btn-primary">
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<!-- Modal for Submitting Document Template -->
<div class="modal fade" id="submitTemplateModal" tabindex="-1" aria-labelledby="submitTemplateModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="submitTemplateModalLabel">Submit Document Template</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form action="submit-template.php" method="post" enctype="multipart/form-data">
                    <div class="mb-3">
                        <label for="templateFile" class="form-label">Upload Document Template</label>
                        <input type="file" class="form-control" id="templateFile" name="templateFile" required>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                        <input type="submit" value="Submit Template" class="btn btn-primary">
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>




<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/js/bootstrap.bundle.min.js" crossorigin="anonymous"></script>
<script src="js/scripts.js"></script>
<script src="https://cdn.jsdelivr.net/npm/simple-datatables@7.1.2/dist/umd/simple-datatables.min.js" crossorigin="anonymous"></script>
<script src="js/datatables-simple-demo.js"></script>
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script>
    function copyToClipboard(cfCode) {
        // Copy the content to the clipboard
        navigator.clipboard.writeText(cfCode)
            .then(() => {
                console.log('Copied to clipboard: ', cfCode);
            })
            .catch(err => {
                console.error('Failed to copy: ', err);
            });
    }
</script>
</body>

</html>