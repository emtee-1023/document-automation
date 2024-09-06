<?php
include 'php/dbconn.php';
session_start();
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
        header("Location: assign-task?taskid=".$taskId);
        exit();
    } else {
        // Error preparing the statement
        $error_msg = 'Error preparing the SQL statement.';
    }
}

?>