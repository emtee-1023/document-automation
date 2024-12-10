<div class="container mt-4">
    <ul class="nav nav-tabs" id="taskTabs" role="tablist">
        <li class="nav-item" role="presentation">
            <button class="nav-link active" id="tab1-tab" data-bs-toggle="tab" data-bs-target="#tab1" type="button" role="tab" aria-controls="tab1" aria-selected="true">
                Tasks by me
            </button>
        </li>
        <li class="nav-item" role="presentation">
            <button class="nav-link" id="tab2-tab" data-bs-toggle="tab" data-bs-target="#tab2" type="button" role="tab" aria-controls="tab2" aria-selected="false">
                Tasks for me
            </button>
        </li>
    </ul>
    <div class="tab-content" id="taskTabsContent">
        <!-- Tab 1 -->
        <div class="tab-pane fade show active" id="tab1" role="tabpanel" aria-labelledby="tab1-tab">
            <!-- Existing table structure for Assigned Tasks -->
            <div class="card mb-4">
                <div class="card-header">
                    <i class="fas fa-table me-1"></i>
                    Displaying all assigned tasks
                </div>
                <div class="card-body">
                    <table id="datatablesSimple" class="table table-bordered table-striped">
                        <!-- Existing table structure -->
                        <thead>
                            <tr>
                                <th>Task</th>
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
                            // User and firm session variables
                            $user = $_SESSION['userid'];
                            $firmid = $_SESSION['fid'];

                            // SQL query using prepared statements
                            $stmt = $conn->prepare("
                                                    SELECT 
                                                        t1.TaskName, t1.TaskID,
                                                        CONCAT(u.fname,' ',u.lname) AS assignee,
                                                        t2.AssignedAt, t2.UserID,
                                                        t1.TaskDeadline, t1.TaskStatus
                                                    FROM tasks t1 
                                                     JOIN task_assignments t2  ON t1.taskid = t2.taskid 
                                                     JOIN users u ON u.userid = t2.userid
                                                    WHERE t1.firmid = ?
                                                ");
                            $stmt->bind_param("i", $firmid);
                            $stmt->execute();
                            $res = $stmt->get_result();

                            // Populate table rows with fetched data
                            while ($row = $res->fetch_assoc()) {
                                echo '<tr>
                            <td>' . htmlspecialchars($row['TaskName']) . '</td>
                            <td>' . htmlspecialchars($row['assignee']) . '</td>
                            <td>' . htmlspecialchars(date('D d M Y \a\t h.iA', strtotime($row['AssignedAt']))) . '</td>
                            <td>' . htmlspecialchars(date('D d M Y \a\t h.iA', strtotime($row['TaskDeadline']))) . '</td>
                            <td>' . htmlspecialchars($row['TaskStatus']) . '</td>
                            <td><a href="#" class="btn btn-primary btn-sm open-modal" data-id="' . $row['TaskID'] . '">View Task</a ></td>
                            <td><a href="delete?taskid=' . urlencode($row['TaskID']) . '&assignee=' . urlencode($row['UserID']) . '" class="btn btn-danger btn-sm">Delete Task</a ></td>
                        </tr>';
                            }
                            ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <!-- Tab 2 -->
        <div class="tab-pane fade" id="tab2" role="tabpanel" aria-labelledby="tab2-tab">
            <div class="card mb-4">
                <div class="card-header">
                    <i class="fas fa-table me-1"></i>
                    Displaying completed tasks
                </div>
                <div class="card-body">
                    <table id="datatablesCompleted" class="table table-bordered table-striped">
                        <thead>
                            <tr>
                                <th>Task</th>
                                <th>Assigned By</th>
                                <th>Assigned on</th>
                                <th>Deadline</th>
                                <th>Status</th>
                                <th>View</th>
                                <th>Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php
                            // User and firm session variables
                            $user = $_SESSION['userid'];
                            $firmid = $_SESSION['fid'];

                            // SQL query using prepared statements
                            $stmt = $conn->prepare("
                                                    SELECT 
                                                        t1.TaskName, t1.TaskID,
                                                        CONCAT(u.fname,' ',u.lname) AS tasker,
                                                        t2.AssignedAt, t2.UserID,
                                                        t1.TaskDeadline, t1.TaskStatus
                                                    FROM tasks t1 
                                                    JOIN task_assignments t2 ON  t1.taskid = t2.taskid 
                                                    JOIN users u ON u.userid = t1.userid
                                                    WHERE  t2.userid = ?
                                                ");
                            $stmt->bind_param("i", $user);
                            $stmt->execute();
                            $res = $stmt->get_result();

                            // Populate table rows with fetched data
                            while ($row = $res->fetch_assoc()) {
                                echo '<tr>
                            <td>' . htmlspecialchars($row['TaskName']) . '</td>
                            <td>' . htmlspecialchars($row['tasker']) . '</td>
                            <td>' . htmlspecialchars(date('D d M Y \a\t h.iA', strtotime($row['AssignedAt']))) . '</td>
                            <td>' . htmlspecialchars(date('D d M Y \a\t h.iA', strtotime($row['TaskDeadline']))) . '</td>
                            <td>' . htmlspecialchars($row['TaskStatus']) . '</td>
                            <td><a href="#" class="btn btn-primary btn-sm open-modal" data-id="' . $row['TaskID'] . '">View Task</a ></td>
                            <td><a href="complete-task?id=' . $row['TaskID'] . '" class="btn btn-success btn-sm">Complete Task</a ></td>
                        </tr>';
                            }
                            ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="descriptionModal" tabindex="-1" aria-labelledby="descriptionModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="descriptionModalLabel">Task Details</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <h6>Additional Task Details/Instructions:</h6>
                <p id="descriptionText" class="ms-1"></p>
                <a href="#" id="documentLink" class="btn btn-primary d-none" target="_blank">View Document</a>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>

<script>
    $(document).ready(function() {
        // Handle modal trigger
        $('.open-modal').on('click', function() {
            const taskId = $(this).data('id'); // Get the TaskID from the button's data attribute

            // AJAX request to fetch task details
            $.ajax({
                url: 'fetch-task-details.php', // Backend PHP file
                type: 'GET',
                data: {
                    id: taskId
                }, // Pass TaskID to the backend
                dataType: 'json', // Expect JSON response
                success: function(response) {
                    if (response.success) {
                        // Populate modal with task details
                        $('#descriptionText').text(response.data.description);

                        // Handle document link or display "No document attached"
                        if (response.data.document_url) {
                            $('#documentLink')
                                .attr('href', response.data.document_url)
                                .removeClass('d-none')
                                .text('View Document');
                        } else {
                            $('#documentLink')
                                .removeClass('d-none') // Make visible
                                .attr('href', '#') // Ensure no navigation
                                .text('No document attached') // Update text
                                .addClass('disabled'); // Add disabled class to prevent click
                        }

                        // Show modal
                        $('#descriptionModal').modal('show');
                    } else {
                        alert('Error: ' + response.message);
                    }
                },
                error: function(xhr, status, error) {
                    console.error('AJAX Error: ', error);
                    alert('An error occurred while fetching task details.');
                }
            });
        });
    });

    window.addEventListener("DOMContentLoaded", (event) => {
        // Initialize Simple DataTable for the first (visible) tab
        const table1 = document.getElementById('datatablesSimple');
        if (table1) {
            new simpleDatatables.DataTable(table1);
        }

        // Initialize Simple DataTables for other tabs only when they are shown
        document.querySelectorAll('button[data-bs-toggle="tab"]').forEach((button) => {
            button.addEventListener('shown.bs.tab', (e) => {
                let target = e.target.getAttribute("data-bs-target");

                if (target === "#tab2") {
                    const table2 = document.getElementById('datatablesCompleted');
                    if (table2 && !table2.dataset.initialized) {
                        new simpleDatatables.DataTable(table2);
                        table2.dataset.initialized = true; // Mark as initialized
                    }
                }
            });
        });
    });
</script>