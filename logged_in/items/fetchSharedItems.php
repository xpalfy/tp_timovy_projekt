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

if (!isset($_GET['key']) || empty($_GET['key'])) {
    http_response_code(400);
    echo json_encode(['error' => 'Missing key parameter']);
    exit;
}

$key = $_GET['key'];

$conn = getDatabaseConnection();

$sql = "
    SELECT d.id AS document_id, i.image_path
    FROM document_user_association dua
    JOIN documents d ON dua.document_id = d.id
    LEFT JOIN items i ON d.id = i.document_id
    WHERE dua.user_id = ? AND d.doc_type = ?
    GROUP BY d.id
";

$stmt = $conn->prepare($sql);
$stmt->bind_param("is", $userData['id'], $key);
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
