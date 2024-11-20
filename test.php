<?php
require 'vendor/autoload.php';
$dotenv = Dotenv\Dotenv::createImmutable(__DIR__);
$dotenv->load();

// Your SendGrid API Key
$apiKey = $_ENV['SENDGRID_KEY'];

// Create a new SendGrid client
$sendgrid = new \SendGrid($apiKey);

// Set the desired time in Nairobi (EAT) timezone
$desiredTime = '2024-11-20 20:04:00'; // 8:00 PM Nairobi time (EAT)

// Create a DateTime object for Nairobi time
$nairobiTime = new DateTime($desiredTime, new DateTimeZone('Africa/Nairobi'));

// Get the Unix timestamp for SendGrid (Nairobi time directly)
$sendAt = $nairobiTime->getTimestamp();

// Create the email content
$email = new \SendGrid\Mail\Mail();
$email->setFrom("info@inlaw-legal.tech", "Test Mail"); // Sender email and name
$email->setSubject("Test Email from SendGrid"); // Email subject
$email->addTo("marktalamson@gmail.com", "emtee"); // Recipient email and name
$email->addContent("text/html", "<strong>This is a test email sent from SendGrid!</strong>"); // Email body in HTML
$email->setSendAt($sendAt);

// Send the email
try {
    $response = $sendgrid->send($email);
    echo "Email sent successfully. Response: " . $response->statusCode() . "\n";
    echo $response->body() . "\n";
    echo $response->headers() . "\n";
} catch (Exception $e) {
    echo 'Error: ' . $e->getMessage() . "\n";
}
