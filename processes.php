<?php
session_start();
include 'php/dbconn.php';
include 'php/mail.php';

date_default_timezone_set('Africa/Nairobi');
$currentTimestamp = date('Y-m-d H:i:s');
$_10Expiry = date('Y-m-d H:i:s', strtotime('+10 minutes'));

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
}
