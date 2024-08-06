<?php include 'php/header.php';?>

<div id="layoutSidenav">
            <?php include 'php/sidebar.php';?>
            <div id="layoutSidenav_content">
                <main>
                    <div class="container-fluid px-4">
                        <h1 class="mt-4">Courts</h1>
                        <ol class="breadcrumb mb-4">
                            <li class="breadcrumb-item"><a href="index">Home</a></li>
                            <li class="breadcrumb-item active">Courts</li>
                        </ol>
                        <ol class="breadcrumb mb-2">
                            <li class="breadcrumb-item active">Showing all registered courts</li>
                        </ol>
                        <p class="text-secondary fs-6"><strong>Note: </strong>You are required to register a court before creating a case</p>
                        <div class="row justify-content-end">
                            <div class="col-xl-3 col-md-6">
                                <div class="mt-2 mb-5 float-end">
                                    <a href="add-court" class="btn btn-secondary"><i class="fa-solid fa-gavel"></i> Add New Court</a>
                                </div>
                            </div>
                        </div>
                    <?php include 'php/courts-table.php';?>
                    </div>
                </main>
                <?php include 'php/footer.php';?>
