<?php

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\SMTP;
use PHPMailer\PHPMailer\Exception;

require 'dbconn.php';
require 'vendor/autoload.php';

$dotenv = Dotenv\Dotenv::createImmutable(__DIR__ . '/../');
$dotenv->load();

function defMail($recepient, $subject, $message)
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
    } catch (Exception $e) {
        echo "mail not sent";
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
        <!-- Include Bootstrap CSS -->
        <link
        rel="stylesheet"
        href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css"
        />
    </head>

    <body>
        <div
        class="container"
        style="margin: 0 auto; max-width: 580px; padding: 10px"
        >
        <div class="card" style="width: 100%">
            <div class="card-body text-center">
            <h1 class="h2">Password Reset</h1>
            <p class="mb-3">Dear ' . $fname . '</p>
            <p class="mb-3">
                We have received your password reset request. Click on the password
                reset button below or paste the provided link on your browser
            </p>
            <a href="password-reset?token=' . $token . '" class="btn btn-primary">Reset Pass</a>
            <p class="mb-3">link</p>
            <p class="mb-3">Thank you for choosing InLaw.</p>
            </div>
        </div>

        <div class="footer text-center mt-3">
            <div class="content-block">
            <p class="text-muted" style="font-size: 12px">Inlaw-Legal</p>
            </div>
            <div class="content-block powered-by">
            <p class="text-muted" style="font-size: 12px">
                Powered by
                <a href="https://inlaw-legal.tech" class="text-muted">InLaw</a>.
            </p>
            </div>
        </div>
        </div>
    </body>
    </html>
    ';
    return $message;
}
