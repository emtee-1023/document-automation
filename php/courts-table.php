<div class="card mb-4">
    <div class="card-header">
        <i class="fas fa-table me-1"></i>
        Displaying all courts
    </div>
    <div class="card-body">
        <table id="datatablesSimple">
            <thead>
                <tr>
                    <th>CourtID</th>
                    <th>Court Name</th>
                    <th>Active</th>
                    <th>Pending</th>
                    <th>Closed</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
            <?php
                $owner = $_SESSION['userid'];
                // Use a prepared statement to avoid SQL injection
                $stmt = $conn->prepare("SELECT 
                            c1.courtid, 
                            c1.courtname, 
                            COUNT(CASE WHEN c2.casestatus = 'open' THEN 1 END) AS num_open,
                            COUNT(CASE WHEN c2.casestatus = 'closed' THEN 1 END) AS num_closed,
                            COUNT(CASE WHEN c2.casestatus = 'pending' THEN 1 END) AS num_pending
                        FROM 
                            courts c1 
                        LEFT JOIN 
                            cases c2 
                        ON 
                            c1.courtid = c2.Courtid 
                        WHERE 
                            c1.added_by = ? 
                        GROUP BY 
                            c1.courtid, 
                            c1.courtname");
                $stmt->bind_param("i", $owner);
                $stmt->execute();
                $res = $stmt->get_result();

                while ($row = $res->fetch_assoc()) {
                    echo '<tr>
                            <td>'.$row['courtid'].'</td>
                            <td>'.$row['courtname'].'</td>
                            <td><a href="cases?courtid='.$row['courtid'].'&status=1">'.$row['num_open'].'</a></td>
                            <td><a href="cases?courtid='.$row['courtid'].'&status=2">'.$row['num_pending'].'</a></td>
                            <td><a href="cases?courtid='.$row['courtid'].'&status=3">'.$row['num_closed'].'</a></td>  
                            <td><a href="edit-court?id='.$row['courtid'].'" class="btn btn-primary btn-sm">Edit</a></td>                          
                        </tr>';
                }
            ?>

            </tbody>
        </table>
    </div>
</div>