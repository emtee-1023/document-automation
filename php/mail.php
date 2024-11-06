<?php

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\SMTP;
use PHPMailer\PHPMailer\Exception;

require __DIR__ . DIRECTORY_SEPARATOR . 'dbconn.php';
require __DIR__ . '/../vendor/autoload.php';

$dotenv = Dotenv\Dotenv::createImmutable(__DIR__ . '/../');
$dotenv->load();

function noReplyMail($recepient, $subject, $message)
{
    //Create an instance; passing `true` enables exceptions
    $mail = new PHPMailer(true);

    try {
        //Server settings
        //$mail->SMTPDebug = SMTP::DEBUG_SERVER;                      //Enable verbose debug output
        $mail->isSMTP();                                            //Send using SMTP
        $mail->Host       = $_ENV['MAIL_HOST'];                     //Set the SMTP server to send through
        $mail->SMTPAuth   = true;                                   //Enable SMTP authentication
        $mail->Username   = $_ENV['NOREPLY_USER'];                     //SMTP username
        $mail->Password   = $_ENV['EMAIL_PASS'];                               //SMTP password
        $mail->SMTPSecure = PHPMailer::ENCRYPTION_SMTPS;            //Enable implicit TLS encryption
        $mail->Port       = 465;                                    //TCP port to connect to; use 587 if you have set `SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS`

        //Recipients
        $mail->setFrom($_ENV['NOREPLY_USER'], 'InLaw noreply');
        $mail->addAddress($recepient, 'user');     //Add a recipient


        //Attachments
        //$mail->addAttachment('/var/tmp/file.tar.gz');         //Add attachments
        //$mail->addAttachment('/tmp/image.jpg', 'new.jpg');    //Optional name

        //Content
        $mail->isHTML(true);                                  //Set email format to HTML
        $mail->Subject = $subject;
        $mail->Body    = $message;
        $mail->AltBody = $message;

        $mail->send();
        return true;
    } catch (Exception $e) {
        return false;
    }
}

function passReset($fname, $token)
{
    $message = '
    <!DOCTYPE html>
    <html lang="en">
    <head>
        <meta charset="utf-8" />
        <meta name="viewport" content="width=device-width, initial-scale=1.0" />
        <meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
        <title>InLaw Password Reset</title>
        <style>
            body {
                font-family: Arial, sans-serif;
                background-color: #f8f9fa;
                margin: 0;
                padding: 0;
            }
            .container {
                margin: 0 auto;
                max-width: 580px;
                padding: 10px;
            }
            .card {
                background-color: #ffffff;
                border: 1px solid #dee2e6;
                border-radius: 5px;
                box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
                padding: 20px;
                width: 100%;
                box-sizing: border-box;
            }
            .card-body {
                text-align: center;
            }
            .h2 {
                font-size: 24px;
                margin-bottom: 10px;
            }
            .mb-3 {
                margin-bottom: 15px;
            }
            .btn {
                display: inline-block;
                background-color: #007bff;
                color: #ffffff;
                padding: 10px 20px;
                text-decoration: none;
                border-radius: 5px;
                font-size: 16px;
            }
            .btn:hover {
                background-color: #0056b3;
                color: #ffffff;
            }
            .footer {
                text-align: center;
                margin-top: 20px;
            }
            .content-block {
                margin-bottom: 5px;
            }
            .text-muted {
                color: #6c757d;
                font-size: 12px;
            }
            .powered-by a {
                color: #6c757d;
                text-decoration: none;
            }
        </style>
    </head>
    <body>
        <div class="container">
            <div class="card">
                <div class="card-body">
                    <h1 class="h2">Password Reset</h1>
                    <p class="mb-3">Dear ' . $fname . '</p>
                    <p class="mb-3">
                        We have received your password reset request. Click on the password
                        reset button below or paste the provided link on your browser
                    </p>
                    <a href="https://app.inlaw-legal.tech/reset-pass?token=' . $token . '" class="btn">Reset Pass</a>
                    <p class="mb-3">https://app.inlaw-legal.tech/reset-pass?token=' . $token . '</p>
                    <p class="mb-3">Thank you for choosing InLaw.</p>
                </div>
            </div>
            <div class="footer">
                <div class="content-block">
                    <p class="text-muted">Inlaw-Legal</p>
                </div>
                <div class="content-block powered-by">
                    <p class="text-muted">
                        Powered by
                        <a href="https://inlaw-legal.tech">InLaw</a>.
                    </p>
                </div>
            </div>
        </div>
    </body>
    </html>

    ';
    return $message;
}

function mailAddedDoc($clientName, $firmName, $caseName)
{
    $message = '
    <!DOCTYPE html>
    <html lang="en">
    <head>
        <meta charset="utf-8" />
        <meta name="viewport" content="width=device-width, initial-scale=1.0" />
        <meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
        <title>InLaw Doc Notification</title>
        <style>
            body {
                font-family: Arial, sans-serif;
                background-color: #f8f9fa;
                margin: 0;
                padding: 0;
            }
            .container {
                margin: 0 auto;
                max-width: 580px;
                padding: 10px;
            }
            .card {
                background-color: #ffffff;
                border: 1px solid #dee2e6;
                border-radius: 5px;
                box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
                padding: 20px;
                width: 100%;
                box-sizing: border-box;
            }
            .card-body {
                text-align: center;
            }
            .h2 {
                font-size: 24px;
                margin-bottom: 10px;
            }
            .mb-3 {
                margin-bottom: 15px;
            }
            .btn {
                display: inline-block;
                background-color: #007bff;
                color: #ffffff;
                padding: 10px 20px;
                text-decoration: none;
                border-radius: 5px;
                font-size: 16px;
            }
            .btn:hover {
                background-color: #0056b3;
                color: #ffffff;
            }
            .footer {
                text-align: center;
                margin-top: 20px;
            }
            .content-block {
                margin-bottom: 5px;
            }
            .text-muted {
                color: #6c757d;
                font-size: 12px;
            }
            .powered-by a {
                color: #6c757d;
                text-decoration: none;
            }
        </style>
    </head>
    <body>
        <div class="container">
            <div class="card">
                <div class="card-body">
                    <h1 class="h2">New Document Upload</h1>
                    <p class="mb-3">Dear ' . $clientName . '</p>
                    <p class="mb-3">
                        A new Document has been uploaded by the firm ' . $firmName . ' to your case titled ' . $caseName . '.
                    </p>
                    <p class="mb-3">
                        Log back in to your client portal by following the link below to check it out
                    </p>
                    <a href="https://app.inlaw-legal.tech/client/cases" class="btn">Go to InLaw</a>
                    <p class="mb-3">Thank you for choosing InLaw.</p>
                </div>
            </div>
            <div class="footer">
                <div class="content-block">
                    <p class="text-muted">Inlaw-Legal</p>
                </div>
                <div class="content-block powered-by">
                    <p class="text-muted">
                        Powered by
                        <a href="https://inlaw-legal.tech">InLaw</a>.
                    </p>
                </div>
            </div>
        </div>
    </body>
    </html>

    ';
    return $message;
}
