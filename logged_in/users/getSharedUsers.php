<?php
require '../../config.php';
header('Content-Type: application/json');
session_start();
require '../../checkType.php';

try {
    $userData = validateToken();
} catch (Exception $e) {
    http_response_code(500);
    $_SESSION['toast'] = ['type' => 'error', 'message' => 'You have to log in first!'];
    header('Location: ../../login.php');
    exit();
}

$documentId = $_GET['id'] ?? null;

if (!$documentId) {
    echo json_encode([]);
    exit;
}

$conn = getDatabaseConnection();
$stmt = $conn->prepare("SELECT u.username
FROM document_user_association dua
JOIN users u ON dua.user_id = u.id
WHERE dua.document_id = ? AND dua.user_id != ?
");
$stmt->bind_param("ii", $documentId, $userData['id']);
$stmt->execute();
$result = $stmt->get_result();

$usernames = [];
while ($row = $result->fetch_assoc()) {
    $usernames[] = $row['username'];
}
echo json_encode($usernames);
