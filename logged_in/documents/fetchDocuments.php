<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

require '../../checkType.php';
header('Content-Type: application/json');

try {
    $userData = validateToken();
} catch (Exception $e) {
    http_response_code(401);
    echo json_encode(['error' => 'Token validation failed']);
    exit;
}

require_once '../../config.php';

$conn = getDatabaseConnection();

if (!isset($_GET['key']) || empty($_GET['key'])) {
    http_response_code(400);
    echo json_encode(['error' => 'Missing key parameter']);
    exit;
}

$key = $_GET['key'];
$public = isset($_GET['public']) && $_GET['public'] === 'true';

if ($public) {
    $sql = "SELECT id, title FROM documents WHERE is_public = 1 AND doc_type = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("s", $key);
} else {
    $sql = "SELECT id, title FROM documents WHERE author_id = ? AND doc_type = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("is", $userData['id'], $key);
}

$stmt->execute();
$result = $stmt->get_result();

$documents = [];
while ($row = $result->fetch_assoc()) {
    $documents[] = $row;
}

$stmt->close();
$conn->close();

echo json_encode($documents);
