<?php
session_start();
require_once '../../checkType.php';
require_once '../../config.php';

header('Content-Type: application/json');

try {
    $userData = validateToken();
} catch (Exception $e) {
    http_response_code(500);
    $_SESSION['toast'] = ['type' => 'error', 'message' => 'Token validation failed'];
    header('Location: ../../login.php');
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
$sql = "SELECT * FROM documents WHERE id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $documentId);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows === 0) {
    http_response_code(404);
    echo json_encode(['error' => 'Document not found']);
    exit();
}

$document = $result->fetch_assoc();

//from document author_id I have to connect users table to get the author name
$sql = "SELECT username FROM users WHERE id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $document['author_id']);
$stmt->execute();
$result = $stmt->get_result();
if ($result->num_rows === 0) {
    http_response_code(404);
    echo json_encode(['error' => 'Author not found']);
    exit();
}
$author = $result->fetch_assoc();
$document['author_name'] = $author['username'];

// Step 2: Get items related to the document and collect image paths
$sql = "SELECT image_path, publish_date FROM items WHERE document_id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $documentId);
$stmt->execute();
$result = $stmt->get_result();

$imagePaths = [];
while ($row = $result->fetch_assoc()) {
    $imagePaths[] = $row['image_path'];
    if (empty($row['publish_date'])) {
        $publishDate = null; 
    } else {
        $publishDate = $row['publish_date'];
    }
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
    'publishDate' => $publishDate,
    'sharedUsers' => $sharedUsers
]);
