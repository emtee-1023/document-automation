<div class="card mb-4">
    <div class="card-header">
        <i class="fas fa-table me-1"></i>
        Displaying all Clients
    </div>
    <div class="card-body">
        <table id="datatablesSimple">
            <thead>
                <tr>
                    <th>ClientID</th>
                    <th>Client Type</th>
                    <th>Full Name</th>
                    <th>Email</th>
                    <th>Phone Number</th>
                    <th>Address</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
            <?php
                $owner = $_SESSION['userid'];
                $firm = $_SESSION['fid'];
                // Use a prepared statement to avoid SQL injection
                $stmt = $conn->prepare("SELECT 
                        *
                        FROM 
                            clients c 
                        
                        WHERE 
                            c.firmid = ? 
                        ");
                $stmt->bind_param("i",$firm);
                $stmt->execute();
                $res = $stmt->get_result();

                while ($row = $res->fetch_assoc()) {
                    echo '<tr>
                            <td>'.$row['ClientID'].'</td>
                            <td>'.$row['ClientType'].'</td>
                            <td>'.$row['Prefix'].' '.$row['FName'].' '.$row['MName'].' '.$row['LName'].'</td>
                            <td>'.$row['Email'].'</td>
                            <td>'.$row['Phone'].'</td>
                            <td>'.$row['Address'].'</td>
                            <td><a href="edit-client?id='.$row['ClientID'].'" class="btn btn-primary btn-sm">Edit</a></td>
                                                        
                        </tr>';
                }
            ?>

            </tbody>
        </table>
    </div>
</div>