<?php
include '../php/dbconn.php';
session_start();

$error_msg = '';

if (isset($_POST['login'])) {
    $client_email = $_POST['email'];
    $client_pass = $_POST['password'];

    // Prepare a statement
    if ($stmt = mysqli_prepare($conn, "SELECT * FROM clients WHERE email = ?")) {
        // Bind parameters
        mysqli_stmt_bind_param($stmt, "s", $client_email);

        // Execute the statement
        mysqli_stmt_execute($stmt);

        // Get the result
        $res = mysqli_stmt_get_result($stmt);

        // Check if a user was found
        if ($row = mysqli_fetch_assoc($res)) {
            // User found, now you can verify the password
            if (password_verify($client_pass, $row['PortalPass'])) {
                // Password is correct, proceed with login
                $_SESSION['clientid'] = $row['ClientID'];
                $_SESSION['userid'] = $row['ClientID'];
                $_SESSION['fname'] = $row['FName'];
                $_SESSION['mname'] = $row['MName'];
                $_SESSION['lname'] = $row['LName'];
                $_SESSION['email'] = $row['Email'];

                header('location: choose-firm');
            } else {
                // Incorrect password
                $error_msg = 'invalid credentials';
            }
        } else {
            // No user found with that email
            $error_msg = 'invalid credentials';
        }

        // Close the statement
        mysqli_stmt_close($stmt);
    } else {
        // Error preparing the statement
        $error_msg = "Error preparing the SQL statement.";
    }
}

?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8" />
    <meta http-equiv="X-UA-Compatible" content="IE=edge" />
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no" />
    <meta name="description" content="" />
    <meta name="author" content="" />
    <title>Client Login - DocAuto</title>
    <link href="css/styles.css" rel="stylesheet" />
    <script src="https://use.fontawesome.com/releases/v6.3.0/js/all.js" crossorigin="anonymous"></script>
    <link rel="shortcut icon" href="../assets/img/icon.png" type="image/x-icon">
</head>

<body class="bg-dark">
    <div id="layoutAuthentication">
        <div id="layoutAuthentication_content">
            <main>
                <div class="container">
                    <div class="row justify-content-center">
                        <div class="col-lg-5">
                            <div class="mt-3">
                                <?php
                                if ($error_msg != '') {
                                    echo
                                    '
                                        <div class="alert alert-danger" role="alert">
                                            ' . $error_msg . '
                                        </div>
                                        ';
                                }
                                ?>

                            </div>
                            <div class="card shadow-lg border-0 rounded-lg mt-5">
                                <div class="card-header">
                                    <h3 class="text-center font-weight-light my-4">Login to Client Portal</h3>
                                </div>
                                <div class="card-body">
                                    <form method="post" action="">
                                        <div class="form-floating mb-3">
                                            <input class="form-control" id="inputEmail" type="email" placeholder="email address" name="email" />
                                            <label for="inputEmail">Email address</label>
                                        </div>
                                        <div class="form-floating mb-3">
                                            <input class="form-control" id="inputPassword" type="password" placeholder="Password" name="password" />
                                            <label for="inputPassword">Password</label>
                                        </div>
                                        <!-- <div class="form-check mb-3">
                                                <input class="form-check-input" id="inputRememberPassword" type="checkbox" value="" />
                                                <label class="form-check-label" for="inputRememberPassword">Remember Password</label>
                                            </div> -->
                                        <div class="d-flex align-items-center justify-content-between mt-4 mb-0">
                                            <a class="small" href="password.html">Forgot Password?</a>
                                            <input type="submit" class="btn btn-primary" name="login" value="login">
                                        </div>
                                    </form>
                                </div>
                                <div class="card-footer text-center py-3">
                                    <div class="small"><a href="../firm-login">Login as Firm</a></div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </main>
        </div>
        <div id="layoutAuthentication_footer">
            <?php include 'php/footer.php'; ?>