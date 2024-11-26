<?php
session_start();

if (!isset($_GET['firm'])) {
    header('location: choose-firm');
    exit();
}

$_SESSION['fid'] = $_GET['firm'];
header('location: index');
exit();
