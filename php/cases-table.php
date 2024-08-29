<?php
if(isset($_GET['status'])){
    $status = $_GET['status']; //1=open 2=pending 3=closed
    switch ($status) {
        case 1:
            $title = 'Displaying Open Cases';
            $cond = "and c1.casestatus='open'";
            break;
        case 2:
            $title = 'Displaying Pending Cases';
            $cond = "and c1.casestatus='pending'";
            break;
        case 3:
            $title = 'Displaying Closed Cases';
            $cond = "and c1.casestatus='closed'";
            break;
        
        default:
            $title = 'Displaying All Cases';
            $cond = '';
            break;
    }
} else {
    $title = "Displaying All Cases";
    $cond = "";
}

?>

<div class="card mb-4">
    <div class="card-header">
        <i class="fas fa-table me-1"></i>
        <?php echo $title;?>
    </div>
    <div class="card-body">
        <table id="datatablesSimple">
            <thead>
                <tr>
                    <th>Case Number</th>
                    <th>Case Name</th>
                    <th>Case Description</th>
                    <th>Client Name</th>
                    <th>Advocate in Charge</th>
                    <th>Case Status</th>
                    <th>Open Date</th>
                    <th>Close Date</th>
                    <th>Court Name</th>
                    <th>Uploaded Documents</th>
                    <th>Action</th>
                </tr>
            </thead>
            <tbody>
            <?php
            $owner = $_SESSION['userid'];
            $firm = $_SESSION['fid'];

            // Use a prepared statement to avoid SQL injection
            $stmt = $conn->prepare("SELECT
                                        c1.*,
                                        CONCAT(c2.prefix, ' ', c2.fname, ' ', c2.lname) as ClientName,
                                        CONCAT(u.fname,' ',u.lname) as advocatename,
                                        c3.courtname as CourtName
                                    FROM 
                                        cases c1
                                    JOIN
                                        clients c2 ON c1.clientid = c2.clientid
                                    JOIN
                                        courts c3 ON c1.courtid = c3.courtid
                                    JOIN 
                                        users u on u.userid = c1.userid
                                    WHERE 
                                        c1.firmid = ?");
            $stmt->bind_param("i", $firm);
            $stmt->execute();
            $res = $stmt->get_result();

            while ($row = $res->fetch_assoc()) {
                // Count the number of documents in the current case
                $stmtDocCount = $conn->prepare("SELECT 
                                                    COUNT(cd.docid) AS num_documents
                                                FROM 
                                                    case_docs cd
                                                JOIN 
                                                    cases c ON cd.caseid = c.caseid
                                                WHERE 
                                                    c.userid = ? AND cd.caseid = ?");
                $stmtDocCount->bind_param("ii", $owner, $row['CaseID']);
                $stmtDocCount->execute();
                $documentCountResult = $stmtDocCount->get_result();

                // Fetch the count into $num
                if ($docCountRow = $documentCountResult->fetch_assoc()) {
                    $num = $docCountRow['num_documents'];
                } else {
                    $num = 0; // Default value if no documents are found
                }
                
                echo '<tr>
                        <td>'.$row['CaseNumber'].'</td>
                        <td>'.$row['CaseName'].'</td>
                        <td>'.$row['CaseDescription'].'</td>
                        <td>'.$row['ClientName'].'</td>
                        <td>'.$row['advocatename'].'</td>
                        <td>'.$row['CaseStatus'].'</td>
                        <td>'.$row['OpenDate'].'</td>
                        <td>'.$row['CloseDate'].'</td>
                        <td>'.$row['CourtName'].'</td>
                        <td><a href="case-docs?caseid='.$row['CaseID'].'">'.$num.'</a></td>
                        <td><a href="edit-case?id='.$row['CaseID'].'" class="btn btn-primary btn-sm">Edit</a></td>
                    </tr>';
            }
            ?>


            </tbody>
        </table>
    </div>
</div>