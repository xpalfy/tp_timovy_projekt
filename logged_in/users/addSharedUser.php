<?php
require '../../config.php';
header('Content-Type: application/json');
session_start();
require '../../checkType.php';

try {
    $userData = validateToken();
} catch (Exception $e) {
    http_response_code(500);
    $_SESSION['toast'] = ['type' => 'error', 'message' => 'Token validation failed'];
    header('Location: ../../login.php');
    exit();
}

$documentId = $_POST['document_id'] ?? null;
$username = $_POST['username'] ?? null;

if (!$documentId || !$username) {
    echo json_encode(['error' => 'Missing data']);
    exit;
}

$conn = getDatabaseConnection();

$stmt = $conn->prepare("SELECT id FROM users WHERE username = ?");
$stmt->bind_param("s", $username);
$stmt->execute();
$result = $stmt->get_result();
if ($row = $result->fetch_assoc()) {
    $userId = $row['id'];

    $check = $conn->prepare("SELECT 1 FROM document_user_association WHERE document_id = ? AND user_id = ?");
    $check->bind_param("ii", $documentId, $userId);
    $check->execute();
    $checkResult = $check->get_result();
    if ($checkResult->num_rows === 0) {
        $insert = $conn->prepare("INSERT INTO document_user_association (document_id, user_id) VALUES (?, ?)");
        $insert->bind_param("ii", $documentId, $userId);
        if ($insert->execute()) {
            echo json_encode(['success' => true]);
            exit;
        }
    } else {
        echo json_encode(['error' => 'User already added']);
        exit;
    }
}

echo json_encode(['error' => 'User not found']);
