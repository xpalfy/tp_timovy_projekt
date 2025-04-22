<?php
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require_once '../vendor/autoload.php';

function sendVerificationEmail($toEmail, $code) {
    $mail = new PHPMailer(true);
    try {
        $mail->isSMTP();
        $mail->Host = 'smtp.gmail.com';
        $mail->SMTPAuth = true;
        $mail->Username = 'ptimovy@gmail.com';
        $mail->Password = 'cfxc llnb lspi sevg';
        $mail->SMTPSecure = 'tls';
        $mail->Port = 587;

        $mail->setFrom('ptimovy@gmail.com', 'HandScript');
        $mail->addAddress($toEmail);
        $mail->isHTML(false);
        $mail->Subject = 'Email Verification';
        $mail->Body    = "Your verification code is: $code";

        $mail->send();
        return true;
    } catch (Exception $e) {
        error_log("Mailer Error: " . $mail->ErrorInfo);
        return false;
    }
}
