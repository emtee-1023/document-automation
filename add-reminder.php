<?php include 'php/header.php'; ?>

<?php
if (!isset($_SESSION['userid']) && !isset($_SESSION['fid'])) {
    header('location: firm-login');
} elseif (!isset($_SESSION['userid']) && isset($_SESSION['fid'])) {
    header('location: login');
}

$error_msg = '';
$success_msg = '';
$redirect = '';

$user = $_SESSION['userid'];
$firm = $_SESSION['fid'];

// Check if the form was submitted
if (isset($_POST['submit'])) {
    $caseid = $_POST['Case'];
    $clientid = $_POST['Client'];
    $nextdate = $_POST['nextDate'];
    $bringup = $_POST['bringupDate'];
    $link = $_POST['Link'];
    $notes = $_POST['Notes'];

    // Fetch message from the database
    $query = "
            SELECT
                cases.casenumber,
                cases.casename, 
                courts.courtname,
                CONCAT(c.prefix,' ',c.fname,' ',c.mname,' ',c.lname) as clientname,
                c.email,
                CONCAT(u.fname, ' ', u.lname) as advName,
                u.Email as advMail
            FROM cases 
            JOIN courts ON cases.courtid = courts.courtid
            JOIN clients c ON c.clientid = cases.clientid
            JOIN users u ON u.userid = cases.userid
            WHERE caseid = ?";
    $stmt = mysqli_prepare($conn, $query);
    mysqli_stmt_bind_param($stmt, "i", $caseid);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);
    $row = mysqli_fetch_assoc($result);
    $casename = $row['casename'];
    $casenumber = $row['casenumber'];
    $courtname = $row['courtname'];
    $clientname = $row['clientname'];
    $advName = $row['advName'];
    $clientRecepient = $row['email'];
    $advRecepient = $row['advMail'];

    $message1 = "Case: " . $casename;

    $message2 = "Case: " . $casename;

    // Prepare and execute the insertion of assignments
    $stmt_reminder = mysqli_prepare($conn, "INSERT INTO reminders (CaseID, clientid, nextdate, bringupdate, meetinglink, notes, userid, firmid) VALUES (?, ?, ?, ?, ?, ?, ?, ?)");
    $stmt_notification = mysqli_prepare($conn, "INSERT INTO notifications (NotifSubject, NotifText, UserID, ClientID) VALUES (?, ?, ?, ?)");
    $stmt_notification2 = mysqli_prepare($conn, "INSERT INTO notifications (NotifSubject, NotifText, UserID, ClientID, SendAt) VALUES (?, ?, ?, ?, ?)");

    //setup the email
    $next_date_readable = date('D d M Y \a\t h.iA', strtotime($nextdate));
    $bringup_date_readable = date('D d M Y \a\t h.iA', strtotime($bringup));
    //email 1: let the client know a reminder has been set
    $subject = "New Date Scheduled for " . $courtname . " " . $casenumber . " " . $casename;
    $message = mailClientAddedRem($clientname, $courtname, $casenumber, $casename, $next_date_readable, $notes, $link);
    noReplyMail($clientRecepient, $subject, $message);

    //email 2: let the client know the bringup date has reached
    $subject = "Bring-Up Date for " . $courtname . " " . $casenumber . " " . $casename . " is Today";
    $message = mailClientBringup($clientname, $courtname, $casenumber, $casename, $next_date_readable, $notes, $link);
    scheduledMail($clientRecepient, $subject, $message, $bringup);

    //email 3: let the advocate know the bringup date has reached
    $subject = "Bring-Up Date for " . $courtname . " " . $casenumber . " " . $casename . " is Today";
    $message = mailAdvBringup($advName, $courtname, $casenumber, $casename, $next_date_readable, $notes, $link);
    scheduledMail($advRecepient, $subject, $message, $bringup);

    //email 3: let the client know the actual date is today
    $subject = "Reminder: Matter coming up today " . $courtname . " " . $casenumber . " " . $casename;
    $message = mailClientRem($clientname, $courtname, $casenumber, $casename, $next_date_readable, $notes, $link);
    scheduledMail($clientRecepient, $subject, $message, $nextdate);

    //email 3: let the advocate know the actual date is today
    $subject = "Reminder: Matter coming up today " . $courtname . " " . $casenumber . " " . $casename;
    $message = mailAdvRem($advName, $courtname, $casenumber, $casename, $next_date_readable, $notes, $link);
    scheduledMail($advRecepient, $subject, $message, $nextdate);


    if ($stmt_reminder && $stmt_notification && $stmt_notification2) {

        // Insert into reminders
        mysqli_stmt_bind_param($stmt_reminder, "iissssii", $caseid, $clientid, $nextdate, $bringup, $link, $notes, $user, $firm);
        mysqli_stmt_execute($stmt_reminder);

        // Insert into notifications
        $notifSubject = "New Case Reminder Set";
        $notifText = $message1;
        mysqli_stmt_bind_param($stmt_notification, "ssii", $notifSubject, $notifText, $user, $clientid);
        mysqli_stmt_execute($stmt_notification);

        // Insert into notifications2
        $notifSubject2 = "Case reminder Bringup";
        $notifText2 = $message2;
        mysqli_stmt_bind_param($stmt_notification2, "ssiis", $notifSubject2, $notifText2, $user, $clientid, $bringup);
        mysqli_stmt_execute($stmt_notification2);


        // Close the statements
        mysqli_stmt_close($stmt_reminder);
        mysqli_stmt_close($stmt_notification);
        mysqli_stmt_close($stmt_notification2);

        $success_msg = "Reminder Added Successfuly";
    } else {
        // Handle the error if the prepared statements failed
        $error_msg = "Error preparing statements: " . mysqli_error($conn);
    }
}
?>

<div id="layoutSidenav">
    <?php include 'php/sidebar.php'; ?>
    <div id="layoutSidenav_content">
        <main">
            <div class="container-fluid px-4 d-flex flex-column align-items-start">


                <div class="card shadow-sm border-0 rounded-lg mt-3 md-6 col-md-10 align-self-center d-flex flex-column">
                    <?php
                    if ($error_msg != '') {
                        echo '
                        <div class="alert alert-danger" role="alert">
                            ' . $error_msg . '
                        </div>';
                    }
                    ?>

                    <?php
                    if ($success_msg != '') {
                        echo '
                        <div class="alert alert-success" role="alert">
                            ' . $success_msg . '
                        </div>';
                    }
                    ?>
                    <div class="card-header">
                        <h3 class="text-center font-weight-light my-4">Create Reminder</h3>
                    </div>
                    <div class="card-body">
                        <form method="post" action="" enctype="multipart/form-data">
                            <!--choose case and client on the same row -->
                            <div class="row mb-3">
                                <div class="col-md-6">
                                    <div class="form-floating mb-3">
                                        <select class="form-select" id="chooseCase" name="Case" aria-label="chooseCase">
                                            <option value="" disabled selected>Choose Case</option>
                                            <?php
                                            $user = $_SESSION['userid'];
                                            $firm = $_SESSION['fid'];
                                            // Prepare SQL query with parameterized statement
                                            $stmt = $conn->prepare("SELECT caseid, casename FROM cases WHERE firmid = ? AND casestatus='open'");
                                            $stmt->bind_param("i", $firm); // "i" specifies the variable type as integer
                                            $stmt->execute();
                                            $result = $stmt->get_result();

                                            // Fetch and display options
                                            while ($row = $result->fetch_assoc()) {
                                                echo '<option value="' . htmlspecialchars($row["caseid"]) . '">' . htmlspecialchars($row["casename"]) . '</option>';
                                            }

                                            $stmt->close();
                                            ?>
                                        </select>
                                        <label for="chooseCase">Choose Case</label>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-floating mb-3">
                                        <select class="form-select" id="chooseClient" name="Client" aria-label="chooseClient">
                                            <option value="" disabled selected>Choose Client</option>
                                            <?php
                                            $user = $_SESSION['userid'];
                                            $firm = $_SESSION['fid'];
                                            // Prepare SQL query with parameterized statement
                                            $stmt = $conn->prepare("SELECT clientid, concat(fname,' ',mname,' ',lname) as clientname FROM clients WHERE firmid = ?");
                                            $stmt->bind_param("i", $firm); // "i" specifies the variable type as integer
                                            $stmt->execute();
                                            $result = $stmt->get_result();

                                            // Fetch and display options
                                            while ($row = $result->fetch_assoc()) {
                                                echo '<option value="' . htmlspecialchars($row["clientid"]) . '">' . htmlspecialchars($row["clientname"]) . '</option>';
                                            }

                                            $stmt->close();
                                            ?>
                                        </select>
                                        <label for="chooseClient">Choose Client</label>
                                    </div>
                                </div>
                            </div>

                            <!-- Next Hearing and Bringup Date-->
                            <div class="row ">
                                <div class="col-md-6">
                                    <div class="form-floating mb-3">
                                        <input class="form-control" id="nextDate" type="datetime-local" name="nextDate" required />
                                        <label for="nextDate">Next Date</label>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-floating mb-3">
                                        <input class="form-control" id="bringupDate" type="datetime-local" name="bringupDate" />
                                        <label for="deadline">Bringup Date</label>
                                    </div>
                                </div>
                            </div>

                            <!-- Link-->
                            <div class="row ">
                                <div class="col-md-12">
                                    <div class="form-floating mb-3">
                                        <input class="form-control" id="Link" type="text" name="Link" />
                                        <label for="Link">Meeting Link (online)</label>
                                    </div>
                                </div>
                            </div>

                            <!-- Link-->
                            <div class="row ">
                                <div class="col-md-12">
                                    <div class="form-floating mb-3">
                                        <textarea class="form-control" name="Notes" id="Notes" rows="10"></textarea>
                                        <label for="Notes">Notes</label>
                                    </div>
                                </div>
                            </div>
                            <div class="mt-3 mb-0 d-flex justify-content-center">
                                <div class="d-grid">
                                    <input type="submit" class="btn btn-primary" name="submit" value="Submit">
                                </div>
                            </div>
                        </form>

                        <div class="d-grid">
                            <a href="reminders">Back to reminders</a>
                        </div>
                    </div>
                    </main>
                    <?php include 'php/footer.php'; ?>