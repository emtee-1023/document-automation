<?php include 'php/header.php';?>

<div id="layoutSidenav">
            <?php include 'php/sidebar.php';?>
            <div id="layoutSidenav_content">
                <main>
                    <div class="container-fluid px-4">
                        <h1 class="mt-4">Clients</h1>
                        <ol class="breadcrumb mb-2">
                            <li class="breadcrumb-item"><a href="index">Home</a></li>
                            <li class="breadcrumb-item active">Clients</li>
                        </ol>
                        <ol class="breadcrumb mb-4">
                            <li class="breadcrumb-item active">Showing all Registered clients</li>
                        </ol>
                        <div class="row justify-content-end">
                            <div class="col-xl-3 col-md-6">
                                <div class="mt-4 mb-5 float-end">
                                    <a href="add-client" class="btn btn-secondary"><i class="fa-solid fa-users"></i> Add New Client</a>
                                </div>
                            </div>
                        </div>
                    <?php include 'php/clients-table.php';?>
                    </div>
                </main>
                <?php include 'php/footer.php';?>
