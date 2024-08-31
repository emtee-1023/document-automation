<?php
$success_msg = '';
$error_msg = '';

$owner = $_SESSION['userid'];
$firm =$_SESSION['fid'];

if(isset($_GET['caseid'])){
    $caseid = $_GET['caseid'];

    // Use a prepared statement to avoid SQL injection
    $stmt = $conn->prepare("SELECT * FROM cases c WHERE userid = ? AND caseid = ?");
    if (!$stmt) {
        die('Prepare failed: ' . $conn->error);
    }
    $stmt->bind_param("ii",$owner,$caseid);
    $stmt->execute();
    $res = $stmt->get_result();
    $row = $res->fetch_assoc();

    $casenumber = $row['CaseNumber'];
    $casename = $row['CaseName'];
    $case_id = $row['CaseID'];
    $cond = " AND c.caseid = $case_id";

    $table_title = "Showing case documents under <strong>Case: ".$casenumber."</strong>";
} else {
    $table_title = "Displaying all case documents";
    $cond = "";
}


?>

<?php
if (isset($_GET['fileid'])) {
    // Sanitize the file parameter
    $fileid = intval($_GET['fileid']); // Convert to integer for security
    

    // Prepare and execute query
    $stmt = $conn->prepare("SELECT 
                                cd.FilePath
                            FROM 
                                case_docs cd
                            WHERE 
                                cd.docid = ?");
    if ($stmt === false) {
        die("Error preparing statement: " . $conn->error);
    }
    $stmt->bind_param("i", $fileid); // Bind the integer file ID
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

        // Check if file exists in the filesystem
        if (file_exists($filepath)) {
            // Delete the file
            if (unlink($filepath)) {
                $success_msg = "File deleted successfully.";
                
                // Optionally, delete the file record from the database
                $stmt = $conn->prepare("DELETE FROM case_docs WHERE docid = ?");
                $stmt->bind_param("i", $fileid);
                $stmt->execute();

                if ($stmt->affected_rows > 0) {
                    $success_msg = "File deleted successfully.";
                } else {
                    $error_msg = "Error deleting record from database.";
                }
            } else {
                $error_msg = "Error deleting the file.";
            }
        } else {
            $error_msg = "File does not exist.";
        }
    } else {
        // No matching record found or the file doesn't belong to the user
        $error_msg = "No matching record found.";
    }
}

?>
<div class="mt-3">
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

<div class="card mb-4">
    <div class="card-header">
        <i class="fas fa-table me-1"></i>
        <?php echo $table_title;?>
    </div>
    <div class="card-body">
        <table id="datatablesSimple">
            <thead>
                <tr>
                    <th>DocID</th>
                    <th>Case Number</th>
                    <th>Document Name</th>
                    <th>Added on</th>
                    <th>Edit</th>
                    <th>Download</th>
                    <th>Delete</th>
                </tr>
            </thead>
            <tbody>
            <?php            
            // Use a prepared statement to avoid SQL injection
            $stmt = $conn->prepare("SELECT 
                                        cd.*,
                                        c.casenumber as casenumber,
                                        c.casename as casename
                                    FROM 
                                        case_docs cd
                                    JOIN 
                                        cases c ON cd.caseid = c.caseid
                                    WHERE 
                                        cd.firmid = ? $cond
                    ");
            $stmt->bind_param("i",$firm);
            $stmt->execute();
            $res = $stmt->get_result();

            while ($row = $res->fetch_assoc()) {
                echo '<tr>
                        <td>'.$row['DocID'].'</td>
                        <td>'.$row['casenumber'].'</td>
                        <td>'.$row['DocName'].'</td>
                        <td>'.$row['CreatedAt'].'</td>
                        <td><a href="edit-doc?fileid='.$row['DocID'].'" class="btn btn-primary btn-sm">Edit</a></td>
                        <td><a href="download?file='.$row['DocID'].'" class="btn btn-success btn-sm">Download</a></td>
                        <td><a href="?fileid='.$row['DocID'].'" class="btn btn-danger btn-sm">Delete</a></td>     
                    </tr>';
            }
            ?>

            </tbody>
        </table>
    </div>
</div>