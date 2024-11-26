<?php
// Start the session
session_start();

// Destroy the session
unset($_SESSION['fid']);

// Redirect to login page or home page
header("Location: choose-firm");
exit();
