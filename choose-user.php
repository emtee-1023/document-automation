<?php
include 'php/dbconn.php';
session_start();
$pageTitle = 'InLaw | Choose User';
?>

<!DOCTYPE html>
<html lang="en">
<?php include 'php/head.php'; ?>

<body class="bg-light">
    <div id="layoutAuthentication">
        <div id="layoutAuthentication_content">
            <main>
                <div class="container">
                    <div class="row justify-content-center">
                        <div class="row g-5">
                            <div class="d-flex flex-column justify-content-center align-items-center my-0">
                                <div class="w-100 d-flex flex-column gap-4 flex-md-row align-items-center">
                                    <div class="flex-grow-1 d-flex flex-row justify-content-center"><img src="assets/img/submitted/<?php echo $_SESSION['flogo'] ?>" class="img-fluid" alt="Company Logo" style="height: 100px;"></div>
                                </div>
                                <div class="text-center">
                                    <p class="my-2">Signed in Under <strong><?php echo $_SESSION['firmname']; ?></strong></p>
                                    <p class="mt-0 mb-2"><strong>Choose</strong> a User to continue</p>
                                    <p><a href="logout" class="small mb-0">logout</a></p>
                                </div>
                            </div>
                            <?php
                            $stmt = $conn->prepare("SELECT * FROM users WHERE firmid = ?");
                            $stmt->bind_param("s", $_SESSION['fid']);
                            $stmt->execute();
                            $res = $stmt->get_result();
                            while ($row = mysqli_fetch_assoc($res)):
                            ?>
                                <div class="col-xl-3 col-md-6 mt-2">
                                    <a href="login?userid=<?= $row['UserID'] ?>&name=<?= $row['FName'] ?>" class="text-decoration-none text-black">
                                        <div class="card shadow-lg border-0 rounded-lg mt-5">
                                            <div class="card-header">
                                                <h3 class="text-center fs-5 font-weight-light my-2"><?= $row['FName'] ?> <?= $row['LName'] ?></h3>
                                            </div>
                                            <div class="card-body d-flex flex-column justify-content-center align-items-center">
                                                <div>
                                                    <img src="assets/img/submitted/<?= $row['Photo'] ?>" alt="Profile Picture" class="img-fluid rounded-circle" style="height:120px; width:120px;">
                                                </div>

                                            </div>
                                            <div class="card-footer text-center py-3">
                                                <div class="small"><?= $row['User_type'] ?></div>
                                            </div>
                                        </div>
                                    </a>
                                </div>
                            <?php endwhile ?>
                        </div>
                    </div>
                </div>
        </div>
        </main>
    </div>
    <div id="layoutAuthentication_footer">
        <?php include 'php/footer.php'; ?>