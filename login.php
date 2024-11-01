<?php
include 'php/dbconn.php';
session_start();

$error_msg = '';

if (!isset($_SESSION['fid'])) {
    header('location: firm-login');
    exit();
}

if (!isset($_GET['userid'])) {
    header('location: choose-user');
} else {
    $user = $_GET['userid'];

    if (isset($_POST['login'])) {
        //$email = $_POST['email'];
        $pass = $_POST['password'];

        // Prepare a statement
        if ($stmt = mysqli_prepare($conn, "SELECT * FROM users WHERE userid = ?")) {
            // Bind parameters
            mysqli_stmt_bind_param($stmt, "s", $user);

            // Execute the statement
            mysqli_stmt_execute($stmt);

            // Get the result
            $res = mysqli_stmt_get_result($stmt);

            // Check if a user was found
            if ($row = mysqli_fetch_assoc($res)) {
                // User found, now you can verify the password
                if (password_verify($pass, $row['Password'])) {
                    // Password is correct, proceed with login
                    $_SESSION['userid'] = $row['UserID'];
                    $_SESSION['fname'] = $row['FName'];
                    $_SESSION['lname'] = $row['LName'];
                    $_SESSION['pfp'] = $row['Photo'];
                    $_SESSION['user_type'] = $row['User_type'];
                    header('location: index');
                } else {
                    // Incorrect password
                    $error_msg = 'invalid password';
                }
            } else {
                // No user found with that email
                $error_msg = 'invalid password';
            }

            // Close the statement
            mysqli_stmt_close($stmt);
        } else {
            // Error preparing the statement
            echo "Error preparing the SQL statement.";
        }
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
    <title>Login - DocAuto</title>
    <link href="css/styles.css" rel="stylesheet" />
    <script src="https://use.fontawesome.com/releases/v6.3.0/js/all.js" crossorigin="anonymous"></script>
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

                            <?php
                            if ($stmt = mysqli_prepare($conn, "SELECT * FROM users WHERE userid = ?")) {
                                // Bind parameters
                                mysqli_stmt_bind_param($stmt, "s", $user);

                                // Execute the statement
                                mysqli_stmt_execute($stmt);

                                // Get the result
                                $res = mysqli_stmt_get_result($stmt);

                                $row = mysqli_fetch_assoc($res);

                                $username = $row['FName'] . ' ' . $row['LName'];

                                // Close the statement
                                mysqli_stmt_close($stmt);
                            } else {
                                // Error preparing the statement
                                echo "Error preparing the SQL statement.";
                            }

                            ?>
                            <div class="card shadow-lg border-0 rounded-lg mt-5">
                                <div class="card-header">
                                    <h3 class="text-center font-weight-light my-4">Welcome Back <?php echo $username; ?></h3>
                                    <p class="text-center">Enter Password to sign in</p>
                                </div>
                                <div class="card-body">
                                    <form method="post" action="">
                                        <div class="form-floating mb-3">
                                            <input class="form-control" id="inputPassword" type="password" placeholder="Password" name="password" />
                                            <label for="inputPassword">Password</label>
                                        </div>
                                        <!-- <div class="form-check mb-3">
                                                <input class="form-check-input" id="inputRememberPassword" type="checkbox" value="" />
                                                <label class="form-check-label" for="inputRememberPassword">Remember Password</label>
                                            </div> -->
                                        <div class="d-flex align-items-center justify-content-between mt-4 mb-0">
                                            <a class="small" href="password.php">Forgot Password?</a>
                                            <input type="submit" class="btn btn-primary" name="login" value="login">
                                        </div>
                                    </form>
                                </div>
                                <div class="card-footer text-center py-3">
                                    <!-- <div class="small"><a href="register.html">Need an account? Sign up!</a></div> -->
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </main>
        </div>
        <div id="layoutAuthentication_footer">
            <?php include 'php/footer.php'; ?>