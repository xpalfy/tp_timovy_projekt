<?php
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require_once '../vendor/autoload.php'; // PHPMailer
require_once '../config.php';         // Your app config

header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(['success' => false, 'error' => 'Invalid request method.']);
    exit();
}

$input = json_decode(file_get_contents("php://input"), true);

if (!isset($input['question']) || trim($input['question']) === '') {
    http_response_code(400);
    echo json_encode(['success' => false, 'error' => 'Question is required.']);
    exit();
}

$question = trim($input['question']);

// Set up PHPMailer
$mail = new PHPMailer(true);

try {
    $mail->isSMTP();
    $mail->Host = 'smtp.gmail.com';
    $mail->SMTPAuth = true;
    $mail->Username = 'ptimovy@gmail.com';
    $mail->Password = 'cfxc llnb lspi sevg'; 
    $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
    $mail->Port = 587;

    $mail->setFrom('ptimovy@gmail.com', 'HandScript');
    $mail->addAddress('ptimovy@gmail.com');

    $mail->isHTML(false);
    $mail->Subject = 'New FAQ Question Submitted';
    $mail->Body    = "A new question has been submitted via the FAQ form:\n\n" . $question;

    $mail->send();
    echo json_encode(['success' => true, 'message' => 'Question emailed successfully.']);
} catch (Exception $e) {
    error_log("PHPMailer Error: {$mail->ErrorInfo}");
    http_response_code(500);
    echo json_encode(['success' => false, 'error' => 'Failed to send email.']);
}
