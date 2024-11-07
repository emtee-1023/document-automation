<footer class="py-3  mt-auto">
    <div class="container-fluid px-3">

    </div>
</footer>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/js/bootstrap.bundle.min.js" crossorigin="anonymous"></script>
<script src="js/scripts.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/Chart.js/2.8.0/Chart.min.js" crossorigin="anonymous"></script>
<script src="assets/demo/chart-area-demo.js"></script>
<script src="assets/demo/chart-bar-demo.js"></script>
<script src="https://cdn.jsdelivr.net/npm/simple-datatables@7.1.2/dist/umd/simple-datatables.min.js" crossorigin="anonymous"></script>
<script src="js/datatables-simple-demo.js"></script>

<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.1.0/jquery.min.js"></script>

<script>
    $(document).ready(function() {
        // Show notification details in modal
        $('#notificationModal').on('show.bs.modal', function(event) {
            var button = $(event.relatedTarget); // Button that triggered the modal
            var notifId = button.data('id'); // Extract info from data-* attributes

            // AJAX request to fetch notification details
            $.ajax({
                url: 'php/notification-details', // PHP script to fetch details
                type: 'GET',
                dataType: 'json',
                data: {
                    id: notifId
                },
                success: function(response) {
                    $('#notifSubject').text(response.subject || 'No subject');
                    $('#notifText').text(response.text || 'No text');

                    // Update the notification as read
                    $.ajax({
                        url: 'php/notification-details', // PHP script to update the notification status
                        type: 'GET',
                        data: {
                            notifid: notifId
                        }
                    });
                },
                error: function(xhr, status, error) {
                    console.error('Error fetching notification details:', status, error);
                }
            });
        });

        // Refresh the page when the modal is closed
        $('#notificationModal').on('hidden.bs.modal', function() {
            // Reload the page
            location.reload();
        });
    });
</script>

<script type="module">
    // Import the functions you need from the Firebase SDK
    import {
        initializeApp
    } from "https://www.gstatic.com/firebasejs/9.0.0/firebase-app.js";
    import {
        getMessaging,
        getToken,
        onMessage
    } from "https://www.gstatic.com/firebasejs/9.0.0/firebase-messaging.js";

    // Your web app's Firebase configuration
    const firebaseConfig = {
        apiKey: "AIzaSyCO38M7kDoh15hYUaCdkj_SmlN5zEJzkqY",
        authDomain: "inlaw-1a86b.firebaseapp.com",
        projectId: "inlaw-1a86b",
        storageBucket: "inlaw-1a86b.firebasestorage.app",
        messagingSenderId: "258998046435",
        appId: "1:258998046435:web:75d11b3ef48d0ef8907fd6",
        measurementId: "G-MYC012N43D"
    };

    // Initialize Firebase
    const app = initializeApp(firebaseConfig);

    // Initialize Firebase Messaging
    const messaging = getMessaging(app);

    // Register the service worker with the full ngrok URL
    if ('serviceWorker' in navigator) {
        navigator.serviceWorker.register('/law/firebase-messaging-sw.js', {
                scope: '/law/'
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



</body>

</html>