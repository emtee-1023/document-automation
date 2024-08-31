<?php include 'php/header.php';?>

<?php include 'notifications.php';?>


<div id="layoutSidenav">
            <?php include 'php/sidebar.php';?>
            <div id="layoutSidenav_content">
                <main>
                    <div class="container-fluid px-4">
                        <h1 class="mt-4">Tasks</h1>
                        <ol class="breadcrumb mb-4">
                            <li class="breadcrumb-item"><a href="index">Home</a></li>
                            <li class="breadcrumb-item active">Tasks</li>
                        </ol>
                        <ol class="breadcrumb mb-2">
                            <li class="breadcrumb-item active">Task Manager</li>
                        </ol>
                        <div class="row justify-content-end">
                            <div class="col-xl-3 col-md-6">
                                <div class="mt-2 mb-5 float-end">
                                    <a href="add-task" class="btn btn-secondary"><i class="fa-solid fa-list-check"></i> Create New Task</a>
                                </div>
                            </div>
                        </div>
                    <?php include 'php/tasks-table.php';?>
                    </div>
                </main>
                <?php include 'php/footer.php';?>
