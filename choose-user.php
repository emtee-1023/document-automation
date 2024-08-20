<?php
include 'php/dbconn.php';
session_start();

$error_msg = '';
$success_msg = '';
?>

<!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="utf-8" />
        <meta http-equiv="X-UA-Compatible" content="IE=edge" />
        <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no" />
        <meta name="description" content="" />
        <meta name="author" content="" />
        <title>Firm Login - DocAuto</title>
        <link href="css/styles.css" rel="stylesheet" />
        <script src="https://use.fontawesome.com/releases/v6.3.0/js/all.js" crossorigin="anonymous"></script>
        <style>
            /* Apply margin-left: 120px for medium screens and up */
            @media (min-width: 768px) {
                .custom-margin {
                    margin-left: 120px;
                }
            }
        </style>
    </head>
    <body class="bg-light">
        <div id="layoutAuthentication">
            <div id="layoutAuthentication_content">
                <main> 
                    <div class="container">
                        <div class="row justify-content-center">
                            <div class="row g-5">
                                    <div class="d-flex flex-column justify-content-center align-items-center my-0">
                                        <div class="w-100 d-flex flex-column gap-4 flex-md-row align-items-center">
                                            <div class="flex-grow-1 d-flex flex-row justify-content-center custom-margin"><img src="assets/img/submitted/<?php echo $_SESSION['flogo']?>" class="img-fluid" alt="Company Logo" style="height: 100px;"></div>
                                            <div><a href="logout" class="btn btn-secondary ms-auto"><i class="fa-solid fa-user-xmark"></i> Sign Out</a></div>
                                            
                                        </div>
                                        <div class="text-center">
                                            <p class="my-2">Signed in Under <strong><?php echo $_SESSION['firmname'];?></strong></p>
                                            <p class="my-0"><strong>Choose</strong> a User to continue</p>
                                        </div>
                                        
                                    </div>
                                    <?php 
                                    if($error_msg!=''){
                                        echo
                                        '
                                        <div class="alert alert-danger" role="alert">
                                            '.$error_msg.'
                                        </div>
                                        ';
                                    }
                                    if($success_msg!=''){
                                        echo
                                        '
                                        <div class="alert alert-success" role="alert">
                                            '.$success_msg.'
                                        </div>
                                        ';
                                    }
                                    ?>  
                                    <?php
                                    if ($stmt = mysqli_prepare($conn, "SELECT * FROM users WHERE firmid = ?")) {
                                        // Bind parameters
                                        mysqli_stmt_bind_param($stmt, "s", $_SESSION['fid']);
                                
                                        // Execute the statement
                                        mysqli_stmt_execute($stmt);
                                
                                        // Get the result
                                        $res = mysqli_stmt_get_result($stmt);

                                        while($row=mysqli_fetch_assoc($res)){
                                            echo
                                            '
                                            <div class="col-xl-3 col-md-6 mt-2">
                                            <a href="login?userid='.$row['UserID'].'" class="text-decoration-none text-black">
                                                <div class="card shadow-lg border-0 rounded-lg mt-5">
                                                    <div class="card-header"><h3 class="text-center fs-5 font-weight-light my-2">'.$row['FName'].' '.$row['LName'].'</h3></div>
                                                    <div class="card-body d-flex flex-column justify-content-center align-items-center">
                                                        <div>
                                                            <img src="assets/img/submitted/'.$row['Photo'].'" alt="Profile Picture" class="img-fluid rounded-circle" style="height:120px; width:120px;">
                                                        </div>
                                            
                                                    </div>
                                                    <div class="card-footer text-center py-3">
                                                        <div class="small">'.$row['User_type'].'</div>
                                                    </div>
                                                </div>
                                            </a>
                                            </div>
                                            
                                            ';
                                        }
                                
                                        // Close the statement
                                        mysqli_stmt_close($stmt);
                                    } else {
                                        // Error preparing the statement
                                        echo "Error preparing the SQL statement.";
                                    }
                                    
                                    ?>
                                    
                                    
                                </div>
                            </div>
                        </div>
                    </div>
                </main>
                </div>
            <div id="layoutAuthentication_footer">
           <?php include 'php/footer.php';?>
