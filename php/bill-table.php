<div class="card mb-4">
    <div class="card-header">
        <i class="fas fa-table me-1"></i>
        Displaying clients with pending invoices
    </div>
    <div class="card-body">
        <table id="datatablesSimple">
            <thead>
                <tr>
                    <th>Case Name</th>
                    <th>Client Type</th>
                    <th>Client Name</th>
                    <th>Email</th>
                    <th>Phone Number</th>
                    <th>Address</th>
                    <th>Invoices</th>
                </tr>
            </thead>
            <tbody>
            <?php
                $owner = $_SESSION['userid'];
                $firm = $_SESSION['fid'];
                // Use a prepared statement to avoid SQL injection
                $stmt = $conn->prepare("SELECT 
                                            cl.*, 
                                            c.CaseName, 
                                            COUNT(i.invoiceid) AS invoiceCount
                                        FROM 
                                            clients cl
                                        JOIN 
                                            cases c ON cl.clientid = c.clientid
                                        JOIN 
                                            invoices i ON cl.clientid = i.clientid
                                        WHERE 
                                            cl.firmid = ? AND i.Status = 'pending'
                                        GROUP BY 
                                            cl.clientid, c.caseid

                        ");
                        $owner = 1;
                $stmt->bind_param("i",$firm);
                $stmt->execute();
                $res = $stmt->get_result();

                while ($row = $res->fetch_assoc()) {
                    echo '<tr>
                            <td>'.$row['CaseName'].'</td>
                            <td>'.$row['ClientType'].'</td>
                            <td>'.$row['Prefix'].' '.$row['FName'].' '.$row['LName'].'</td>
                            <td>'.$row['Email'].'</td>
                            <td>'.$row['Phone'].'</td>
                            <td>'.$row['Address'].'</td>
                            <td><a href="?clientid='.$row['ClientID'].'" class="">'.$row['invoiceCount'].'</a></td>
                                                        
                        </tr>';
                }
            ?>

            </tbody>
        </table>
    </div>
</div>