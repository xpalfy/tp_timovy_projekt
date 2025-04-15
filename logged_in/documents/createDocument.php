<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

require_once '../../checkType.php';
require_once '../../config.php';

try {
    $userData = validateToken();
} catch (Exception $e) {
    http_response_code(500);
    echo json_encode(['error' => 'Token validation failed']);
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $post = json_decode(file_get_contents('php://input'), true);

    if (empty($post['user_name']) || empty($post['id']) || empty($post['type']) || empty($post['doc_name'])) {
        http_response_code(400);
        echo json_encode(['error' => 'Invalid input data']);
        exit;
    }

    if ($post['user_name'] !== $userData['username'] || $post['id'] !== $userData['id']) {
        http_response_code(400);
        echo json_encode(['error' => 'Invalid user']);
        exit;
    }

    if (!preg_match('/^[a-zA-Z0-9_\-]+$/', $post['doc_name'])) {
        http_response_code(400);
        echo json_encode(['error' => 'Invalid document name']);
        exit;
    }

    $doc_name = $post['doc_name'];
    $user_name = $post['user_name'];
    $id = $post['id'];
    $type = $post['type'];


    $conn = getDatabaseConnection();

    // Check if the document already exists
    $stmt = $conn->prepare('SELECT * FROM documents WHERE author_id = ? AND title = ? AND doc_type = ?');

    $stmt->bind_param('iss', $id, $doc_name, $type);
    $stmt->execute();
    $result = $stmt->get_result();
    if ($result->num_rows > 0) {
        http_response_code(400);
        echo json_encode(['error' => 'Document already exists']);
        exit;
    }
    $stmt->close();

    // Create the directory for the document
    $dir = realpath(__DIR__ . '/../..') . '/DOCS/' . $user_name . '/' . $type . '/' . $doc_name;
    if (!is_dir($dir)) {
        if (!mkdir($dir, 0777, true)) {
            http_response_code(500);
            echo json_encode(['error' => 'Failed to create directory']);
            exit;
        }
    }

    // Create the document
    $stmt = $conn->prepare('INSERT INTO documents (author_id, title, doc_type, status, description) VALUES (?, ?, ?, ?, ?)');
    $status = 'ACTIVE';
    $description = 'Please add a description';
    $stmt->bind_param('issss', $id, $doc_name, $type, $status, $description);
    if (!$stmt->execute()) {
        http_response_code(500);
        echo json_encode(['error' => 'Failed to create document']);
        exit;
    }
    $stmt->close();

    // Fetch the document ID
    $stmt = $conn->prepare('SELECT id FROM documents WHERE author_id = ? AND title = ? AND doc_type = ?');
    $stmt->bind_param('iss', $id, $doc_name, $type);
    $stmt->execute();
    $result = $stmt->get_result();
    if ($result->num_rows === 0) {
        http_response_code(500);
        echo json_encode(['error' => 'Failed to fetch document ID']);
        exit;
    }
    $row = $result->fetch_assoc();
    $document_id = $row['id'];
    $stmt->close();
    echo json_encode(['success' => true ,'message' => 'Document created successfully', 'document_id' => $document_id]);
    $conn->close();
}
else {
    http_response_code(405);
    echo json_encode(['error' => 'Method not allowed']);
}
?>