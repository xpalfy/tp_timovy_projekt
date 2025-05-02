<?php
session_start();
require_once '../checkType.php';
require_once '../config.php';

header('Content-Type: application/json');

try {
    $userData = validateToken();
} catch (Exception $e) {
    http_response_code(401);
    echo json_encode(['error' => 'Unauthorized']);
    exit();
}

$userId = $_GET['user'] ?? null;
$documentId = $_GET['id'] ?? null;

if (!$userId || !$documentId || $userId != $userData['id']) {
    http_response_code(403);
    echo json_encode(['error' => 'Access denied']);
    exit();
}

$conn = getDatabaseConnection();

// Step 1: Get the document
$sql = "SELECT * FROM documents WHERE id = ? AND author_id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("ii", $documentId, $userId);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows === 0) {
    http_response_code(404);
    echo json_encode(['error' => 'Document not found']);
    exit();
}

$document = $result->fetch_assoc();

// Step 2: Get items related to the document and collect image paths
$sql = "SELECT image_path FROM items WHERE document_id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $documentId);
$stmt->execute();
$result = $stmt->get_result();

$imagePaths = [];
while ($row = $result->fetch_assoc()) {
    $imagePaths[] = $row['image_path'];
}

// Step 3: Get shared users
$sql = "SELECT u.username FROM users u
        JOIN document_user_association d_u_a ON u.id = d_u_a.user_id
        WHERE d_u_a.document_id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $documentId);
$stmt->execute();
$result = $stmt->get_result();

$sharedUsers = [];
while ($row = $result->fetch_assoc()) {
    $sharedUsers[] = $row['username'];
}

$conn->close();

// Final output
echo json_encode([
    'document' => $document,
    'imagePaths' => $imagePaths,
    'sharedUsers' => $sharedUsers
]);
