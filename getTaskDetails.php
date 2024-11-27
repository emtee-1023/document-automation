<?php
include 'php/dbconn.php';

if (isset($_GET['taskid'])) {
    $taskId = intval($_GET['taskid']);
    $query = "SELECT * FROM tasks WHERE task_id = ?";

    $stmt = mysqli_prepare($conn, $query);
    mysqli_stmt_bind_param($stmt, "i", $taskId);
    mysqli_stmt_execute($stmt);

    $result = mysqli_stmt_get_result($stmt);
    $task = mysqli_fetch_assoc($result);

    // Return task data as JSON
    echo json_encode($task);
}
