<?php
include 'php/dbconn.php';
session_start();

// Enable error reporting for debugging
error_reporting(E_ALL);
ini_set('display_errors', 1);


//Downloading A Case-Doc
if (isset($_GET['file'])) {
    // Sanitize the file parameter
    $fileId = intval($_GET['file']); // Convert to integer for security
    $owner = $_SESSION['userid'];

    // Prepare and execute query
    $stmt = $conn->prepare("SELECT 
                                cd.*
                                
                            FROM 
                                case_docs cd
                            WHERE 
                                cd.docid = ?");
    if ($stmt === false) {
        die("Error preparing statement: " . $conn->error);
    }
    $stmt->bind_param("i", $fileId); // Bind the integer file ID
    $stmt->execute();
    $res = $stmt->get_result();

    // Check if we have a result and fetch the data
    if ($res === false) {
        die("Error executing query: " . $conn->error);
    }

    if ($row = $res->fetch_assoc()) {
        // The file exists in the database
        $fileName = $row['FilePath'];
        $filepath = 'assets/files/submitted/' . $fileName;
        $extension = $row['Extension'];
        $downloadName = $row['DocName'].'.'.$extension; 
        

        // Check if file exists in the filesystem
        if (file_exists($filepath)) {
            // Set headers to trigger the download with the original name
            header('Content-Description: File Transfer');
            header('Content-Type: application/octet-stream');
            header('Content-Disposition: attachment; filename="' . $downloadName . '"'); // Use basename for security
            header('Expires: 0');
            header('Cache-Control: must-revalidate');
            header('Pragma: public');
            header('Content-Length: ' . filesize($filepath));

            // Clear output buffer to avoid corrupting the file
            ob_clean();
            flush();

            // Read the file and output its contents
            readfile($filepath);
            exit;
        } else {
            // File doesn't exist on the server, redirect to custom 404 page
            header("Location: /404");
            exit();
        }
    } else {
        // No matching record found or the file doesn't belong to the user
        header('Location: /401');
        exit();
    }
} elseif (isset($_GET['invoiceid'])) {
    // Sanitize the file parameter
    $invoiceid = intval($_GET['invoiceid']); // Convert to integer for security
    $user = $_SESSION['userid'];
    $firm = $_SESSION['fid'];

    // Prepare and execute query
    $stmt = $conn->prepare("SELECT 
                                i.*
                                
                            FROM 
                                invoice_uploads i
                            WHERE 
                                i.invoiceid = ?");
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
        $extension = $row['Extension'];
        $downloadName = $row['InvoiceNumber'].'.'.$extension; 
        

        // Check if file exists in the filesystem
        if (file_exists($filepath)) {
            // Set headers to trigger the download with the original name
            header('Content-Description: File Transfer');
            header('Content-Type: application/octet-stream');
            header('Content-Disposition: attachment; filename="' . $downloadName . '"'); // Use basename for security
            header('Expires: 0');
            header('Cache-Control: must-revalidate');
            header('Pragma: public');
            header('Content-Length: ' . filesize($filepath));

            // Clear output buffer to avoid corrupting the file
            ob_clean();
            flush();

            // Read the file and output its contents
            readfile($filepath);
            exit;
        } else {
            // File doesn't exist on the server, redirect to custom 404 page
            header("Location: /404");
            exit();
        }
    } else {
        // No matching record found or the file doesn't belong to the user
        header('Location: /401');
        exit();
    }
}else {
    // No file parameter passed, unauthorized access
    header('Location: /401');
    exit();
}
?>
