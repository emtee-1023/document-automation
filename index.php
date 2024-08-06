<?php include 'php/header.php';?>

<div id="layoutSidenav">
            <?php include 'php/sidebar.php';?>
            <div id="layoutSidenav_content">
                <main>
                    <div class="container-fluid px-4">
                        <h1 class="mt-4">Welcome Back, <?php echo $_SESSION['username'];?></h1>
                        <ol class="breadcrumb mb-4">
                            <li class="breadcrumb-item active">Let's increase your productivity today</li>
                        </ol>
                        <div class="row">
                            <div class="col-xl-3 col-md-6">
                                <div class="card bg-secondary text-white mb-4">
                                    <div class="card-body">Total Courts:
                                        <?php
                                        $owner = $_SESSION['userid'];
                                        $res = mysqli_query($conn,"select * from courts where added_by=$owner");
                                        $count = mysqli_num_rows($res);
                                        echo $count;
                                        ?>
                                    </div>
                                    <div class="card-footer d-flex align-items-center justify-content-between">
                                        <a class="small text-white stretched-link" href="courts">Add New Court</a>
                                        <div class="small text-white"><i class="fas fa-angle-right"></i></div>
                                    </div>
                                </div>
                            </div>
                            <div class="col-xl-3 col-md-6">
                                <div class="card bg-secondary text-white mb-4">
                                    <div class="card-body">Total Registered Clients: 
                                        <?php
                                        $owner = $_SESSION['userid'];
                                        $res = mysqli_query($conn,"select * from clients where Belong_to=$owner");
                                        $count = mysqli_num_rows($res);
                                        echo $count;
                                        ?></div>
                                    <div class="card-footer d-flex align-items-center justify-content-between">
                                        <a class="small text-white stretched-link" href="clients">View All Clients</a>
                                        <div class="small text-white"><i class="fas fa-angle-right"></i></div>
                                    </div>
                                </div>
                            </div>
                            <div class="col-xl-3 col-md-6">
                                <div class="card bg-secondary text-white mb-4">
                                    <div class="card-body">Total Active Cases: 
                                        <?php
                                        $owner = $_SESSION['userid'];
                                        $res = mysqli_query($conn,"select * from cases where UserID=$owner and casestatus='open'");
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
                                    <div class="card-body">Total Closed Cases: 
                                        <?php
                                        $owner = $_SESSION['userid'];
                                        $res = mysqli_query($conn,"select * from cases where UserID=$owner and casestatus='closed'");
                                        $count = mysqli_num_rows($res);
                                        echo $count;
                                        ?></div>
                                    <div class="card-footer d-flex align-items-center justify-content-between">
                                        <a class="small text-white stretched-link" href="cases?status=3">View Closed Cases</a>
                                        <div class="small text-white"><i class="fas fa-angle-right"></i></div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <?php include 'php/courts-table.php';?>
                    </div>
                </main>
                <?php include 'php/footer.php';?>
