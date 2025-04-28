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

if (!isset($_GET['key']) || empty($_GET['key'])) {
    http_response_code(400);
    echo json_encode(['error' => 'Missing key parameter']);
    exit;
}

$key = $_GET['key'];
$public = isset($_GET['public']) && $_GET['public'] === 'true';

if ($public) {
    // Public documents (is_public = 1)
    $sql = "SELECT d.id as document_id, i.image_path
            FROM documents d
            LEFT JOIN items i ON d.id = i.document_id
            WHERE d.is_public = 1 AND d.doc_type = ?
            GROUP BY d.id";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("s", $key);
} else {
    // Private documents (only user's own)
    $sql = "SELECT d.id as document_id, i.image_path
            FROM documents d
            LEFT JOIN items i ON d.id = i.document_id
            WHERE d.author_id = ? AND d.doc_type = ?
            GROUP BY d.id";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("is", $userData['id'], $key);
}

$stmt->execute();
$result = $stmt->get_result();

$images = [];
while ($row = $result->fetch_assoc()) {
    if (!isset($images[$row['document_id']])) {
        $images[$row['document_id']] = $row['image_path'];
    }
}

$stmt->close();
$conn->close();

echo json_encode($images);
