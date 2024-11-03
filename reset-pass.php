<?php
session_start();

if (!isset($_GET['token'])) {
    $_SESSION['error_msg'] = "Invalid Token Used";
    header('location: password');
    exit();
}

$token = $_GET['token'];

$error_msg = $_SESSION['error_msg'] ?? '';
$success_msg = $_SESSION['success_msg'] ?? '';

unset($_SESSION['error_msg']);
unset($_SESSION['success_msg']);
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8" />
    <meta http-equiv="X-UA-Compatible" content="IE=edge" />
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no" />
    <meta name="description" content="" />
    <meta name="author" content="" />
    <title>Recover Password | InLaw</title>
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
                            <div class="card shadow-lg border-0 rounded-lg mt-5">
                                <div class="card-header">
                                    <h3 class="text-center font-weight-light my-4">Reset Password</h3>
                                </div>
                                <div class="card-body">
                                    <div class="small mb-3 text-muted">Enter a new Password</div>
                                    <form method="post" action="processes.php">
                                        <input type="text" name="token" hidden value="<?= $token ?>">
                                        <div class="form-floating mb-3">
                                            <input class="form-control" id="newPass" type="password" placeholder="New Password" required />
                                            <label for="newPass">New Password</label>
                                        </div>
                                        <div class="form-floating mb-3">
                                            <input class="form-control" id="confirmPass" type="password" placeholder="Confirm Password" name="password" required />
                                            <label for="confirmPass">Confirm Password</label>
                                        </div>
                                        <p id="passwordFeedback" style="color: red; display: none;">Passwords do not match.</p>
                                        <div class="d-flex align-items-center justify-content-between mt-4 mb-0">
                                            <input class="btn btn-primary" type="submit" value="Reset Password" name="reset-pass" id="resetButton" disabled>
                                        </div>
                                    </form>
                                </div>
                                <div class="card-footer text-center py-3">
                                    <div class="small"><a href="firm-login">Need an account? Sign up!</a></div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </main>
        </div>
    </div>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/js/bootstrap.bundle.min.js" crossorigin="anonymous"></script>
    <script src="js/scripts.js"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const newPassword = document.getElementById('newPass');
            const confirmPassword = document.getElementById('confirmPass');
            const submitButton = document.getElementById('resetButton');
            const feedback = document.getElementById('passwordFeedback');

            function checkPasswordsMatch() {
                if (newPassword.value && confirmPassword.value) {
                    if (newPassword.value === confirmPassword.value) {
                        submitButton.disabled = false;
                        feedback.style.display = 'none';
                    } else {
                        submitButton.disabled = true;
                        feedback.style.display = 'block';
                        feedback.textContent = 'Passwords do not match.';
                    }
                } else {
                    submitButton.disabled = true;
                    feedback.style.display = 'none';
                }
            }

            newPassword.addEventListener('input', checkPasswordsMatch);
            confirmPassword.addEventListener('input', checkPasswordsMatch);
        });
    </script>
</body>

</html>