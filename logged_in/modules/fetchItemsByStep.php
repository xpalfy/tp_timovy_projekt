<?php
// TODO: remove this if not needed
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

require '../../checkType.php';
header('Content-Type: application/json');

try {
    $userData = validateToken();
} catch (Exception $e) {
    http_response_code(500);
    $_SESSION['toast'] = ['type' => 'error', 'message' => 'You have to log in first!'];
    header('Location: ../../login.php');
    exit();
}

require_once '../../config.php';

$conn = getDatabaseConnection();

if (!isset($_GET['status']) || empty($_GET['status']) || !isset($_GET['document_id']) || empty($_GET['document_id'])) {
    http_response_code(400);
    echo json_encode(['error' => 'Missing parameter']);
    exit;
}

$status = $_GET['status'];
$document_id = $_GET['document_id'];

// Check if the document belongs to the user
$sql = "SELECT id, doc_type FROM documents WHERE id = ? AND author_id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("ii", $document_id, $userData['id']);
$stmt->execute();
$result = $stmt->get_result();
if ($result->num_rows === 0) {
    http_response_code(403);
    echo json_encode(['error' => 'Document not found or access denied']);
    exit;
}
$type = $result->fetch_assoc()['doc_type'];
$stmt->close();

// Fetch Items in document owned by user that have the specified status
$sql = "SELECT i.id, i.title, i.image_path FROM items i WHERE i.document_id = ? AND i.status = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("is", $document_id, $status);
$stmt->execute();
$result = $stmt->get_result();
$items = [];
while ($row = $result->fetch_assoc()) {
    $items[] = $row + ['type' => $type];
}
$stmt->close();
$conn->close();
echo json_encode($items);
