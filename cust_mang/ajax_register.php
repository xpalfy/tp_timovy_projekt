<?php
require_once '../config.php';
session_start();
header('Content-Type: application/json');

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;
require_once '../vendor/autoload.php';

function isUserExists($conn, $field, $value) {
    $query = "SELECT id FROM users WHERE $field = ?";
    $stmt = $conn->prepare($query);
    $stmt->bind_param('s', $value);
    $stmt->execute();
    $stmt->store_result();
    return $stmt->num_rows > 0;
}

$data = json_decode(file_get_contents("php://input"), true);

$username = trim($data['username'] ?? '');
$email = trim($data['email'] ?? '');
$password = $data['password'] ?? '';
$confirm = $data['confirm'] ?? '';

if (empty($username) || empty($email) || empty($password) || empty($confirm)) {
    echo json_encode(['success' => false, 'message' => 'All fields are required.']);
    exit;
}

if ($password !== $confirm) {
    echo json_encode(['success' => false, 'message' => 'Passwords do not match.']);
    exit;
}

if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
    echo json_encode(['success' => false, 'message' => 'Invalid email format.']);
    exit;
}

if (strlen($password) < 8) {
    echo json_encode(['success' => false, 'message' => 'Password must be at least 8 characters.']);
    exit;
}

if (strlen($username) > 255) {
    echo json_encode(['success' => false, 'message' => 'Username is too long.']);
    exit;
}

$conn = getDatabaseConnection();

if (isUserExists($conn, 'username', $username)) {
    echo json_encode(['success' => false, 'message' => 'Username already taken.']);
    exit;
}

if (isUserExists($conn, 'email', $email)) {
    echo json_encode(['success' => false, 'message' => 'Email already used.']);
    exit;
}

$code = rand(100000, 999999);
$hashedPassword = password_hash($password, PASSWORD_DEFAULT);

$stmt = $conn->prepare("INSERT INTO users (username, email, password, verification_code, is_verified) VALUES (?, ?, ?, ?, 0)");
$stmt->bind_param("ssss", $username, $email, $hashedPassword, $code);
$stmt->execute();
$stmt->close();

require_once 'mail.php';
$emailSent = sendVerificationEmail($email, $code);

if ($emailSent) {
    echo json_encode(['success' => true, 'message' => 'Registration successful. Verification code sent.', 'email' => $email]);
} else {
    echo json_encode(['success' => false, 'message' => 'Failed to send verification email.']);
}
