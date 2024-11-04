<div class="card mb-4">
    <div class="card-header">
        <i class="fas fa-table me-1"></i>
        Showing all active reminders
    </div>
    <div class="card-body">
        <table id="datatablesSimple">
            <thead>
                <tr>
                    <th>Court Name</th>
                    <th>Case Number</th>
                    <th>Case Name</th>
                    <th>Client Name</th>
                    <th>Advocate in Charge</th>
                    <th>Next Date</th>
                    <th>Bringup Date</th>
                    <th>View</th>
                    <th>Delete</th>
                </tr>
            </thead>
            <tbody>
                <?php
                $user = $_SESSION['userid'];
                $firmid = $_SESSION['fid'];

                // Use a prepared statement to avoid SQL injection
                $stmt = $conn->prepare("SELECT 
                            r.reminderid, r.nextdate, r.bringupdate,
                            CONCAT(c1.fname,' ',c1.mname,' ',c1.lname) as client,
                            c2.casename, c2.casenumber,
                            c3.courtname,
                            CONCAT(u.fname,' ',c1.lname) as advocate
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
                        JOIN
                            courts c3
                        ON
                            c3.courtid = c2.courtid
                        JOIN
                            users u
                        ON 
                            u.userid = c2.userid
                        WHERE
                            r.firmid = ?
                        ");
                $stmt->bind_param("i", $firmid);
                $stmt->execute();
                $res = $stmt->get_result();

                while ($row = $res->fetch_assoc()) {
                    echo '<tr>
                            <td>' . $row['courtname'] . '</td>
                            <td>' . $row['casenumber'] . '</td>
                            <td>' . $row['casename'] . '</td>
                            <td>' . $row['client'] . '</td>
                            <td>' . $row['advocate'] . '</td>
                            <td>' . date('D d M Y \a\t h.iA', strtotime($row['nextdate'])) . '</td>
                            <td>' . date('D d M Y \a\t h.iA', strtotime($row['bringupdate'])) . '</td>
                            <td><a href="#" class="">View Reminder</a ></td>
                            <td><a href="delete?reminderid=' . $row['reminderid'] . '" class="btn btn-danger btn-sm">Delete Reminder</a ></td>
                        </tr>';
                }
                ?>

            </tbody>
        </table>
    </div>
</div>