<?php
include 'dbconn.php';

// Read JSON data from the request body
$data = json_decode(file_get_contents('php://input'), true);

if (isset($_POST['token'])) {
    $token = $_POST['token'];

    $stmt = $conn->prepare("INSERT INTO push_tokens (Token) VALUES (?)");
    $stmt->bind_param("s", $token);

    if ($stmt->execute()) {
        echo "Token saved successfully";
    } else {
        echo "Error saving token: " . $stmt->error;
    }
}
