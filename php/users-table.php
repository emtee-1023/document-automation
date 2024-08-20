<?php

$success_msg = '';
$error_msg = '';
if (isset($_GET['userid'])) {
    // Sanitize the file parameter
    $userid = intval($_GET['userid']); // Convert to integer for security
    $owner = $_SESSION['userid'];
                
    // Optionally, delete the file record from the database
    $stmt = $conn->prepare("DELETE FROM users WHERE userid = ?");
    $stmt->bind_param("i", $userid);
    $stmt->execute();

    if ($stmt->affected_rows > 0) {
        $success_msg = "User Account deleted successfully.";
    } else {
        //$error_msg = "Error deleting User from database.";
    }
            
    }

?>

<div class="mt-3">
    <?php 
    if($error_msg!=''){
        echo
        '
        <div class="alert alert-danger" role="alert">
            '.$error_msg.'
        </div>
        ';}
    ?>
    <?php 
    if($success_msg!=''){
        echo
        '
        <div class="alert alert-success" role="alert">
            '.$success_msg.'
        </div>
        ';}
    ?>
</div>

<div class="card mb-4">
    <div class="card-header">
        <i class="fas fa-table me-1"></i>
        Displaying Users
    </div>
    <div class="card-body">
        <table id="datatablesSimple">
            <thead>
                <tr>
                    <th>User Photo</th>
                    <th>User Name</th>
                    <th>Email</th>
                    <th>User Type</th>
                    <th>Status</th>
                    <th>Edit</th>
                    <th>Delete</th>
                </tr>
            </thead>
            <tbody>
            <?php
                $owner = $_SESSION['userid'];
                $firm = $_SESSION['fid'];
                // Use a prepared statement to avoid SQL injection
                $stmt = $conn->prepare("SELECT 
                        *,
                        concat(fname,' ',lname) as username
                        FROM 
                            users u                        
                        WHERE 
                            u.firmid = ? 
                        ");
                $stmt->bind_param("i",$firm);
                $stmt->execute();
                $res = $stmt->get_result();
                $status='stat';

                while ($row=$res->fetch_assoc()) {
                    if($row['Status']==1) {
                        $status = "Active";
                    } else {
                        $status = "Inactive";
                    }
                    echo '<tr>
                            <td><img src="assets/img/submitted/'.$row['Photo'].'" style="height: 50px; width: 50px; border-radius: 50%;"></td>
                            <td>'.$row['username'].'</td>
                            <td>'.$row['Email'].'</td>
                            <td>'.$row['User_type'].'</td>
                            <td>'.$status.'</td>
                            <td><a href="edit-user?id='.$row['UserID'].'" class="btn btn-primary btn-sm">Edit</a></td>     
                            <td><a href="?userid='.$row['UserID'].'" class="btn btn-danger btn-sm">Delete</a></td>                      
                        </tr>';
                }
            ?>

            </tbody>
        </table>
    </div>
</div>