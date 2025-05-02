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

$sql = "
    SELECT d.id, d.title
    FROM document_user_association dua
    JOIN documents d ON dua.document_id = d.id
    WHERE dua.user_id = ? AND d.doc_type = ?
";

$stmt = $conn->prepare($sql);
$stmt->bind_param("is", $userData['id'], $key);
$stmt->execute();
$result = $stmt->get_result();

$sharedDocuments = [];
while ($row = $result->fetch_assoc()) {
    $sharedDocuments[] = $row;
}

$stmt->close();
$conn->close();

echo json_encode($sharedDocuments);
