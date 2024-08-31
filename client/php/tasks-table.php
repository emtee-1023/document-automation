<div class="card mb-4">
    <div class="card-header">
        <i class="fas fa-table me-1"></i>
        Displaying all assigned tasks
    </div>
    <div class="card-body">
        <table id="datatablesSimple">
            <thead>
                <tr>
                    <th>TaskName</th>
                    <th>Assigned To</th>
                    <th>Assigned on</th>
                    <th>Deadline</th>
                    <th>Status</th>
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
                            t1.TaskName, t1.TaskID,
                            CONCAT(u.fname,' ',u.lname) as assignee,
                            t2.AssignedAt,t2.UserID,
                            t1.TaskDeadline,
                            t1.TaskStatus
                        FROM 
                            tasks t1 
                        JOIN 
                            task_assignments t2 
                        ON 
                            t1.taskid = t2.taskid 
                        JOIN
                            users u
                        ON
                            u.userid = t2.userid
                        WHERE
                            t1.userid = ?
                        ");
                $stmt->bind_param("i", $user);
                $stmt->execute();
                $res = $stmt->get_result();

                while ($row = $res->fetch_assoc()) {
                    echo '<tr>
                            <td>'.$row['TaskName'].'</td>
                            <td>'.$row['assignee'].'</td>
                            <td>'.$row['AssignedAt'].'</td>
                            <td>'.$row['TaskDeadline'].'</td>
                            <td>'.$row['TaskStatus'].'</td>
                            <td><a href="#" class="btn btn-primary btn-sm">View Task</a ></td>
                            <td><a href="delete?taskid='.$row['TaskID'].'&assignee='.$row['UserID'].'" class="btn btn-danger btn-sm">Delete Task</a ></td>
                        </tr>';
                }
            ?>

            </tbody>
        </table>
    </div>
</div>