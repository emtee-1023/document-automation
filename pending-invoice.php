<?php
include 'php/dbconn.php';
session_start();

if (isset($_GET['invid']) && isset($_GET['caseid'])) {
    $invoiceid = $_GET['invid'];
    $caseid = $_GET['caseid'];
    $pending = 'pending';

    // Debugging: Check if variables are set correctly
    if (empty($invoiceid) || empty($caseid)) {
        $error_msg = "Error: Invoice ID or Client ID is missing.";
        exit();
    }
    
    // Prepare the SQL statement
    $stmt = mysqli_prepare($conn, "UPDATE invoice_uploads SET Status = ? WHERE invoiceid = ?");
    if ($stmt) {
        mysqli_stmt_bind_param($stmt, "si", $pending, $invoiceid);
        if (mysqli_stmt_execute($stmt)) {
            // Success
            mysqli_stmt_close($stmt);
            header('Location: bill-clients?caseid=' . $caseid);
            exit();
        } else {
            // Error executing the statement
            $error_msg = "Error executing the SQL statement: " . mysqli_error($conn);
            mysqli_stmt_close($stmt);
            exit();
        }
    } else {
        // Error preparing the statement
        $error_msg = "Error preparing the SQL statement: " . mysqli_error($conn);
        exit();
    }
} else {
    $error_msg = "Error: Required parameters are missing.";
    header('location: bill-clients');
    exit();
}
?>

<div class="row justify-content-end">
                <?php 
                if($error_msg!=''){
                    echo
                    '
                    <div class="alert alert-danger" role="alert">
                        '.$error_msg.'
                    </div>
                    ';}
                ?>
                <?php 
                if($success_msg!=''){
                    echo
                    '
                    <div class="alert alert-success" role="alert">
                        '.$success_msg.'
                    </div>
                    ';}
                ?>
</div>
