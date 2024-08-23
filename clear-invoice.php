<?php
include 'php/header.php';

if (isset($_GET['invid']) && isset($_GET['clid'])) {
    $invoiceid = $_GET['invid'];
    $clientid = $_GET['clid'];
    $cleared = 'cleared';

    // Debugging: Check if variables are set correctly
    if (empty($invoiceid) || empty($clientid)) {
        echo "Error: Invoice ID or Client ID is missing.";
        exit();
    }
    
    // Prepare the SQL statement
    $stmt = mysqli_prepare($conn, "UPDATE invoices SET Status = ? WHERE invoiceid = ?");
    if ($stmt) {
        mysqli_stmt_bind_param($stmt, "si", $cleared, $invoiceid);
        if (mysqli_stmt_execute($stmt)) {
            // Success
            mysqli_stmt_close($stmt);
            header('Location: bill-clients?clientid=' . $clientid);
            exit();
        } else {
            // Error executing the statement
            echo "Error executing the SQL statement: " . mysqli_error($conn);
            mysqli_stmt_close($stmt);
            exit();
        }
    } else {
        // Error preparing the statement
        echo "Error preparing the SQL statement: " . mysqli_error($conn);
        exit();
    }
} else {
    echo "Error: Required parameters are missing.";
}
?>
