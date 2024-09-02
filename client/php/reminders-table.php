<div class="card mb-4">
    <div class="card-header">
        <i class="fas fa-table me-1"></i>
        Showing all active reminders
    </div>
    <div class="card-body">
        <table id="datatablesSimple">
            <thead>
                <tr>
                    <th>Case</th>
                    <th>Next Hearing</th>
                    <th>Bringup Date</th>
                    <th>View</th>
                </tr>
            </thead>
            <tbody>
            <?php
                $client = $_SESSION['clientid'];
                $firmid = $_SESSION['fid'];

                // Use a prepared statement to avoid SQL injection
                $stmt = $conn->prepare("SELECT 
                            r.reminderid, r.nextdate, r.bringupdate,
                            CONCAT(c1.fname,' ',c1.mname,' ',c1.lname) as client,
                            c2.casename
                        FROM 
                            reminders r 
                        JOIN 
                            clients c1 
                        ON 
                            r.clientid = c1.clientid 
                        JOIN
                            cases c2
                        ON
                            r.caseid = c2.caseid
                        WHERE
                            r.clientid = ?
                        ");
                $stmt->bind_param("i", $client);
                $stmt->execute();
                $res = $stmt->get_result();

                while ($row = $res->fetch_assoc()) {
                    echo '<tr>
                            <td>'.$row['casename'].'</td>
                            <td>'.$row['nextdate'].'</td>
                            <td>'.$row['bringupdate'].'</td>
                            <td><a href="#" class="">View Reminder</a ></td>
                        </tr>';
                }
            ?>

            </tbody>
        </table>
    </div>
</div>