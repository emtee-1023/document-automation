<?php include 'php/header.php';?>

<?php
if(!isset($_SESSION['userid']) && !isset($_SESSION['fid'])){
    header('location: firm-login');
} elseif(!isset($_SESSION['userid']) && isset($_SESSION['fid'])){
    header('location: login');
}

$error_msg = '';
$success_msg = '';
$redirect = '';

$user = $_SESSION['userid'];
$firm = $_SESSION['fid'];

// Check if the form was submitted
if (isset($_POST['submit_task'])) {
    $taskName = $_POST['task_name'];
    $description = $_POST['description'];
    $deadline = $_POST['deadline'];
    $user = $_SESSION['userid'];
    $firm = $_SESSION['fid'];

    // Prepare and execute the insert statement
    $stmt = mysqli_prepare($conn, "INSERT INTO tasks (TaskName, TaskDescription, TaskDeadline, CreatedAt, UserID, FirmID) VALUES (?, ?, ?, NOW(), ?, ?)");
    if ($stmt) {
        mysqli_stmt_bind_param($stmt, "sssii", $taskName, $description, $deadline, $user, $firm);
        mysqli_stmt_execute($stmt);
        
        // Get the newly created task ID
        $taskId = mysqli_insert_id($conn);

        // Close the statement
        mysqli_stmt_close($stmt);

        // Redirect to the task assignment page with the task ID
        header("Location: assign-task?taskid=$taskId");
        exit();
    } else {
        // Error preparing the statement
        $error_msg = 'Error preparing the SQL statement.';
    }
}
?>

<div id="layoutSidenav">
    <?php include 'php/sidebar.php';?>
    <div id="layoutSidenav_content">
        <main">
            <div class="container-fluid px-4 d-flex flex-column align-items-start">
                <h1 class="mt-4">Create Task</h1>
                <ol class="breadcrumb mb-4">
                    <li class="breadcrumb-item"><a href="tasks">Tasks</a></li>
                    <li class="breadcrumb-item active">NewTask</li>
                </ol>
                <div class="row justify-content-end">
                </div>

                <div class="card shadow-sm border-0 rounded-lg mt-3 md-6 col-md-10 align-self-center d-flex flex-column">
                    <?php 
                    if ($error_msg != '') {
                        echo '
                        <div class="alert alert-danger" role="alert">
                            '.$error_msg.'
                        </div>';
                    }
                    ?>

                    <?php 
                    if ($success_msg != '') {
                        echo '
                        <div class="alert alert-success" role="alert">
                            '.$success_msg.'
                        </div>';
                    }
                    ?>
                    <div class="card-header"><h3 class="text-center font-weight-light my-4">Create Task</h3></div>
                    <div class="card-body">
                        <form method="post" action="" enctype="multipart/form-data">
                            <!--Task nameand deadline on the same row -->
                            <div class="row mb-3">
                                <div class="col-md-6">
                                    <div class="form-floating mb-3">
                                        <input class="form-control" id="taskName" type="text" placeholder="Task Name" name="task_name" required/>
                                        <label for="taskName">Task Name</label>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-floating mb-3">
                                        <input class="form-control" id="deadline" type="datetime-local" name="deadline" required/>
                                        <label for="deadline">Deadline</label>
                                    </div>
                                </div>
                            </div>

                            <!-- Deadline-->
                            <div class="row ">
                                <div class="col-md-12">
                                    <div class="form-floating mb-3"> 
                                        <textarea class="form-control" id="description" name="description" rows="3" placeholder="Description"></textarea>   
                                        <label for="description">Description</label>
                                    </div>
                                </div>
                            </div>
                            

                            <div class="mt-3 mb-0 d-flex justify-content-center">
                                <div class="d-grid">
                                    <input type="submit" class="btn btn-primary" name="submit_task" value="Choose User">
                                </div>
                            </div>
                        </form>
            </div>
        </main>
        <?php include 'php/footer.php';?>
