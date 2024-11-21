<?php

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\SMTP;
use PHPMailer\PHPMailer\Exception;
use SendGrid\Mail\Mail;

require __DIR__ . DIRECTORY_SEPARATOR . 'dbconn.php';
require __DIR__ . '/../vendor/autoload.php';

$dotenv = Dotenv\Dotenv::createImmutable(__DIR__ . '/../');
$dotenv->load();

date_default_timezone_set('Africa/Nairobi');

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

function scheduledMail($recepient, $subject, $message, $scheduleTime)
{
    $apiKey = 'your-sendgrid-api-key';
    $email = new \SendGrid\Mail\Mail(); // Adjusting to your existing namespace

    $email->setFrom("info@inlaw-legal.tech", "InLaw (Reminders)");
    $email->setSubject($subject);
    $email->addTo($recepient);
    $email->addContent("text/html", $message);

    // Add scheduling header if a time is provided
    if ($scheduleTime) {
        $sendAt = strtotime($scheduleTime); // Convert to Unix timestamp
        $email->setSendAt($sendAt);
    }

    $sendgrid = new \SendGrid($_ENV['SENDGRID_KEY']);

    try {
        $response = $sendgrid->send($email);

        // Check if the status code indicates success
        if ($response->statusCode() == 202) {
            return true; // Email successfully sent to SendGrid for processing
        }

        return false; // Email not successfully queued
    } catch (Exception $e) {
        return false; // An error occurred (No printing in production)
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
    </head>
    <body style="font-family: Arial, sans-serif; background-color: #f8f9fa; margin: 0; padding: 0;">
        <div style="margin: 0 auto; max-width: 580px; padding: 10px;">
            <div style="background-color: #ffffff; border: 1px solid #dee2e6; border-radius: 5px; box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1); padding: 20px; width: 100%; box-sizing: border-box;">
                <div style="text-align: start;">
                    <h1 style="font-size: 24px; text-align: center; margin-bottom: 10px;">Password Reset</h1>
                    <p style="margin-bottom: 15px;">Dear ' . $fname . '</p>
                    <p style="margin-bottom: 15px;">
                        We have received your password reset request. Click on the password
                        reset button below or paste the provided link on your browser.
                    </p>
                    <a
                        href="https://app.inlaw-legal.tech/reset-pass?token=' . $token . '"
                        style="display: inline-block; background-color: #d7d8da; color: #574c4c; padding: 10px 20px; text-decoration: none; border-radius: 5px; font-size: 16px; text-align: center; max-width: 100px; margin: 0 auto;"
                        target="_blank"
                    >Reset Pass</a>
                    <p style="margin-bottom: 15px;">
                        https://app.inlaw-legal.tech/reset-pass?token=' . $token . '
                    </p>
                    <p style="margin-bottom: 15px;">Thank you for choosing InLaw.</p>
                </div>
            </div>
            <div style="text-align: center; margin-top: 20px;">
                <div style="margin-bottom: 5px;">
                    <p style="color: #6c757d; font-size: 12px;">Inlaw-Legal</p>
                </div>
                <div>
                    <p style="color: #6c757d; font-size: 12px;">
                        Powered by
                        <a href="https://inlaw-legal.tech" style="color: #6c757d; text-decoration: none;">InLaw</a>.
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
    </head>
    <body style="font-family: Arial, sans-serif; background-color: #f8f9fa; margin: 0; padding: 0;">
        <div style="margin: 0 auto; max-width: 580px; padding: 10px;">
            <div style="background-color: #ffffff; border: 1px solid #dee2e6; border-radius: 5px; box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1); padding: 20px; width: 100%; box-sizing: border-box;">
                <div style="text-align: start;">
                    <h1 style="font-size: 24px; text-align: center; margin-bottom: 10px;">New Document Upload</h1>
                    <p style="margin-bottom: 15px;">Dear ' . $clientName . '</p>
                    <p style="margin-bottom: 15px;">
                        A new document has been uploaded by the firm ' . $firmName . ' to
                        your case titled ' . $caseName . '.
                    </p>
                    <p style="margin-bottom: 15px;">
                        Log back in to your client portal by following the link below to
                        check it out.
                    </p>
                    <a href="https://app.inlaw-legal.tech/client/case-docs"
                       style="display: inline-block; background-color: #d7d8da; color: #574c4c; padding: 10px 20px; text-decoration: none; border-radius: 5px; font-size: 16px; text-align: center; max-width: 100px; margin: 0 auto;"
                       target="_blank">Go to InLaw</a>
                    <p style="margin-bottom: 15px;">Thank you for choosing InLaw.</p>
                </div>
            </div>
            <div style="text-align: center; margin-top: 20px;">
                <div style="margin-bottom: 5px;">
                    <p style="color: #6c757d; font-size: 12px;">Inlaw-Legal</p>
                </div>
                <div>
                    <p style="color: #6c757d; font-size: 12px;">
                        Powered by
                        <a href="https://inlaw-legal.tech" style="color: #6c757d; text-decoration: none;">InLaw</a>.
                    </p>
                </div>
            </div>
        </div>
    </body>
    </html>
    ';
    return $message;
}

function mailClientAddedRem($clientName, $court, $caseNum, $caseName, $nextDate, $notes, $onlineLink)
{
    $message = '
    <!DOCTYPE html>
    <html lang="en">
    <head>
        <meta charset="utf-8" />
        <meta name="viewport" content="width=device-width, initial-scale=1.0" />
        <meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
        <title>InLaw Doc Notification</title>
    </head>
    <body style="font-family: Arial, sans-serif; background-color: #f8f9fa; margin: 0; padding: 0;">
        <div style="margin: 0 auto; max-width: 580px; padding: 10px;">
            <div style="background-color: #ffffff; border: 1px solid #dee2e6; border-radius: 5px; box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1); padding: 20px; width: 100%; box-sizing: border-box;">
                <div style="text-align: start;">
                    <h1 style="font-size: 24px; text-align: center; margin-bottom: 10px;">New Date Scheduled</h1>
                    <p style="margin-bottom: 15px;">Hello ' . $clientName . '</p>
                    <p style="margin-bottom: 15px;">
                       This is to inform you that a new date has been scheduled for ' . $court . ' ' . $caseNum . ' ' . $caseName . '.
                    </p>
                    <p style="margin-bottom: 15px;">
                        Updated Details:
                        <ul style="margin-bottom: 15px;">
                            <li>New Date: ' . $nextDate . '</li>
                            <li>Additional Notes: ' . $notes . '</li>
                            <li>Online Link (where applicable): ' . $onlineLink . '</li>
                        </ul>
                    </p>
                    <p style="margin-bottom: 15px;">
                        Please log in to InLaw to review the details and make any necessary updates to your schedule.
                    </p>
                    <a href="https://app.inlaw-legal.tech/client/reminders" 
                       style="display: inline-block; background-color: #d7d8da; color: #574c4c; padding: 10px 20px; text-decoration: none; border-radius: 5px; font-size: 16px; text-align: center; max-width: 100px; margin: 0 auto;"
                       target="_blank">Go to InLaw</a>
                    <p style="margin-bottom: 15px;">Thank you for choosing InLaw.</p>
                    <p style="margin-bottom: 15px;">Best Regards, <br> The InLaw team.</p>
                </div>
            </div>
            <div style="text-align: center; margin-top: 20px;">
                <div style="margin-bottom: 5px;">
                    <p style="color: #6c757d; font-size: 12px;">Inlaw-Legal</p>
                </div>
                <div>
                    <p style="color: #6c757d; font-size: 12px;">
                        Powered by
                        <a href="https://inlaw-legal.tech" style="color: #6c757d; text-decoration: none;">InLaw</a>.
                    </p>
                </div>
            </div>
        </div>
    </body>
    </html>
    ';
    return $message;
}

function mailClientBringup($clientName, $court, $caseNum, $caseName, $nextDate, $notes, $onlineLink)
{
    $message = '
    <!DOCTYPE html>
    <html lang="en">
    <head>
        <meta charset="utf-8" />
        <meta name="viewport" content="width=device-width, initial-scale=1.0" />
        <meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
        <title>InLaw Doc Notification</title>
    </head>
    <body style="font-family: Arial, sans-serif; background-color: #f8f9fa; margin: 0; padding: 0;">
        <div style="margin: 0 auto; max-width: 580px; padding: 10px;">
            <div style="background-color: #ffffff; border: 1px solid #dee2e6; border-radius: 5px; box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1); padding: 20px; width: 100%; box-sizing: border-box;">
                <div style="text-align: start;">
                    <h1 style="font-size: 24px; text-align: center; margin-bottom: 10px;">REMINDER FOR YOUR MATTER</h1>
                    <p style="margin-bottom: 15px;">Hello ' . $clientName . '</p>
                    <p style="margin-bottom: 15px;">
                       This is to inform you that ' . $court . ' ' . $caseNum . ' ' . $caseName . ' is scheduled for ' . $nextDate . ' .
                    </p>
                    <p style="margin-bottom: 15px;">
                        Matter Details:
                        <ul style="margin-bottom: 15px;">
                            <li>Date & Time: ' . $nextDate . '</li>
                            <li>Additional Notes: ' . $notes . '</li>
                            <li>Online Link (where applicable): ' . $onlineLink . '</li>
                        </ul>
                    </p>
                    <p style="margin-bottom: 15px;">
                        Please log in to InLaw for more information or to review any relevant documents.
                    </p>
                    <a href="https://app.inlaw-legal.tech/client/reminders" 
                       style="display: inline-block; background-color: #d7d8da; color: #574c4c; padding: 10px 20px; text-decoration: none; border-radius: 5px; font-size: 16px; text-align: center; max-width: 100px; margin: 0 auto;"
                       target="_blank">Go to InLaw</a>
                    <p style="margin-bottom: 15px;">Thank you for choosing InLaw.</p>
                    <p style="margin-bottom: 15px;">Best Regards, <br> The InLaw team.</p>
                </div>
            </div>
            <div style="text-align: center; margin-top: 20px;">
                <div style="margin-bottom: 5px;">
                    <p style="color: #6c757d; font-size: 12px;">Inlaw-Legal</p>
                </div>
                <div>
                    <p style="color: #6c757d; font-size: 12px;">
                        Powered by
                        <a href="https://inlaw-legal.tech" style="color: #6c757d; text-decoration: none;">InLaw</a>.
                    </p>
                </div>
            </div>
        </div>
    </body>
    </html>
    ';
    return $message;
}

function mailAdvBringup($advName, $court, $caseNum, $caseName, $nextDate, $notes, $onlineLink)
{
    $message = '
    <!DOCTYPE html>
    <html lang="en">
    <head>
        <meta charset="utf-8" />
        <meta name="viewport" content="width=device-width, initial-scale=1.0" />
        <meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
        <title>InLaw Doc Notification</title>
    </head>
    <body style="font-family: Arial, sans-serif; background-color: #f8f9fa; margin: 0; padding: 0;">
        <div style="margin: 0 auto; max-width: 580px; padding: 10px;">
            <div style="background-color: #ffffff; border: 1px solid #dee2e6; border-radius: 5px; box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1); padding: 20px; width: 100%; box-sizing: border-box;">
                <div style="text-align: start;">
                    <h1 style="font-size: 24px; text-align: center; margin-bottom: 10px;">REMINDER FOR YOUR MATTER</h1>
                    <p style="margin-bottom: 15px;">Hello ' . $advName . '</p>
                    <p style="margin-bottom: 15px;">
                       This is to inform you that ' . $court . ' ' . $caseNum . ' ' . $caseName . ' is scheduled for ' . $nextDate . ' .
                    </p>
                    <p style="margin-bottom: 15px;">
                        Matter Details:
                        <ul style="margin-bottom: 15px;">
                            <li>Date & Time: ' . $nextDate . '</li>
                            <li>Additional Notes: ' . $notes . '</li>
                            <li>Online Link (where applicable): ' . $onlineLink . '</li>
                        </ul>
                    </p>
                    <p style="margin-bottom: 15px;">
                        Please log in to InLaw for more information or to review any relevant documents.
                    </p>
                    <a href="https://app.inlaw-legal.tech/reminders" 
                       style="display: inline-block; background-color: #d7d8da; color: #574c4c; padding: 10px 20px; text-decoration: none; border-radius: 5px; font-size: 16px; text-align: center; max-width: 100px; margin: 0 auto;"
                       target="_blank">Go to InLaw</a>
                    <p style="margin-bottom: 15px;">Thank you for choosing InLaw.</p>
                    <p style="margin-bottom: 15px;">Best Regards, <br> The InLaw team.</p>
                </div>
            </div>
            <div style="text-align: center; margin-top: 20px;">
                <div style="margin-bottom: 5px;">
                    <p style="color: #6c757d; font-size: 12px;">Inlaw-Legal</p>
                </div>
                <div>
                    <p style="color: #6c757d; font-size: 12px;">
                        Powered by
                        <a href="https://inlaw-legal.tech" style="color: #6c757d; text-decoration: none;">InLaw</a>.
                    </p>
                </div>
            </div>
        </div>
    </body>
    </html>
    ';
    return $message;
}

function mailClientRem($clientName, $court, $caseNum, $caseName, $nextDate, $notes, $onlineLink)
{
    $message = '
    <!DOCTYPE html>
    <html lang="en">
    <head>
        <meta charset="utf-8" />
        <meta name="viewport" content="width=device-width, initial-scale=1.0" />
        <meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
        <title>InLaw Doc Notification</title>
    </head>
    <body style="font-family: Arial, sans-serif; background-color: #f8f9fa; margin: 0; padding: 0;">
        <div style="margin: 0 auto; max-width: 580px; padding: 10px;">
            <div style="background-color: #ffffff; border: 1px solid #dee2e6; border-radius: 5px; box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1); padding: 20px; width: 100%; box-sizing: border-box;">
                <div style="text-align: start;">
                    <h1 style="font-size: 24px; text-align: center; margin-bottom: 10px;">Matter Due Today</h1>
                    <p style="margin-bottom: 15px;">Hello ' . $clientName . '</p>
                    <p style="margin-bottom: 15px;">
                       This is to inform you that ' . $court . ' ' . $caseNum . ' ' . $caseName . ' is scheduled for today.
                    </p>
                    <p style="margin-bottom: 15px;">
                        Matter Details:
                        <ul style="margin-bottom: 15px;">
                            <li>Date & Time: ' . $nextDate . '</li>
                            <li>Additional Notes: ' . $notes . '</li>
                            <li>Online Link (where applicable): ' . $onlineLink . '</li>
                        </ul>
                    </p>
                    <p style="margin-bottom: 15px;">
                        Please log in to InLaw for more information or to review any relevant documents.
                    </p>
                    <a href="https://app.inlaw-legal.tech/client/reminders" 
                       style="display: inline-block; background-color: #d7d8da; color: #574c4c; padding: 10px 20px; text-decoration: none; border-radius: 5px; font-size: 16px; text-align: center; max-width: 100px; margin: 0 auto;"
                       target="_blank">Go to InLaw</a>
                    <p style="margin-bottom: 15px;">Thank you for choosing InLaw.</p>
                    <p style="margin-bottom: 15px;">Best Regards, <br> The InLaw team.</p>
                </div>
            </div>
            <div style="text-align: center; margin-top: 20px;">
                <div style="margin-bottom: 5px;">
                    <p style="color: #6c757d; font-size: 12px;">Inlaw-Legal</p>
                </div>
                <div>
                    <p style="color: #6c757d; font-size: 12px;">
                        Powered by
                        <a href="https://inlaw-legal.tech" style="color: #6c757d; text-decoration: none;">InLaw</a>.
                    </p>
                </div>
            </div>
        </div>
    </body>
    </html>
    ';
    return $message;
}

function mailAdvRem($advName, $court, $caseNum, $caseName, $nextDate, $notes, $onlineLink)
{
    $message = '
    <!DOCTYPE html>
    <html lang="en">
    <head>
        <meta charset="utf-8" />
        <meta name="viewport" content="width=device-width, initial-scale=1.0" />
        <meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
        <title>InLaw Doc Notification</title>
    </head>
    <body style="font-family: Arial, sans-serif; background-color: #f8f9fa; margin: 0; padding: 0;">
        <div style="margin: 0 auto; max-width: 580px; padding: 10px;">
            <div style="background-color: #ffffff; border: 1px solid #dee2e6; border-radius: 5px; box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1); padding: 20px; width: 100%; box-sizing: border-box;">
                <div style="text-align: start;">
                    <h1 style="font-size: 24px; text-align: center; margin-bottom: 10px;">Matter Due Today</h1>
                    <p style="margin-bottom: 15px;">Hello ' . $advName . '</p>
                    <p style="margin-bottom: 15px;">
                       This is to inform you that ' . $court . ' ' . $caseNum . ' ' . $caseName . ' is scheduled for today.
                    </p>
                    <p style="margin-bottom: 15px;">
                        Matter Details:
                        <ul style="margin-bottom: 15px;">
                            <li>Date & Time: ' . $nextDate . '</li>
                            <li>Additional Notes: ' . $notes . '</li>
                            <li>Online Link (where applicable): ' . $onlineLink . '</li>
                        </ul>
                    </p>
                    <p style="margin-bottom: 15px;">
                        Please log in to InLaw for more information or to review any relevant documents.
                    </p>
                    <a href="https://app.inlaw-legal.tech/reminders" 
                       style="display: inline-block; background-color: #d7d8da; color: #574c4c; padding: 10px 20px; text-decoration: none; border-radius: 5px; font-size: 16px; text-align: center; max-width: 100px; margin: 0 auto;"
                       target="_blank">Go to InLaw</a>
                    <p style="margin-bottom: 15px;">Thank you for choosing InLaw.</p>
                    <p style="margin-bottom: 15px;">Best Regards, <br> The InLaw team.</p>
                </div>
            </div>
            <div style="text-align: center; margin-top: 20px;">
                <div style="margin-bottom: 5px;">
                    <p style="color: #6c757d; font-size: 12px;">Inlaw-Legal</p>
                </div>
                <div>
                    <p style="color: #6c757d; font-size: 12px;">
                        Powered by
                        <a href="https://inlaw-legal.tech" style="color: #6c757d; text-decoration: none;">InLaw</a>.
                    </p>
                </div>
            </div>
        </div>
    </body>
    </html>
    ';
    return $message;
}

function mailAssignedTask($assignee, $taskName, $assigner, $taskDeadline, $description)
{
    $message = '
    <!DOCTYPE html>
    <html lang="en">
    <head>
        <meta charset="utf-8" />
        <meta name="viewport" content="width=device-width, initial-scale=1.0" />
        <meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
        <title>InLaw Doc Notification</title>
    </head>
    <body style="font-family: Arial, sans-serif; background-color: #f8f9fa; margin: 0; padding: 0;">
        <div style="margin: 0 auto; max-width: 580px; padding: 10px;">
            <div style="background-color: #ffffff; border: 1px solid #dee2e6; border-radius: 5px; box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1); padding: 20px; width: 100%; box-sizing: border-box;">
                <div style="text-align: start;">
                    <h1 style="font-size: 24px; text-align: center; margin-bottom: 10px;">NEW TASK ASSIGNMENT</h1>
                    <p style="margin-bottom: 15px;">Hello ' . $assignee . '</p>
                    <p style="margin-bottom: 15px;">
                       You have been assigned a new task ' . $taskName . '.
                    </p>
                    <p style="margin-bottom: 15px;">
                        Task Details:
                        <ul style="margin-bottom: 15px;">
                            <li>Task Name: ' . $taskName . '</li>
                            <li>Assigned By: ' . $assigner . '</li>
                            <li>Due Date: ' . $taskDeadline . '</li>
                            <li>Description: ' . $description . '</li>
                        </ul>
                    </p>
                    <p style="margin-bottom: 15px;">
                        Please log in to InLaw to review the task and get started.
                    </p>
                    <a href="https://app.inlaw-legal.tech/client/tasks" 
                       style="display: inline-block; background-color: #d7d8da; color: #574c4c; padding: 10px 20px; text-decoration: none; border-radius: 5px; font-size: 16px; text-align: center; max-width: 100px; margin: 0 auto;"
                       target="_blank">Go to InLaw</a>
                    <p style="margin-bottom: 15px;">Thank you for choosing InLaw.</p>
                    <p style="margin-bottom: 15px;">Best Regards, <br> The InLaw team.</p>
                </div>
            </div>
            <div style="text-align: center; margin-top: 20px;">
                <div style="margin-bottom: 5px;">
                    <p style="color: #6c757d; font-size: 12px;">Inlaw-Legal</p>
                </div>
                <div>
                    <p style="color: #6c757d; font-size: 12px;">
                        Powered by
                        <a href="https://inlaw-legal.tech" style="color: #6c757d; text-decoration: none;">InLaw</a>.
                    </p>
                </div>
            </div>
        </div>
    </body>
    </html>
    ';
    return $message;
}
