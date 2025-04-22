<?php
require_once 'config.php';
session_start();
header('Content-Type: application/json');

$data = json_decode(file_get_contents("php://input"), true);

$email = trim($data['email'] ?? '');
$code = trim($data['code'] ?? '');

if (empty($email) || empty($code)) {
    echo json_encode(['success' => false, 'message' => 'Email and verification code are required.']);
    exit;
}

if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
    echo json_encode(['success' => false, 'message' => 'Invalid email address.']);
    exit;
}

$conn = getDatabaseConnection();

$stmt = $conn->prepare("SELECT verification_code FROM users WHERE email = ? AND is_verified = 0");
$stmt->bind_param("s", $email);
$stmt->execute();
$stmt->bind_result($dbCode);
$stmt->fetch();
$stmt->close();

if (!$dbCode) {
    echo json_encode(['success' => false, 'message' => 'No verification pending for this email.']);
    exit;
}

if ($code === $dbCode) {
    $update = $conn->prepare("UPDATE users SET is_verified = 1, verification_code = NULL WHERE email = ?");
    $update->bind_param("s", $email);
    $update->execute();
    $update->close();

    $_SESSION['toast'] = ['type' => 'success', 'message' => 'Email verified successfully!'];
    echo json_encode(['success' => true, 'message' => 'Email verified successfully.']);
} else {
    echo json_encode(['success' => false, 'message' => 'Invalid verification code.']);
}
