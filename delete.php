<?php
include 'php/dbconn.php';
session_start();

// Enable error reporting for debugging
error_reporting(E_ALL);
ini_set('display_errors', 1);

//Deleting invoices
if (isset($_GET['invoiceid']) && isset($_GET['clientid'])) {
    // Sanitize the file parameter
    $invoiceid = intval($_GET['invoiceid']); // Convert to integer for security
    $clientid = intval($_GET['clientid']); // Convert to integer for security
    $owner = $_SESSION['userid'];
    $firm = $_SESSION['fid'];

    // Prepare and execute query
    $stmt = $conn->prepare("SELECT 
                                FilePath, InvoiceNumber
                            FROM 
                                invoice_uploads i
                            WHERE 
                                invoiceid = ?");
    if ($stmt === false) {
        die("Error preparing statement: " . $conn->error);
    }
    $stmt->bind_param("i", $invoiceid); // Bind the integer file ID
    $stmt->execute();
    $res = $stmt->get_result();

    // Check if we have a result and fetch the data
    if ($res === false) {
        die("Error executing query: " . $conn->error);
    }

    if ($row = $res->fetch_assoc()) {
        // The file exists in the database
        $fileName = $row['FileName'];
        $filepath = 'assets/files/submitted/' . $fileName; 
        $invoicenum = $row['InvoiceNumber'];   

                // Start a transaction
                $conn->begin_transaction();

                try {
                    // Prepare the statement to delete from invoice_uploads
                    $stmt1 = $conn->prepare("DELETE FROM invoice_uploads WHERE InvoiceID = ?");
                    $stmt1->bind_param("i", $invoiceid);
                    $stmt1->execute();

                    // Prepare the statement for notification
                    $stmt2 = mysqli_prepare($conn, "INSERT INTO notifications (NotifSubject, NotifText, UserID, ClientID) VALUES (?, ?, ?, ?)");
                    // Insert into notifications
                    $notifSubject = "Invoice has Been Deleted";
                    $notifText = 'This is to alert you that invoice number '.$invoicenum.' Has been Deleted';
                    mysqli_stmt_bind_param($stmt2, "ssii", $notifSubject, $notifText, $owner, $clientid);
                    mysqli_stmt_execute($stmt2);

                    // If both deletes are successful, commit the transaction
                    $conn->commit();

                    $success_msg = "Invoice and related items deleted successfully.";
                    header('Location: bill-clients?clientid=' . urlencode($clientid));
                    exit();
                } catch (Exception $e) {
                    // If there's an error, roll back the transaction
                    $conn->rollback();

                    $error_msg = "Failed to delete invoice and items: " . $e->getMessage();
                }

                // Close the statements
                $stmt1->close();
                $stmt2->close();
    } else {
        // No matching record found or the file doesn't belong to the user
        $rror_msg = "File does not exist.";
    }
} 

//Deleting tasks
else if (isset($_GET['taskid']) && isset($_GET['assignee'])) {
    // Sanitize the file parameter
    $taskid = intval($_GET['taskid']); // Convert to integer for security
    $assignee = intval($_GET['assignee']); // Convert to integer for security
    
    $user = $_SESSION['userid'];
    $firm = $_SESSION['fid'];

    // Prepare the statement to delete from task_assignments
    $stmt1 = $conn->prepare("DELETE FROM task_assignments WHERE UserID = ?");
    $stmt1->bind_param("i", $assignee);
    $stmt1->execute();

    $success_msg = "Task Assignment Deleted Successfuly";
    header('Location: tasks');
    exit();

    $stmt1->close();

}
//Deleting reminders
else if (isset($_GET['reminderid'])) {
    // Sanitize the file parameter
    $reminderid = intval($_GET['reminderid']); // Convert to integer for security
    
    $user = $_SESSION['userid'];
    $firm = $_SESSION['fid'];

    // Prepare the statement to delete from reminders
    $stmt1 = $conn->prepare("DELETE FROM reminders WHERE reminderid = ?");
    $stmt1->bind_param("i", $reminderid);
    $stmt1->execute();

    $success_msg = "Reminder Deleted Successfuly";
    header('Location: reminders');
    exit();

    $stmt1->close();

} else {
    // No file parameter passed, unauthorized access
    $error_msg = "Unauthorized access.";
    header('location: index');
    exit();
}  

?>
