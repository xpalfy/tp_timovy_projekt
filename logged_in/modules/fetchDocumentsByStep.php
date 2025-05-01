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

if (!isset($_GET['status']) || empty($_GET['status'])) {
    http_response_code(400);
    echo json_encode(['error' => 'Missing status parameter']);
    exit;
}

$status = $_GET['status'];

// Fetch documents owned by user that have items with the specified status
$sql = "
    SELECT DISTINCT d.id, d.title, d.doc_type 
    FROM documents d
    INNER JOIN items i ON d.id = i.document_id
    WHERE d.author_id = ? AND i.status = ?
";

$stmt = $conn->prepare($sql);
$stmt->bind_param("is", $userData['id'], $status);
$stmt->execute();
$result = $stmt->get_result();

$documents = [];
while ($row = $result->fetch_assoc()) {
    $documents[] = $row;
}

$stmt->close();
$conn->close();

echo json_encode($documents);
