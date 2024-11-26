<?php
include '../php/dbconn.php';
session_start();

if (!isset($_SESSION['clientid'])) {
    header('client-portal');
    exit();
}

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
    <title>InLaw | Client Portal</title>
    <link href="css/styles.css" rel="stylesheet" />
    <script src="https://use.fontawesome.com/releases/v6.3.0/js/all.js" crossorigin="anonymous"></script>
</head>

<body class="bg-light">
    <div id="layoutAuthentication">
        <div id="layoutAuthentication_content">
            <main>
                <div class="container">
                    <div class="row justify-content-center">
                        <div class="row g-5">
                            <div class="d-flex flex-column justify-content-center align-items-center my-0">
                                <div class="text-center">
                                    <p class="my-2"><strong>Welcome Back, <?= $_SESSION['fname']; ?></strong></p>
                                    <p class="mt-0 mb-2"><strong>Choose which Law Firm you wish to access</strong></p>
                                    <p><a href="logout" class="small mb-0">logout</a></p>
                                </div>

                            </div>
                            <?php
                            if ($error_msg != '') {
                                echo
                                '
                                        <div class="alert alert-danger" role="alert">
                                            ' . $error_msg . '
                                        </div>
                                        ';
                            }
                            if ($success_msg != '') {
                                echo
                                '
                                        <div class="alert alert-success" role="alert">
                                            ' . $success_msg . '
                                        </div>
                                        ';
                            }
                            ?>
                            <?php
                            if ($stmt = mysqli_prepare($conn, "
                                                                SELECT firms.*
                                                                FROM firms
                                                                JOIN clients ON clients.FirmID = firms.FirmID
                                                                WHERE clients.ClientID = ?;
                                                                ")) {
                                // Bind parameters
                                mysqli_stmt_bind_param($stmt, "s", $_SESSION['clientid']);

                                // Execute the statement
                                mysqli_stmt_execute($stmt);

                                // Get the result
                                $res = mysqli_stmt_get_result($stmt);

                                while ($row = mysqli_fetch_assoc($res)) {
                                    echo
                                    '
                                            <div class="col-xl-3 col-md-6 mt-2">
                                            <a href="login?firm=' . $row['FirmID'] . '" class="text-decoration-none text-black">
                                                <div class="card shadow-lg border-0 rounded-lg mt-5">
                                                    <div class="card-header"><h3 class="text-center fs-5 font-weight-light my-2">' . $row['FirmName'] . '</h3></div>
                                                    <div class="card-body d-flex flex-column justify-content-center align-items-center">
                                                        <div>
                                                            <img src="../assets/img/submitted/' . ($row['FirmLogo'] ?? 'defaultlogo.png') . '" alt="Firm Logo" class="img-fluid" style="height:50px; width:250px;">
                                                        </div>
                                            
                                                    </div>
                                                    <div class="card-footer text-center py-3">
                                                        <div class="small">' . $row['Address'] . '</div>
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
        <?php include 'php/footer.php'; ?>