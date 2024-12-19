<?php
session_start();
$pageTitle = 'InLaw | Firm Login';
?>

<!DOCTYPE html>
<html lang="en">
<?php include 'php/head.php'; ?>

<body>
    <div id="layoutAuthentication">
        <div id="layoutAuthentication_content">
            <main>
                <div class="container d-flex flex-column flex-md-row justify-content-between align-items-center">

                    <section class=" section register min-vh-md-100 d-flex flex-md-column align-items-center justify-content-center col-6 col-md-5">
                        <img src="assets/img/login.svg" alt="" style="width: 100%;">
                    </section>

                    <section class="section register min-vh-md-100 d-flex flex-md-column align-items-center justify-content-center col-12 col-md-6">
                        <div class="container">
                            <div class="row justify-content-center">
                                <div class="col-lg-12 col-md-12 d-flex flex-column align-items-center justify-content-center">

                                    <div class="d-flex justify-content-center py-4">
                                        <img src="assets/img/icon.png" alt="" style="width: 30px;  height: 50px;">
                                        <span class="d-flex flex-column justify-content-center align-items-center ms-2">InLaw</span>
                                    </div><!-- End Logo -->

                                    <div class="card mb-3">

                                        <div class="card-body">

                                            <div class="pt-4 pb-2">
                                                <h5 class="card-title text-center pb-0 fs-4">Login to Your Firm</h5>
                                                <p class="text-center small">Enter your firm's email & password to login</p>
                                            </div>

                                            <form class="row g-3" action="processes.php" method="POST">
                                                <div class="col-12">
                                                    <label for="firmMail" class="form-label">Email</label>
                                                    <div class="input-group">
                                                        <input type="text" name="firm-mail" class="form-control" id="firmMail" required>
                                                    </div>
                                                </div>

                                                <div class="col-12">
                                                    <label for="firmPass" class="form-label">Password</label>
                                                    <input type="password" name="firm-pass" class="form-control" id="firmPass" required>
                                                </div>

                                                <!-- <div class="col-12">
                                                    <div class="form-check">
                                                        <input class="form-check-input" type="checkbox" name="remember" value="true" id="rememberMe">
                                                        <label class="form-check-label" for="rememberMe">Remember me</label>
                                                    </div>
                                                </div> -->

                                                <div class="col-12">
                                                    <button class="btn btn-primary w-100" type="submit" name="firm-login">Login</button>
                                                </div>
                                                <div class="col-12 d-flex justify-content-between">
                                                    <p class="small mb-0"><a href="client/index">Client Portal</a></p>
                                                    <p class="small mb-0"><a href="new-firm">Create Account</a></p>
                                                    <p class="small mb-0"><a href="password">Forgot Password</a></p>
                                                </div>
                                            </form>

                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                    </section>

                </div>
            </main>
        </div>
        <div id="layoutAuthentication_footer">
            <?php include 'php/footer.php'; ?>