<?php
// Start the session
session_start();

// Destroy the session
unset($_SESSION['userid']);
unset($_SESSION['fname']);
unset($_SESSION['lname']);
unset($_SESSION['pfp']);
unset($_SESSION['user_type']);

// Redirect to login page or home page
header("Location: choose-user");
exit();
?>
