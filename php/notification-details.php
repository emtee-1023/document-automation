<?php
include 'dbconn.php';
session_start();

header('Content-Type: application/json');

$response = array();

if (isset($_GET['id'])) {
    // Fetch notification details
    $lid = intval($_GET['id']);
    $sql_select = "SELECT NotifSubject, NotifText FROM notifications WHERE NotifID = ? AND (SendAt <= NOW() OR SendAt IS NULL)";
    $stmt_select = $conn->prepare($sql_select);
    $stmt_select->bind_param('i', $lid);
    $stmt_select->execute();
    $stmt_select->bind_result($comment_subject, $comment_text);

    if ($stmt_select->fetch()) {
        $response['subject'] = $comment_subject;
        $response['text'] = $comment_text;
    }

    $stmt_select->close();

    echo json_encode($response);
    exit;
}

if (isset($_GET['notifid'])) {
    // Mark notification as read
    $isread = 1;
    $did = intval($_GET['notifid']);
    $sql = "UPDATE notifications SET IsRead=? WHERE NotifID=?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param('ii', $isread, $did);
    $stmt->execute();
    $stmt->close();

    exit;
}

if (isset($_GET['action']) && $_GET['action'] === 'fetch_notifications') {
    // Fetch notifications list
    $user = $_SESSION['userid'];
    $sql = "SELECT
                NotifID,
                NotifSubject,
                NotifText 
            FROM 
                notifications 
            WHERE 
                UserID = ? 
            AND 
                IsRead = 0 
            AND 
                (SendAt <= NOW() OR SendAt IS NULL)";

    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $user); // Bind the UserID parameter
    $stmt->execute();
    $results = $stmt->get_result();

    if ($results->num_rows > 0) {
        while ($row = $results->fetch_assoc()) {
            echo '<li>';
            echo '<a href="#" data-bs-toggle="modal" data-bs-target="#notificationModal" data-id="' . htmlentities($row['NotifID']) . '">';
            echo '<div class="notification">';
            echo '<div class="notification-icon">';
            echo '<i class="fa-solid fa-bell"></i>';
            echo '</div>';
            echo '<div class="notification-text">';
            echo '<p><b>' . htmlentities($row['NotifSubject']) . '</b></p>';
            echo '<p><b>' . htmlentities($row['NotifText']) . '</b></p>';
            echo '</div>';
            echo '</div>';
            echo '</a>';
            echo '</li>';
        }
    } else {
        echo '<li><a href="#">No new notifications</a></li>';
    }

    $stmt->close();
    exit;
}

if (isset($_GET['action']) && $_GET['action'] === 'get_unread_count') {
    // Fetch unread notifications count
    $user = $_SESSION['userid'];
    $sql = "SELECT 
                COUNT(*) as unread_count 
            FROM
                notifications 
            WHERE 
                UserID = ? 
            AND 
                IsRead = 0 
            AND 
                (SendAt <= NOW() OR SendAt IS NULL)";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $user); // Bind the UserID parameter
    $stmt->execute();
    $stmt->bind_result($unread_count);
    $stmt->fetch();
    $stmt->close();

    echo $unread_count;
    exit;
}

echo json_encode(['error' => 'Invalid request.']);

$apiKey = 'BMHHZetJR_qKNYaDQL3AENwakb1HsI0jMUQuRvKF2J2GFx4N4GEt_oxcZ2-xbkiPffWfc6LKCH0Uj-oU8AzjogY'; // Your FCM Server Key
$url = 'https://fcm.googleapis.com/fcm/send';

$data = [
    'to' => 'USER_PUSH_TOKEN', // Replace with the token you want to send to
    'notification' => [
        'title' => 'Test Notification',
        'body' => 'This is a test push notification.',
        'icon' => 'icon_url'
    ]
];

$headers = [
    'Authorization: key=' . $apiKey,
    'Content-Type: application/json'
];

$ch = curl_init();
curl_setopt($ch, CURLOPT_URL, $url);
curl_setopt($ch, CURLOPT_POST, true);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));

$response = curl_exec($ch);
curl_close($ch);

echo $response;
