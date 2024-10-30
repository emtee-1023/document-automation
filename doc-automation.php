<?php include 'php/header.php'; ?>
<?php include 'notifications.php'; ?>
<?php
$fid = $_SESSION['fid'];
?>


<div id="layoutSidenav">
    <?php require 'php/sidebar.php' ?>
    <div id="layoutSidenav_content">
        <main>

            <div class="container-fluid px-4">
                <h1 class="mt-4">Document Automation</h1>
                <ol class="breadcrumb mb-4">
                    <li class="breadcrumb-item"><a href="index.php">Home</a></li>
                    <li class="breadcrumb-item active">Document Automation</li>
                </ol>
                <div class="card mb-4">
                    <div class="card-header">
                        <i class="fas fa-table me-1"></i>
                        Choose a case to generate a document
                    </div>
                    <div class="card-body">
                        <table id="datatablesSimple">
                            <thead>
                                <tr>
                                    <th>CaseNumber</th>
                                    <th>CaseName</th>
                                    <th>CaseDescription</th>
                                    <th>GeneratedDocuments</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php
                                $stmt1 = $conn->prepare('SELECT 
                                c.caseid,
                                c.casenumber, 
                                c.casename, 
                                c.casedescription,
                                COUNT(gd.gdid) AS doc_count
                            FROM 
                                cases c
                            LEFT JOIN 
                                generated_docs gd ON gd.caseid = c.caseid
                            WHERE 
                                c.firmid = ?
                            GROUP BY 
                                c.casenumber, c.casename, c.casedescription;
                            ');
                                $stmt1->bind_param('i', $fid);
                                $stmt1->execute();
                                $res1 = $stmt1->get_result();

                                while ($row = $res1->fetch_assoc()) {
                                ?>
                                    <tr>
                                        <td><?= htmlspecialchars($row['casenumber']) ?></td>
                                        <td><a href="custom-fields?caseid=<?= htmlspecialchars($row['caseid']) ?>"><?= htmlspecialchars($row['casename']) ?></a></td>
                                        <td><?= htmlspecialchars($row['casedescription']) ?></td>
                                        <td><?= $row['doc_count'] ?></td>
                                    </tr>
                                <?php
                                }
                                ?>

                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </main>
        <footer class="py-4 bg-light mt-auto">
            <div class="container-fluid px-4">
                <div class="d-flex align-items-center justify-content-between small">
                    <div class="text-muted">Copyright &copy; InLaw</div>
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
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/js/bootstrap.bundle.min.js" crossorigin="anonymous"></script>
<script src="js/scripts.js"></script>
<script src="https://cdn.jsdelivr.net/npm/simple-datatables@7.1.2/dist/umd/simple-datatables.min.js" crossorigin="anonymous"></script>
<script src="js/datatables-simple-demo.js"></script>
</body>

</html>