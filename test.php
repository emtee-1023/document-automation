<?php
include 'php/mail.php';
$receipient = "marktalamson@gmail.com";
$subject = "subject";
$message = "message";

noReplyMail($receipient, $subject, $message);
