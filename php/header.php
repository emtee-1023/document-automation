<?php
include 'php/dbconn.php';
session_start();

if (!isset($_SESSION['userid']) && !isset($_SESSION['fid'])) {
    header('location: firm-login');
} elseif (!isset($_SESSION['userid']) && isset($_SESSION['fid'])) {
    header('location: login');
}
$error_msg =  '';
$pageTitle = '';
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8" />
    <meta http-equiv="X-UA-Compatible" content="IE=edge" />
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no" />
    <meta name="description" content="" />
    <meta name="author" content="" />
    <title><?php echo $pageTitle; ?></title>
    <link rel="stylesheet" href="https://cdn.datatables.net/2.1.3/css/dataTables.dataTables.css" />
    <script src="https://cdn.datatables.net/2.1.3/js/dataTables.js"></script>
    <link href="https://cdn.jsdelivr.net/npm/simple-datatables@7.1.2/dist/style.min.css" rel="stylesheet" />
    <link href="css/styles.css" rel="stylesheet" />
    <script src="https://use.fontawesome.com/releases/v6.3.0/js/all.js" crossorigin="anonymous"></script>
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.6.0/jquery.min.js"></script>

    <style>
        /* Center the dropdown on small screens */
        @media (max-width: 575.98px) {
            .dropdown-menu-sm-center {
                right: 50%;
                left: 50%;
                transform: translateX(37%);
                min-width: 200px;
                /* Adjust the minimum width if necessary */
                /* width: auto; Ensure the dropdown adapts to its content */

            }
        }
    </style>

</head>

<body class="sb-nav-fixed">
    <nav class="sb-topnav navbar navbar-expand navbar-dark bg-dark">
        <!-- Navbar Brand-->
        <a class="navbar-brand ps-3" href="index">InLaw</a>
        <!-- Sidebar Toggle-->
        <button class="btn btn-link btn-sm order-1 order-lg-0 me-4 me-lg-0" id="sidebarToggle" href="#!"><i class="fas fa-bars"></i></button>

        <!-- Navbar Search-->
        <!-- <form class="d-none d-md-inline-block form-inline ms-auto me-0 me-md-3 my-2 my-md-0">
            <div class="input-group">
                <input class="form-control" type="text" placeholder="Search for..." aria-label="Search for..." aria-describedby="btnNavbarSearch" />
                <button class="btn btn-primary" id="btnNavbarSearch" type="button"><i class="fas fa-search"></i></button>
            </div>
        </form> -->
        <!-- Navbar-->
        <ul class="navbar-nav ms-auto ms-md-0 me-5 me-lg-5">
            <!-- Notification Bell -->
            <?php
            $user = $_SESSION['userid'];
            $isread = 0;
            $sql = "SELECT NotifID FROM notifications WHERE IsRead = ? AND userid = ? AND (SendAt <= NOW() OR SendAt IS NULL)";
            $stmt = mysqli_prepare($conn, $sql);
            mysqli_stmt_bind_param($stmt, 'ii', $isread, $user);
            mysqli_stmt_execute($stmt);
            $result = mysqli_stmt_get_result($stmt);
            $unreadcount = mysqli_num_rows($result);
            ?>
            <li class="nav-item dropdown">
                <a class="nav-link dropdown-toggle" href="#" id="notificationsDropdown" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                    <span class="badge rounded-pill bg-danger"><?php echo htmlentities($unreadcount); ?></span>
                    <span class="fa-solid fa-bell" style="font-size:18px;"></span>
                </a>
                <ul class="dropdown-menu dropdown-menu-end dropdown-menu-sm-center" aria-labelledby="notificationsDropdown">
                    <li>
                        <h6 class="dropdown-header">Notifications</h6>
                    </li>
                    <?php
                    $user = $_SESSION['userid'];
                    $is_read = 0;
                    $sql = "SELECT NotifID, NotifSubject, NotifText FROM notifications WHERE UserID = ? AND IsRead = ? AND (SendAt <= NOW() OR SendAt IS NULL)";
                    $stmt = mysqli_prepare($conn, $sql);
                    mysqli_stmt_bind_param($stmt, 'ii', $user, $is_read);
                    mysqli_stmt_execute($stmt);
                    $result = mysqli_stmt_get_result($stmt);

                    if (mysqli_num_rows($result) > 0) {
                        while ($row = mysqli_fetch_assoc($result)) {
                    ?>
                            <li>
                                <a class="dropdown-item" href="#" data-bs-toggle="modal" data-bs-target="#notificationModal" data-id="<?php echo htmlentities($row['NotifID']); ?>">
                                    <div class="notification">
                                        <div class="notification-icon">
                                            <i class="fa-solid fa-bell"></i>
                                        </div>
                                        <div class="notification-text">
                                            <p><b><?php echo htmlentities($row['NotifSubject']); ?></b></p>
                                        </div>
                                    </div>
                                </a>
                            </li>
                    <?php
                        }
                    } else {
                        echo "<li><a class='dropdown-item' href='#'>No new notifications</a></li>";
                    }

                    mysqli_stmt_close($stmt);
                    ?>
                </ul>
            </li>

            <li class="nav-item dropdown">
                <a class="nav-link dropdown-toggle" id="navbarDropdown" href="#" role="button" data-bs-toggle="dropdown" aria-expanded="false"><i class="fas fa-user fa-fw"></i></a>
                <ul class="dropdown-menu dropdown-menu-end" aria-labelledby="navbarDropdown">
                    <li><a class="dropdown-item" href="edit-profile?id=<?php echo $_SESSION['userid']; ?>">Profile Settings</a></li>
                    <li><a class="dropdown-item" href="#">Activity Log</a></li>
                    <li>
                        <hr class="dropdown-divider" />
                    </li>
                    <li><a class="dropdown-item" href="switch-user">Switch User</a></li>
                    <li><a class="dropdown-item" href="logout">Logout</a></li>
                </ul>
            </li>
        </ul>
    </nav>