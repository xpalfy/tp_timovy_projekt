<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

require '../checkType.php';
header('Content-Type: application/json');

try {
    $userData = validateToken();
} catch (Exception $e) {
    http_response_code(401);
    echo json_encode(['error' => 'Token validation failed']);
    exit;
}

require_once '../config.php';

$conn = getDatabaseConnection();

$sql = "SELECT document_id FROM document_user_association WHERE user_id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $userData['id']);
$stmt->execute();
$result = $stmt->get_result();

$sharedDocuments = [];

while ($row = $result->fetch_assoc()) {
    $documentId = $row['document_id'];

    $docStmt = $conn->prepare("SELECT id, title FROM documents WHERE id = ?");
    $docStmt->bind_param("i", $documentId);
    $docStmt->execute();
    $docResult = $docStmt->get_result();

    if ($docRow = $docResult->fetch_assoc()) {
        $sharedDocuments[] = $docRow;
    }

    $docStmt->close();
}

$stmt->close();
$conn->close();

echo json_encode($sharedDocuments);
