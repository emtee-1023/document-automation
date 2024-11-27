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
                            <td><a href="#" class="view-reminder" data-id="' . $row['reminderid'] . '">View Reminder</a></td>
                            <td><a href="delete?reminderid=' . $row['reminderid'] . '" class="btn btn-danger btn-sm">Delete Reminder</a ></td>
                        </tr>';
                }
                ?>

            </tbody>
        </table>
    </div>
</div>


<!-- Modal Structure -->
<div class="modal fade" id="reminderModal" tabindex="-1" aria-labelledby="reminderModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="reminderModalLabel">Reminder Details</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <p><strong>Court Name:</strong> <span id="courtname"></span></p>
                <p><strong>Case Name:</strong> <span id="casename"></span></p>
                <p><strong>Case Number:</strong> <span id="casenumber"></span></p>
                <p><strong>Next Date:</strong> <span id="nextdate"></span></p>
                <p><strong>Bring Up Date:</strong> <span id="bringupdate"></span></p>
                <p><strong>Meeting Link:</strong> <a href="#" id="meetinglink" target="_blank"></a></p>
                <p><strong>Notes:</strong> <span id="notes"></span></p>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>