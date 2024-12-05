<?php include 'php/header.php'; ?>

<?php
// Redirect based on session state
if (!isset($_SESSION['userid']) && !isset($_SESSION['fid'])) {
    header('Location: firm-login.php');
    exit();
} elseif (!isset($_SESSION['userid']) && isset($_SESSION['fid'])) {
    header('Location: login.php');
    exit();
}

date_default_timezone_set('Africa/Nairobi');
$currentTimestamp = date('Y-m-d H:i:s');

$error_msg = '';
$success_msg = '';
$user = $_SESSION['userid'];
$firm = $_SESSION['fid'];

if (isset($_GET['taskid'])) {
    $task = $_GET['taskid'];

    // Fetch users from the database
    $query = "
            SELECT
                CONCAT('You Have Been Assigned The Task ',t.taskname,' by ',u.fname,' ',u.lname,' that is due on') as message, 
                CONCAT(u.fname,' ',u.lname) as tasker,
                t.taskdeadline,
                t.taskname,
                t.taskdescription
            FROM tasks t 
            JOIN users u ON u.userid = t.userid
            WHERE t.taskid = ?";
    $stmt = mysqli_prepare($conn, $query);
    mysqli_stmt_bind_param($stmt, "i", $task);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);
    $row = mysqli_fetch_assoc($result);

    $task_deadline_readable = date('D d M Y \a\t h.iA', strtotime($row['taskdeadline']));
    $message = $row['message'] . " " . $task_deadline_readable;
    $tasker = $row['tasker'];
    $task_title = $row['taskname'];
    $task_description = $row['taskdescription'];
}

if (isset($_POST['submit_assignment'])) {
    $taskId = $_POST['task_id'];
    $assignedUsers = $_POST['assigned_users']; // This will be an array of user IDs

    // Check if any users were selected
    if (!empty($assignedUsers)) {
        // Prepare and execute the insertion of assignments
        $stmt_assignment = mysqli_prepare($conn, "INSERT INTO task_assignments (taskid, userid, AssignedAt) VALUES (?, ?, ?)");
        $stmt_notification = mysqli_prepare($conn, "INSERT INTO notifications (NotifSubject, NotifText, UserID) VALUES (?, ?, ?)");

        if ($stmt_assignment && $stmt_notification) {
            foreach ($assignedUsers as $userId) {
                //fetch assignee name from the db
                $query2 = "
                        SELECT 
                            CONCAT(fname, ' ', lname) AS assigneeName, 
                            email 
                        FROM users 
                        WHERE userid = ?
                ";
                $stmt2 = mysqli_prepare($conn, $query2);
                mysqli_stmt_bind_param($stmt2, "i", $userId);
                mysqli_stmt_execute($stmt2);
                $result2 = mysqli_stmt_get_result($stmt2);
                $row2 = mysqli_fetch_assoc($result2);

                $assignee_name = $row2['assigneeName'];
                $assignee_email = $row2['email'];

                // Insert into task_assignments
                mysqli_stmt_bind_param($stmt_assignment, "iii", $taskId, $userId, $currentTimestamp);
                mysqli_stmt_execute($stmt_assignment);

                // Insert into notifications
                $notifSubject = "New Task Assignment from " . $tasker;
                $notifText = $message;
                mysqli_stmt_bind_param($stmt_notification, "ssi", $notifSubject, $notifText, $userId);
                mysqli_stmt_execute($stmt_notification);

                $success_msg = "Task Assigned successfuly";

                //email the assignee that they have been tasked
                $email_subject = "New Task Assigned: " . $task_title;
                $email_message = mailAssignedTask($assignee_name, $task_title, $tasker, $task_deadline_readable, $task_description);
                noReplyMail($assignee_email, $email_subject, $email_message);
            }

            // Close the statements
            mysqli_stmt_close($stmt_assignment);
            mysqli_stmt_close($stmt_notification);
        } else {
            // Handle the error if the prepared statements failed
            $error_msg = "Error preparing statements: " . mysqli_error($conn);
        }
    } else {
        $error_msg = 'No users selected.';
    }
}
?>

<div id="layoutSidenav">
    <?php include 'php/sidebar.php'; ?>
    <div id="layoutSidenav_content">
        <main>
            <div class="container-fluid px-4 d-flex flex-column align-items-start">
                <div class="row justify-content-end">
                </div>

                <div class="card shadow-sm border-0 rounded-lg mt-3 md-6 col-md-10 align-self-center d-flex flex-column">
                    <?php if ($error_msg != ''): ?>
                        <div class="alert alert-danger" role="alert">
                            <?php echo htmlspecialchars($error_msg); ?>
                        </div>
                    <?php endif; ?>

                    <?php if ($success_msg != ''): ?>
                        <div class="alert alert-success" role="alert">
                            <?php echo htmlspecialchars($success_msg); ?>
                        </div>
                    <?php endif; ?>

                    <div class="card-header">
                        <h3 class="text-center font-weight-light my-4">Assign User</h3>
                    </div>
                    <div class="card-body">
                        <form method="post" action="">
                            <input type="hidden" name="task_id" value="<?php echo htmlspecialchars($_GET['taskid']); ?>" />
                            <!-- Choose User -->
                            <div class="mb-3">
                                <label for="assignedUsers" class="form-label">Choose Users To Assign</label>
                                <div>
                                    <?php
                                    // Fetch users from the database
                                    $query = "SELECT UserID, CONCAT(fname, ' ', lname) as username FROM users WHERE firmid = ?";
                                    $stmt = mysqli_prepare($conn, $query);
                                    mysqli_stmt_bind_param($stmt, "i", $firm);
                                    mysqli_stmt_execute($stmt);
                                    $result = mysqli_stmt_get_result($stmt);

                                    while ($row = mysqli_fetch_assoc($result)): ?>
                                        <div class="form-check">
                                            <input class="form-check-input" type="checkbox" id="user_<?php echo $row['UserID']; ?>" name="assigned_users[]" value="<?php echo htmlspecialchars($row['UserID']); ?>">
                                            <label class="form-check-label" for="user_<?php echo $row['UserID']; ?>">
                                                <?php echo htmlspecialchars($row['username']); ?>
                                            </label>
                                        </div>
                                    <?php endwhile; ?>
                                </div>
                            </div>

                            <div class="mt-3 mb-0 d-flex justify-content-center">
                                <div class="d-grid">
                                    <input type="submit" class="btn btn-primary" name="submit_assignment" value="Assign Task">
                                </div>
                            </div>
                        </form>

                        <div class="mt-3 mb-0 d-flex justify-content-center">
                            <a href="tasks">go to task manager</a>
                        </div>
                    </div>
                </div>
            </div>
        </main>
        <?php include 'php/footer.php'; ?>
    </div>
</div>