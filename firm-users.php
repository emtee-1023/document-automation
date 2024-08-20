<?php include 'php/header.php';?>

<?php
if ($_SESSION['user_type'] != 'admin' && $_SESSION['user_type'] != 'super admin') {
    header('location: 401');
    exit();
}

?>

<div id="layoutSidenav">
            <?php include 'php/sidebar.php';?>
            <div id="layoutSidenav_content">
                <main>
                    <div class="container-fluid px-4">
                        <h1 class="mt-4">Users</h1>
                        <ol class="breadcrumb mb-2">
                            <li class="breadcrumb-item"><a href="index">Home</a></li>
                            <li class="breadcrumb-item active">Users</li>
                        </ol>
                        <ol class="breadcrumb mb-4">
                            <li class="breadcrumb-item active">Showing all users registered under <strong><?php echo $_SESSION['firmname']?></strong></li>
                        </ol>
                        <div class="row justify-content-end">
                            <div class="col-xl-3 col-md-6">
                                <div class="mt-4 mb-5 float-end">
                                    <a href="add-user" class="btn btn-secondary"><i class="fa-solid fa-users"></i> Add New User</a>
                                </div>
                            </div>
                        </div>
                    <?php include 'php/users-table.php';?>
                    </div>
                </main>
                <?php include 'php/footer.php';?>
