<?php include 'php/header.php';?>

<?php include 'notifications.php';?>


<div id="layoutSidenav">
            <?php include 'php/sidebar.php';?>
            <div id="layoutSidenav_content">
                <main>
                    <div class="container-fluid px-4">
                        <h1 class="mt-4">Reminders</h1>
                        <ol class="breadcrumb mb-4">
                            <li class="breadcrumb-item"><a href="index">Home</a></li>
                            <li class="breadcrumb-item active">Reminders</li>
                        </ol>
                        <div class="row justify-content-end">
                            <div class="col-xl-3 col-md-6">
                                <div class="mt-2 mb-5 float-end">
                                    <a href="add-reminder" class="btn btn-secondary"><i class="fa-regular fa-calendar"></i> Add New Reminder</a>
                                </div>
                            </div>
                        </div>
                    <?php include 'php/reminders-table.php';?>
                    </div>
                </main>
                <?php include 'php/footer.php';?>
