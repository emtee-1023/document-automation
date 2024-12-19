<?php
session_start();
include 'php/dbconn.php';
include 'php/mail.php';

date_default_timezone_set('Africa/Nairobi');
$currentTimestamp = date('Y-m-d H:i:s');
$_10Expiry = date('Y-m-d H:i:s', strtotime('+10 minutes'));

$user = $_SESSION['userid'] ?? '';
$firm = $_SESSION['fid'] ?? '';

if (isset($_POST['recover-pass'])) {
    $email = $_POST['email'];

    $stmt1 = $conn->prepare('SELECT Email, FName from users WHERE Email = ?');
    $stmt1->bind_param('s', $email);
    if (!$stmt1->execute()) {
        $_SESSION['error_msg'] = "Error Executing Statement";
        header('location: password');
        exit();
    }
    $res1 = $stmt1->get_result();

    if ($res1->num_rows > 0) {
        $usr = $res1->fetch_assoc();
        $fname = $usr['FName'];
        $email = $usr['Email'];
        $token = bin2hex(random_bytes(32));

        $stmt3 = $conn->prepare('UPDATE users SET PReset = ?, PResetTime = ? where Email = ?');
        $stmt3->bind_param('sss', $token, $_10Expiry, $email);
        if (!$stmt3->execute()) {
            $_SESSION['error_msg'] = "Problem Encountered Recovering Your Account. Contact support if issue persists";
            header('location: password');
            exit();
        }
        $subject = "PASSWORD RESET";
        $message = passReset($fname, $token);
        if (!noReplyMail($email, $subject, $message)) {
            $_SESSION['error_msg'] = "Error Encountered When Sending Email";
            header('location: password');
            exit();
        }
        $_SESSION['success_msg'] = "A Password Reset Link Has Been Sent To Your Email  Address";
        header('location: password');
        exit();
    } else if ($res1->num_rows == 0) {
        $stmt2 = $conn->prepare('SELECT FirmMail, FirmName FROM firms WHERE FirmMail = ?');
        $stmt2->bind_param('s', $email);
        if (!$stmt2->execute()) {
            $_SESSION['error_msg'] = "Error Executing Statement";
            header('location: password');
            exit();
        }
        $res2 = $stmt2->get_result();

        if ($res2->num_rows > 0) {
            $frm = $res2->fetch_assoc();
            $fname = $frm['FirmName'];
            $email = $frm['FirmMail'];
            $token = bin2hex(random_bytes(32));

            $stmt4 = $conn->prepare('UPDATE firms SET PReset = ?, PResetTime = ? where FirmMail = ?');
            $stmt4->bind_param('sss', $token, $_10Expiry, $email);
            if (!$stmt4->execute()) {
                $_SESSION['error_msg'] = "Problem Encountered Recovering Your Account. Contact support if issue persists";
                header('location: password');
                exit();
            }
            $subject = "PASSWORD RESET";
            $message = passReset($fname, $token);
            if (!noReplyMail($email, $subject, $message)) {
                $_SESSION['error_msg'] = "Error Encountered When Sending Email";
                header('location: password');
                exit();
            }
            $_SESSION['success_msg'] = "A Password Reset Link Has Been Sent To Your Email  Address";
            header('location: password');
            exit();
        } else {
            $_SESSION['error_msg'] = "The Email Address Does Not Exist";
            header('location: password');
            exit();
        }
    } else {
        $_SESSION['error_msg'] = "The Email Address Does Not Exist";
        header('location: password');
        exit();
    }
} else if (isset($_POST['reset-pass'])) {
    $newPass = password_hash($_POST['password'], PASSWORD_DEFAULT);
    $token = $_POST['token'];

    $stmt1 = $conn->prepare('SELECT Email FROM users WHERE PReset = ? AND NOW() <= PResetTime');
    $stmt1->bind_param('s', $token);
    if (!$stmt1->execute()) {
        $_SESSION['error_msg'] = "Invalid Token.";
        header('location: password');
        exit();
    }
    $res1 = $stmt1->get_result();

    if ($res1->num_rows > 0) { //users
        $stmt3 = $conn->prepare('UPDATE users set Password = ? WHERE PReset = ?');
        $stmt3->bind_param('ss', $newPass, $token);
        if (!$stmt3->execute()) {
            $_SESSION['error_msg'] = "Unable to change Password.";
            header('location: password');
            exit();
        }
        $_SESSION['success_msg'] = "Password Reset Successfuly";
        header('location: login');
        exit();
    } else if ($res1->num_rows == 0) { //maybe firm
        $stmt5 = $conn->prepare('SELECT FirmMail FROM firms WHERE PReset = ? AND NOW() <= PResetTime');
        $stmt5->bind_param('s', $token);
        if (!$stmt5->execute()) {
            $_SESSION['error_msg'] = "Invalid Token.";
            header('location: password');
            exit();
        }
        $res5 = $stmt5->get_result();

        if ($res5->num_rows > 0) {
            $stmt4 = $conn->prepare('UPDATE firms set FirmPass = ? WHERE PReset = ?');
            $stmt4->bind_param('ss', $newPass, $token);
            if (!$stmt4->execute()) {
                $_SESSION['error_msg'] = "Unable to change Password.";
                header('location: password');
                exit();
            }
            $_SESSION['success_msg'] = "Password Reset Successfuly";
            header('location: firm-login');
            exit();
        } else { //not found
            $_SESSION['error_msg'] = "Problem Verifying Token.";
            header('location: password');
            exit();
        }
    } else { //not found
        $_SESSION['error_msg'] = "Problem Verifying Token.";
        header('location: password');
        exit();
    }
} else if (isset($_POST['submit_task'])) {
    $taskName = $_POST['task_name'];
    $description = $_POST['description'];
    $deadline = $_POST['deadline'];

    // Handle file upload
    if (isset($_FILES['Document']) && $_FILES['Document']['error'] === UPLOAD_ERR_OK) {
        $fileTmpPath = $_FILES['Document']['tmp_name'];
        $fileName = $_FILES['Document']['name'];
        $fileSize = $_FILES['Document']['size'];
        $fileType = $_FILES['Document']['type'];
        $fileNameCmps = explode(".", $fileName);
        $fileExtension = strtolower(end($fileNameCmps));
        $newFileName = time() . '.' . $fileExtension;
        $Extension = pathinfo($fileName, PATHINFO_EXTENSION);
        $uploadFileDir = 'assets/files/submitted/';
        $dest_path = $uploadFileDir . $newFileName;

        // Move the uploaded file
        if (move_uploaded_file($fileTmpPath, $dest_path)) {
            // Prepare and execute the insert statement
            $stmt = mysqli_prepare($conn, "INSERT INTO tasks (TaskName, TaskDescription, Document, TaskDeadline, CreatedAt, UserID, FirmID) VALUES (?, ?, ?, ?, ?, ?, ?)");
            if ($stmt) {
                mysqli_stmt_bind_param($stmt, "sssssii", $taskName, $description, $newFileName, $deadline, $currentTimestamp, $user, $firm);
                mysqli_stmt_execute($stmt);

                // Get the newly created task ID
                $taskid = mysqli_insert_id($conn);
                mysqli_stmt_close($stmt);

                header('location: assign-task?taskid=' . $taskid);
                exit();
            } else {
                // Error preparing the statement
                $error_msg = 'Error preparing the SQL statement.';
            }
        } else {
            $error_msg = 'Error moving the uploaded file.';
        }
    } else {
        // Prepare and execute the insert statement
        $stmt = mysqli_prepare($conn, "INSERT INTO tasks (TaskName, TaskDescription, TaskDeadline, CreatedAt, UserID, FirmID) VALUES (?, ?, ?, ?, ?, ?)");
        if ($stmt) {
            mysqli_stmt_bind_param($stmt, "ssssii", $taskName, $description, $deadline, $currentTimestamp, $user, $firm);
            mysqli_stmt_execute($stmt);

            // Get the newly created task ID
            $taskid = mysqli_insert_id($conn);
            mysqli_stmt_close($stmt);

            header('location: assign-task?taskid=' . $taskid);
            exit();
        } else {
            // Error preparing the statement
            $error_msg = 'Error preparing the SQL statement.';
        }
    }
} else if (isset($_POST['submit-case-update'])) {
    $caseid = $_POST['caseid'];
    $title = $_POST['title'];
    $details = $_POST['details'];

    // Handle file upload
    if (isset($_FILES['Document']) && $_FILES['Document']['error'] === UPLOAD_ERR_OK) {
        $fileTmpPath = $_FILES['Document']['tmp_name'];
        $fileName = $_FILES['Document']['name'];
        $fileSize = $_FILES['Document']['size'];
        $fileType = $_FILES['Document']['type'];
        $fileNameCmps = explode(".", $fileName);
        $fileExtension = strtolower(end($fileNameCmps));
        $newFileName = time() . '.' . $fileExtension;
        $Extension = pathinfo($fileName, PATHINFO_EXTENSION);
        $uploadFileDir = 'assets/files/submitted/';
        $dest_path = $uploadFileDir . $newFileName;

        // Move the uploaded file
        if (move_uploaded_file($fileTmpPath, $dest_path)) {
            // Prepare and execute the insert statement
            $stmt = mysqli_prepare($conn, "INSERT INTO case_updates (CaseID, Title, Details, Document, CreatedAt, UserID, FirmID) VALUES (?, ?, ?, ?, ?, ?, ?)");
            if ($stmt) {
                mysqli_stmt_bind_param($stmt, "sssssii", $caseid, $title, $details, $newFileName, $currentTimestamp, $user, $firm);
                mysqli_stmt_execute($stmt);

                // Get the newly created update ID
                $updtid = mysqli_insert_id($conn);
                mysqli_stmt_close($stmt);

                //create an email for the client
                $stmtc = $conn->prepare("
                                        SELECT 
                                            concat(cl.fname,' ',cl.lname) as clientName,
                                            cl.email,
                                            f.firmname,
                                            c1.courtname,
                                            c2.casenumber,
                                            c2.casename,
                                            c3.title,
                                            c3.details

                                            FROM case_updates c3
                                            JOIN cases c2 ON c3.caseid = c2.caseid
                                            JOIN firms f ON c3.firmid = f.firmid
                                            JOIN clients cl ON c2.clientid = cl.clientid
                                            JOIN courts c1 ON c2.courtid = c1.courtid
                                            WHERE c3.updateid = ?
                                            
                                            ");
                $stmtc->bind_param('s', $updtid);
                $stmtc->execute();
                $stmtc->bind_result($client, $recepient, $firmName, $courtName, $caseNum, $caseName, $title, $details);
                $stmtc->fetch();
                $stmtc->close();
                $subject = "InLaw Case Update";
                $message = mailCaseUpdate($client, $firmName, $courtName, $caseNum, $caseName, $title, $details);
                if (!noReplyMail($recepient, $subject, $message)) {
                    $_SESSION['error'] = "Problem encountered when mailing the client";
                }
                header('location: case-updates');
                exit();

                $_SESSION['success'] = "Case Update Added Successfuly";
                header('location: case-updates');
                exit();
            } else {
                // Error preparing the statement
                $error_msg = 'Error preparing the SQL statement.';
            }
        } else {
            $error_msg = 'Error moving the uploaded file.';
        }
    } else {
        // Prepare and execute the insert statement
        $stmt = mysqli_prepare($conn, "INSERT INTO case_updates (CaseID, Title, Details, CreatedAt, UserID, FirmID) VALUES (?, ?, ?, ?, ?, ?)");
        if ($stmt) {
            mysqli_stmt_bind_param($stmt, "ssssii", $caseid, $title, $details, $currentTimestamp, $user, $firm);
            mysqli_stmt_execute($stmt);

            // Get the newly created update ID
            $updtid = mysqli_insert_id($conn);
            mysqli_stmt_close($stmt);

            //create an email for the client
            $stmtc = $conn->prepare("
                                    SELECT 
                                        concat(cl.fname,' ',cl.lname) as clientName,
                                        cl.email,
                                        f.firmname,
                                        c1.courtname,
                                        c2.casenumber,
                                        c2.casename,
                                        c3.title,
                                        c3.details

                                        FROM case_updates c3
                                        JOIN cases c2 ON c3.caseid = c2.caseid
                                        JOIN firms f ON c3.firmid = f.firmid
                                        JOIN clients cl ON c2.clientid = cl.clientid
                                        JOIN courts c1 ON c2.courtid = c1.courtid
                                        WHERE c3.updateid = ?
                                        
                                        ");
            $stmtc->bind_param('s', $updtid);
            $stmtc->execute();
            $stmtc->bind_result($client, $recepient, $firmName, $courtName, $caseNum, $caseName, $title, $details);
            $stmtc->fetch();
            $stmtc->close();
            $subject = "InLaw Case Update";
            $message = mailCaseUpdate($client, $firmName, $courtName, $caseNum, $caseName, $title, $details);
            if (!noReplyMail($recepient, $subject, $message)) {
                $_SESSION['error'] = "Problem encountered when mailing the client";
            }
            header('location: case-updates');
            exit();

            $_SESSION['success'] = "Case Update Added Successfuly";
            header('location: case-updates');
            exit();
        } else {
            // Error preparing the statement
            $error_msg = 'Error preparing the SQL statement.';
        }
    }
} else if (isset($_POST['firm-login'])) {
    $firm_email = $_POST['firm-mail'];
    $firm_pass = $_POST['firm-pass'];

    //check whether firm exists
    $stmt = $conn->prepare('SELECT * FROM firms WHERE FirmMail = ?');
    $stmt->bind_param('s', $firm_email);
    if (!$stmt->execute()) {
        $_SESSION['error'] = "Error Getting Firm Details";
        header('location: firm-login');
        exit();
    }

    $res = $stmt->get_result();

    if ($row = mysqli_fetch_assoc($res)) {
        // Firm Acc found, now you can verify the password
        if (password_verify($firm_pass, $row['FirmPass'])) {
            // Password is correct, proceed with login
            $_SESSION['fid'] = $row['FirmID'];
            $_SESSION['firmname'] = $row['FirmName'];
            $_SESSION['fmail'] = $row['FirmMail'];
            $_SESSION['fpass'] = $row['FirmPass'];
            $_SESSION['faddress'] = $row['FirmAddress'];
            $_SESSION['flogo'] = $row['FirmLogo'];
            $_SESSION['fstatus'] = $row['FirmStatus'];
            $_SESSION['success'] = "Login Successful";

            // Update Firm table for last login 
            $stmt2 = $conn->prepare('UPDATE firms SET LastLogin = ? WHERE FirmID = ?');
            $stmt2->bind_param('si', $currentTimestamp, $row['FirmID']);
            $stmt2->execute();

            header('location: choose-user');
            exit();
        } else {
            // Incorrect password
            $_SESSION['error'] = "Invalid Credentials";
            header('location: firm-login');
            exit();
        }
    } else {
        // No firm acc found with that email
        $_SESSION['error'] = "Invalid Credentials";
        header('location: firm-login');
        exit();
    }
} else if (isset($_POST['user-login'])) {
    $user = $_POST['userid'];
    $pass = $_POST['password'];

    $stmt = $conn->prepare('SELECT * FROM users WHERE UserID = ?');
    $stmt->bind_param('i', $user);
    if (!$stmt->execute()) {
        $_SESSION['error'] = "Error Getting User Details";
        header('location: login?userid=' . $user);
        exit();
    }
    $res = $stmt->get_result();

    if ($row = mysqli_fetch_assoc($res)) {
        // User found, now you can verify the password
        if (password_verify($pass, $row['Password'])) {
            // Password is correct, proceed with login
            $_SESSION['userid'] = $row['UserID'];
            $_SESSION['fname'] = $row['FName'];
            $_SESSION['lname'] = $row['LName'];
            $_SESSION['pfp'] = $row['Photo'];
            $_SESSION['user_type'] = $row['User_type'];
            $_SESSION['success'] = "Login Successful";

            // Update User table for last login 
            $stmt2 = $conn->prepare('UPDATE users SET LastLogin = ? WHERE UserID = ?');
            $stmt2->bind_param('si', $currentTimestamp, $row['UserID']);
            $stmt2->execute();

            header('location: index');
            exit();
        } else {
            // Incorrect password
            $_SESSION['error'] = "Invalid Credentials";
            header('location: login?userid=' . $user);
            exit();
        }
    } else {
        // No user found with that id
        $_SESSION['error'] = "Invalid Credentials";
        header('location: login?userid=' . $user);
        exit();
    }
}
