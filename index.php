<?php include 'php/header.php'; ?>

<?php include 'notifications.php'; ?>

<div id="layoutSidenav">
    <?php include 'php/sidebar.php'; ?>
    <div id="layoutSidenav_content">
        <main>
            <div class="container-fluid px-4">
                <h1 class="mt-4">
                    <?php
                    if ($_SESSION['user_type'] == 'client') {
                        echo "Welcome to The Client Portal";
                    } else {
                        echo "Welcome Back, " . $_SESSION['fname'];
                    }
                    ?>
                </h1>
                <ol class="breadcrumb mb-4">
                    <li class="breadcrumb-item active">
                        <?php
                        if ($_SESSION['user_type'] == 'client') {
                            echo "Logged in as " . $_SESSION['fname'] . ' ' . $_SESSION['lname'];
                        } else {
                            echo "Let's increase your productivity today";
                        }
                        ?>
                    </li>
                </ol>

                <div class="row">
                    <div class="col-xl-3 col-md-6">
                        <div class="card bg-secondary text-white mb-4">
                            <div class="card-body">Total Registered Clients:
                                <?php
                                $firm = $_SESSION['fid'];
                                $res = mysqli_query($conn, "select * from clients where firmid = $firm");
                                $count = mysqli_num_rows($res);
                                echo $count;
                                ?></div>
                            <div class="card-footer d-flex align-items-center justify-content-between">
                                <a class="small text-white stretched-link" href="clients">View All Clients</a>
                                <div class="small text-white"><i class="fas fa-angle-right"></i></div>
                            </div>
                        </div>
                    </div>
                    <div class="col-xl-3 col-md-6">
                        <div class="card bg-secondary text-white mb-4">
                            <div class="card-body">Total Courts:
                                <?php
                                $firm = $_SESSION['fid'];
                                $res = mysqli_query($conn, "select * from courts where firmid=$firm");
                                $count = mysqli_num_rows($res);
                                echo $count;
                                ?>
                            </div>
                            <div class="card-footer d-flex align-items-center justify-content-between">
                                <a class="small text-white stretched-link" href="courts">Add New Court</a>
                                <div class="small text-white"><i class="fas fa-angle-right"></i></div>
                            </div>
                        </div>
                    </div>
                    <div class="col-xl-3 col-md-6">
                        <div class="card bg-secondary text-white mb-4">
                            <div class="card-body">Total Active Cases:
                                <?php
                                $firm = $_SESSION['fid'];
                                $res = mysqli_query($conn, "select * from cases where firmid = $firm and casestatus='open'");
                                $count = mysqli_num_rows($res);
                                echo $count;
                                ?>
                            </div>
                            <div class="card-footer d-flex align-items-center justify-content-between">
                                <a class="small text-white stretched-link" href="cases?status=1">View Active Cases</a>
                                <div class="small text-white"><i class="fas fa-angle-right"></i></div>
                            </div>
                        </div>
                    </div>
                    <div class="col-xl-3 col-md-6">
                        <div class="card bg-secondary text-white mb-4">
                            <div class="card-body">Total Closed Cases:
                                <?php
                                $firm = $_SESSION['fid'];
                                $res = mysqli_query($conn, "select * from cases where firmid = $firm and casestatus='closed'");
                                $count = mysqli_num_rows($res);
                                echo $count;
                                ?></div>
                            <div class="card-footer d-flex align-items-center justify-content-between">
                                <a class="small text-white stretched-link" href="cases?status=3">View Closed Cases</a>
                                <div class="small text-white"><i class="fas fa-angle-right"></i></div>
                            </div>
                        </div>
                    </div>
                </div>
                <?php include 'php/courts-table.php'; ?>
            </div>
        </main>

        <script type="module">
            // Import the functions you need from the Firebase SDK
            import {
                initializeApp
            } from "https://www.gstatic.com/firebasejs/10.0.0/firebase-app.js";
            import {
                getMessaging,
                getToken,
                onMessage
            } from "https://www.gstatic.com/firebasejs/10.0.0/firebase-messaging.js";

            // Your web app's Firebase configuration
            const firebaseConfig = {
                apiKey: "AIzaSyCO38M7kDoh15hYUaCdkj_SmlN5zEJzkqY",
                authDomain: "inlaw-1a86b.firebaseapp.com",
                projectId: "inlaw-1a86b",
                storageBucket: "inlaw-1a86b.firebasestorage.app",
                messagingSenderId: "258998046435",
                appId: "1:258998046435:web:75d11b3ef48d0ef8907fd6",
                measurementId: "G-MYC012N43D",
                serviceWorker: false // Disable Firebase's default service worker
            };

            // Initialize Firebase
            const app = initializeApp(firebaseConfig);

            // Initialize Firebase Messaging
            const messaging = getMessaging(app);

            // Register the service worker with the full ngrok URL
            if ('serviceWorker' in navigator) {
                navigator.serviceWorker.register('/law/firebase-messaging-sw.js', {
                        scope: '/law/' // Assuming your service worker is in the law sub-directory
                    })
                    .then(function(registration) {
                        console.log('Service Worker registered with scope:', registration.scope);
                    })
                    .catch(function(error) {
                        console.error('Service Worker registration failed:', error);
                    });
            }

            // Request permission for notifications
            Notification.requestPermission()
                .then(permission => {
                    if (permission === 'granted') {
                        // Get the FCM token
                        return getToken(messaging, {
                            vapidKey: 'BMHHZetJR_qKNYaDQL3AENwakb1HsI0jMUQuRvKF2J2GFx4N4GEt_oxcZ2-xbkiPffWfc6LKCH0Uj-oU8AzjogY'
                        });
                    } else {
                        throw new Error('Notification permission denied');
                    }
                })
                .then((token) => {
                    console.log('Token received: ', token);

                    // Send this token to your server (e.g., PHP) to save in the database
                    fetch('php/save-token.php', {
                            method: 'POST',
                            body: JSON.stringify({
                                token: token
                            }), // Send token as JSON
                            headers: {
                                'Content-Type': 'application/json' // Indicate that the body is JSON
                            }
                        })
                        .then(response => response.json())
                        .then(data => console.log('Token saved:', data))
                        .catch(error => console.log('Error saving token:', error));
                })
                .catch((err) => {
                    console.error('Permission denied or error: ', err);
                });

            // Optional: Handle foreground messages (when the app is open)
            onMessage(messaging, (payload) => {
                console.log('Message received. ', payload);
                // Customize the notification behavior when the app is in the foreground
            });
        </script>
        <?php include 'php/footer.php'; ?>