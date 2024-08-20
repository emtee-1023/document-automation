<?php
include 'php/dbconn.php';
session_start();

// Enable error reporting for debugging
error_reporting(E_ALL);
ini_set('display_errors', 1);

if (isset($_GET['fileid'])) {
    // Sanitize the file parameter
    $fileId = intval($_GET['fileid']); // Convert to integer for security
    $owner = $_SESSION['userid'];

    // Prepare and execute query
    $stmt = $conn->prepare("SELECT 
                                cd.File
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
        $fileName = $row['File'];
        $filepath = 'assets/files/submitted/' . $fileName;

        // Check if file exists in the filesystem
        if (file_exists($filepath)) {
            // Delete the file
            if (unlink($filepath)) {
                $success_msg = "File deleted successfully.";
                
                // Optionally, delete the file record from the database
                $stmt = $conn->prepare("DELETE FROM case_docs WHERE docid = ?");
                $stmt->bind_param("i", $fileId);
                $stmt->execute();

                if ($stmt->affected_rows > 0) {
                    $success_msg = "Database record deleted successfully.";
                } else {
                    $rror_msg = "Error deleting record from database.";
                }
            } else {
                $error_msg = "Error deleting the file.";
            }
        } else {
            $error_msg = "File does not exist.";
        }
    } else {
        // No matching record found or the file doesn't belong to the user
        $rror_msg = "No matching record found.";
    }
} else {
    // No file parameter passed, unauthorized access
    $error_msg = "Unauthorized access.";
}
?>
