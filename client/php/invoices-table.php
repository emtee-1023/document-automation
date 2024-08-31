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
                $client = $_SESSION['clientid'];
                $firm = $_SESSION['fid'];
                $caseid = 
                // Use a prepared statement to avoid SQL injection
                $stmt = $conn->prepare("SELECT 
                                            c.CaseID,
                                            c.CaseName, 
                                            cl.*, 
                                            COUNT(i.invoiceid) AS invoiceCount
                                        FROM 
                                            cases c
                                        JOIN 
                                            clients cl ON cl.clientid = c.clientid
                                        LEFT JOIN 
                                            invoices i ON c.caseid = i.caseid
                                        WHERE 
                                            i.clientid = ?
                                        GROUP BY 
                                            c.CaseID


                        ");
                $stmt->bind_param("i",$client);
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
                            <td><a href="?caseid='.$row['CaseID'].'" class="">'.$row['invoiceCount'].'</a></td>                                                        
                        </tr>';
                }
            ?>

            </tbody>
        </table>
    </div>
</div>