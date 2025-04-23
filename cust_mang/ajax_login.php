<?php
require_once '../config.php';
require_once '../vendor/autoload.php';

use Firebase\JWT\JWT;

header('Content-Type: application/json');
session_start();

$data = json_decode(file_get_contents("php://input"), true);
$username = trim($data['username'] ?? '');
$password = $data['password'] ?? '';

if (!$username || !$password) {
    echo json_encode(['success' => false, 'message' => 'Username and password are required.']);
    exit;
}

$conn = getDatabaseConnection();
$stmt = $conn->prepare('SELECT id, username, password, email, is_verified FROM users WHERE username = ?');
$stmt->bind_param('s', $username);
$stmt->execute();
$stmt->store_result();

if ($stmt->num_rows === 0) {
    echo json_encode(['success' => false, 'message' => 'Invalid username.']);
    exit;
}

$stmt->bind_result($id, $uname, $hash, $email, $is_verified);
$stmt->fetch();

if (!password_verify($password, $hash)) {
    echo json_encode(['success' => false, 'message' => 'Invalid password.']);
    exit;
}

if (!$is_verified) {
    $code = rand(100000, 999999);

    $update = $conn->prepare("UPDATE users SET verification_code = ? WHERE id = ?");
    $update->bind_param('si', $code, $id);
    $update->execute();

    require_once 'mail.php';
    sendVerificationEmail($email, $code);

    echo json_encode([
        'success' => false,
        'unverified' => true,
        'email' => $email,
        'message' => 'Your email is not verified. A new verification code has been sent.'
    ]);
    exit;
}

require_once '../checkType.php';
$token = generateToken($id, $uname);
$_SESSION['toast'] = ['type' => 'success', 'message' => 'Login successful!'];
$_SESSION['token'] = $token;

echo json_encode(['success' => true, 'message' => 'Login successful!']);
exit;
