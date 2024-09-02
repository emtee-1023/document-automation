<?php
include 'php/dbconn.php';
session_start();

$success_msg = '';
$error_msg = '';

if (isset($_GET['invid']) && isset($_GET['caseid'])) {
    $invoiceid = intval($_GET['invid']);
    $caseid = intval($_GET['caseid']);
    $cleared = 'cleared';

    // Check if invoiceid and caseid are valid integers
    if ($invoiceid > 0 && $caseid > 0) {
        // Prepare the SQL statement
        $stmt = mysqli_prepare($conn, "UPDATE invoice_uploads SET Status = ? WHERE invoiceid = ?");
        if ($stmt) {
            mysqli_stmt_bind_param($stmt, "si", $cleared, $invoiceid);
            if (mysqli_stmt_execute($stmt)) {
                // Success: Redirect with a success message
                mysqli_stmt_close($stmt);
                header('Location: bill-clients?caseid=' . $caseid);
                exit();
            } else {
                // Error executing the statement
                $error_msg = "Error executing the SQL statement: " . mysqli_error($conn);
                mysqli_stmt_close($stmt);
                //header('Location: bill-clients?caseid=' . $caseid . '&error=execution');
                //exit();
            }
        } else {
            // Error preparing the statement
            $error_msg = "Error preparing the SQL statement: " . mysqli_error($conn);
            //header('Location: bill-clients?caseid=' . $caseid . '&error=preparation');
            //exit();
        }
    } else {
        // Invalid invoiceid or caseid
        //header('Location: bill-clients?error=invalid_parameters');
        //exit();
    }
} else {
    // Missing required parameters
    header('Location: bill-clients?error=missing_parameters');
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
