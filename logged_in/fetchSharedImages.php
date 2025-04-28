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

// Get all document IDs shared with the user
$sql = "SELECT document_id FROM document_user_association WHERE user_id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $userData['id']);
$stmt->execute();
$result = $stmt->get_result();

$sharedImages = [];

while ($row = $result->fetch_assoc()) {
    $documentId = $row['document_id'];

    // Fetch the first image associated with the document
    $imgStmt = $conn->prepare("SELECT image_path FROM items WHERE document_id = ? LIMIT 1");
    $imgStmt->bind_param("i", $documentId);
    $imgStmt->execute();
    $imgResult = $imgStmt->get_result();

    if ($imgRow = $imgResult->fetch_assoc()) {
        $sharedImages[] = [
            'document_id' => $documentId,
            'image_path' => $imgRow['image_path']
        ];
    }

    $imgStmt->close();
}

$stmt->close();
$conn->close();

echo json_encode($sharedImages);
