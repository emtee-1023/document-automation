<?php include 'php/header.php'; ?>

<?php include 'notifications.php'; ?>

<div id="layoutSidenav">
    <?php include 'php/sidebar.php'; ?>
    <div id="layoutSidenav_content">
        <main>
            <div class="container-fluid px-4">
                <h1 class="mt-4">
                    <?php echo "Hello, " . $_SESSION['fname']; ?>
                </h1>
                <?php
                $firm = $_SESSION['fid'];
                $res = mysqli_query($conn, "select firmname from firms where firmid=$firm");
                $row = mysqli_fetch_assoc($res);
                $firmname = $row['firmname'];
                ?>

                <ol class="breadcrumb mb-4">
                    <li class="breadcrumb-item active">
                        Welcome to the Client Portal under <strong><?php echo $firmname ?></strong>
                    </li>
                </ol>

                <div class="row">
                    <div class="col-xl-3 col-md-6">
                        <div class="card bg-secondary text-white mb-4">
                            <div class="card-body">Your Active Cases:
                                <?php
                                $client = $_SESSION['clientid'];
                                $res = mysqli_query($conn, "select * from cases where ClientID=$client and casestatus='open'");
                                $count = mysqli_num_rows($res);
                                echo $count;
                                ?>
                            </div>
                            <div class="card-footer d-flex align-items-center justify-content-between">
                                <a class="small text-white stretched-link" href="cases?status=1">View Active Cases</a>
                                <div class="small text-white"><i class="fas fa-angle-right"></i></div>
                            </div>
                        </div>
                    </div>
                    <div class="col-xl-3 col-md-6">
                        <div class="card bg-secondary text-white mb-4">
                            <div class="card-body">Pending Invoices:
                                <?php
                                $client = $_SESSION['clientid'];
                                $res = mysqli_query($conn, "select * from invoices where clientid=$client and status = 'pending'");
                                $count = mysqli_num_rows($res);
                                echo $count;
                                ?></div>
                            <div class="card-footer d-flex align-items-center justify-content-between">
                                <a class="small text-white stretched-link" href="invoices">See Pending Invoices</a>
                                <div class="small text-white"><i class="fas fa-angle-right"></i></div>
                            </div>
                        </div>
                    </div>
                    <div class="col-xl-3 col-md-6">
                        <div class="card bg-secondary text-white mb-4">
                            <div class="card-body">Active Reminders:
                                <?php
                                $client = $_SESSION['clientid'];
                                $res = mysqli_query($conn, "select * from reminders where clientid=$client and status = 'active'");
                                $count = mysqli_num_rows($res);
                                echo $count;
                                ?></div>
                            <div class="card-footer d-flex align-items-center justify-content-between">
                                <a class="small text-white stretched-link" href="reminders">See active reminders</a>
                                <div class="small text-white"><i class="fas fa-angle-right"></i></div>
                            </div>
                        </div>
                    </div>

                </div>
            </div>
        </main>
        <?php include 'php/footer.php'; ?>