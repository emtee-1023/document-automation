<?php include 'php/header.php'; ?>

<?php include 'notifications.php'; ?>


<div id="layoutSidenav">
    <?php include 'php/sidebar.php'; ?>
    <div id="layoutSidenav_content">
        <main>
            <div class="container-fluid px-4">
                <h1 class="mt-4">Case Updates</h1>
                <ol class="breadcrumb mb-4">
                    <li class="breadcrumb-item"><a href="index">Home</a></li>
                    <li class="breadcrumb-item active">Case Updates</li>
                </ol>
                <ol class="breadcrumb mb-2">
                    <li class="breadcrumb-item active">Update Your Client</li>
                </ol>

                <div class="container mt-4">
                    <ul class="nav nav-tabs" id="taskTabs" role="tablist">
                        <li class="nav-item" role="presentation">
                            <button class="nav-link active" id="tab1-tab" data-bs-toggle="tab" data-bs-target="#tab1" type="button" role="tab" aria-controls="tab1" aria-selected="true">
                                Open Cases
                            </button>
                        </li>
                        <li class="nav-item" role="presentation">
                            <button class="nav-link" id="tab2-tab" data-bs-toggle="tab" data-bs-target="#tab2" type="button" role="tab" aria-controls="tab2" aria-selected="false">
                                Pending Cases
                            </button>
                        </li>
                        <li class="nav-item" role="presentation">
                            <button class="nav-link" id="tab3-tab" data-bs-toggle="tab" data-bs-target="#tab3" type="button" role="tab" aria-controls="tab3" aria-selected="false">
                                Closed Cases
                            </button>
                        </li>
                    </ul>
                    <div class="tab-content" id="taskTabsContent">
                        <!-- Tab 1 -->
                        <div class="tab-pane fade show active" id="tab1" role="tabpanel" aria-labelledby="tab1-tab">
                            <!-- Existing table structure for Assigned Tasks -->
                            <div class="card mb-4">
                                <div class="card-header">
                                    <i class="fas fa-table me-1"></i>
                                    Displaying updates for open cases
                                </div>
                                <div class="card-body">
                                    <table id="datatablesCompleted" class="table table-bordered table-striped">
                                        <!-- Existing table structure -->
                                        <thead>
                                            <tr>
                                                <th>Case</th>
                                                <th>Update</th>
                                                <th>Added By</th>
                                                <th>Added On</th>
                                                <th>View</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <?php
                                            // User and firm session variables
                                            $clientid = $_SESSION['clientid'];
                                            $firmid = $_SESSION['fid'];

                                            // SQL query using prepared statements
                                            $stmt = $conn->prepare("
                                                    SELECT
                                                        c3.updateid, 
                                                        concat(c1.courtname,' ',c2.casenumber,' ',c2.casename) as case_name,
                                                        c3.title,
                                                        concat(u.fname,' ',u.lname) as updater,
                                                        c3.createdat
                                                    FROM case_updates c3 
                                                    JOIN cases c2 ON c2.caseid = c3.caseid
                                                    JOIN courts c1 ON c2.courtid = c1.courtid
                                                    JOIN users u ON u.userid = c3.userid
                                                    WHERE c2.casestatus = 'open' AND c2.clientid = ?
                                                ");
                                            $stmt->bind_param("i", $clientid);
                                            $stmt->execute();
                                            $res = $stmt->get_result();

                                            // Populate table rows with fetched data
                                            while ($row = $res->fetch_assoc()) {
                                                echo '
                                                <tr>
                                                    <td>' . htmlspecialchars($row['case_name']) . '</td>
                                                    <td>' . htmlspecialchars($row['title']) . '</td>
                                                    <td>' . htmlspecialchars($row['updater']) . '</td>
                                                    <td>' . htmlspecialchars(date('D d M Y \a\t h.iA', strtotime($row['createdat']))) . '</td>
                                                    <td><a href="case-update-details?id=' . $row['updateid'] . '" class="btn btn-primary btn-sm">View Details</a ></td>
                                                </tr>';
                                            }
                                            ?>
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>

                        <!-- Tab 2 -->
                        <div class="tab-pane fade" id="tab2" role="tabpanel" aria-labelledby="tab2-tab">
                            <div class="card mb-4">
                                <div class="card-header">
                                    <i class="fas fa-table me-1"></i>
                                    Displaying Updates for Pending Cases
                                </div>
                                <div class="card-body">
                                    <table id="datatablesCompleted" class="table table-bordered table-striped">
                                        <thead>
                                            <tr>
                                                <th>Case</th>
                                                <th>Update</th>
                                                <th>Added By</th>
                                                <th>Added On</th>
                                                <th>View</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <?php
                                            // SQL query using prepared statements
                                            $stmt = $conn->prepare("
                                                    SELECT
                                                        c3.updateid, 
                                                        concat(c1.courtname,' ',c2.casenumber,' ',c2.casename) as case_name,
                                                        c3.title,
                                                        concat(u.fname,' ',u.lname) as updater,
                                                        c3.createdat
                                                    FROM case_updates c3 
                                                    JOIN cases c2 ON c2.caseid = c3.caseid
                                                    JOIN courts c1 ON c2.courtid = c1.courtid
                                                    JOIN users u ON u.userid = c3.userid
                                                    WHERE c2.casestatus = 'pending' AND c2.clientid = ?
                                                ");
                                            $stmt->bind_param("i", $clientid);
                                            $stmt->execute();
                                            $res = $stmt->get_result();

                                            // Populate table rows with fetched data
                                            while ($row = $res->fetch_assoc()) {
                                                echo '
                                                <tr>
                                                    <td>' . htmlspecialchars($row['case_name']) . '</td>
                                                    <td>' . htmlspecialchars($row['title']) . '</td>
                                                    <td>' . htmlspecialchars($row['updater']) . '</td>
                                                    <td>' . htmlspecialchars(date('D d M Y \a\t h.iA', strtotime($row['createdat']))) . '</td>
                                                    <td><a href="#" class="btn btn-primary btn-sm">View Update</a ></td>
                                                </tr>';
                                            }
                                            ?>
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>

                        <!-- Tab 3 -->
                        <div class="tab-pane fade" id="tab3" role="tabpanel" aria-labelledby="tab3-tab">
                            <div class="card mb-4">
                                <div class="card-header">
                                    <i class="fas fa-table me-1"></i>
                                    Displaying Updates for Closed Cases
                                </div>
                                <div class="card-body">
                                    <table id="datatablesThird" class="table table-bordered table-striped">
                                        <thead>
                                            <tr>
                                                <th>Case</th>
                                                <th>Update</th>
                                                <th>Added By</th>
                                                <th>Added On</th>
                                                <th>View</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <?php
                                            // SQL query using prepared statements
                                            $stmt = $conn->prepare("
                                                    SELECT
                                                        c3.updateid, 
                                                        concat(c1.courtname,' ',c2.casenumber,' ',c2.casename) as case_name,
                                                        c3.title,
                                                        concat(u.fname,' ',u.lname) as updater,
                                                        c3.createdat
                                                    FROM case_updates c3 
                                                    JOIN cases c2 ON c2.caseid = c3.caseid
                                                    JOIN courts c1 ON c2.courtid = c1.courtid
                                                    JOIN users u ON u.userid = c3.userid
                                                    WHERE c2.casestatus = 'closed' AND c2.clientid = ?
                                                ");
                                            $stmt->bind_param("i", $clientid);
                                            $stmt->execute();
                                            $res = $stmt->get_result();

                                            // Populate table rows with fetched data
                                            while ($row = $res->fetch_assoc()) {
                                                echo '
                                                <tr>
                                                    <td>' . htmlspecialchars($row['case_name']) . '</td>
                                                    <td>' . htmlspecialchars($row['title']) . '</td>
                                                    <td>' . htmlspecialchars($row['updater']) . '</td>
                                                    <td>' . htmlspecialchars(date('D d M Y \a\t h.iA', strtotime($row['createdat']))) . '</td>
                                                    <td><a href="#" class="btn btn-primary btn-sm">View Update</a ></td>
                                                </tr>';
                                            }
                                            ?>
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <script>
                    $(document).ready(function() {
                        // Initialize DataTable for 1st Table
                        $('#datatablesSimple').DataTable();

                        // Initialize DataTable for 2nd Table
                        $('#datatablesCompleted').DataTable();

                        // Initialize DataTable for 3rd Table
                        $('#datatablesThird').DataTable();
                    });
                </script>
            </div>
        </main>
        <?php include 'php/footer.php'; ?>