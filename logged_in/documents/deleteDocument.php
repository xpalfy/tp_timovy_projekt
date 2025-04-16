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

    if (empty($post['user_name']) || empty($post['id']) || empty($post['doc_id'])) {
        http_response_code(400);
        echo json_encode(['error' => 'Invalid input data']);
        exit;
    }

    if ($post['user_name'] !== $userData['username'] || $post['id'] !== $userData['id']) {
        http_response_code(400);
        echo json_encode(['error' => 'Invalid user']);
        exit;
    }

    $doc_id = $post['doc_id'];
    $user_name = $post['user_name'];
    $id = $post['id'];


    $conn = getDatabaseConnection();

    // Check if the document exists
    $stmt = $conn->prepare('SELECT * FROM documents WHERE author_id = ? AND doc_id');

    $stmt->bind_param('ii', $id, $doc_id);
    $stmt->execute();
    $result = $stmt->get_result();
    if ($result->num_rows === 0) {
        http_response_code(400);
        echo json_encode(['error' => 'Document not found']);
        exit;
    }
    $row = $result->fetch_assoc();
    $stmt->close();
    // Delete the items associated with the document
    $stmt = $conn->prepare('DELETE FROM items WHERE document_id = ?');
    $stmt->bind_param('i', $doc_id);
    $stmt->execute();
    if ($stmt->affected_rows === 0) {
        http_response_code(400);
        echo json_encode(['error' => 'Failed to delete items']);
        exit;
    }
    $stmt->close();
    // Delete the document
    $stmt = $conn->prepare('DELETE FROM documents WHERE doc_id = ? AND author_id = ?');
    $stmt->bind_param('ii', $doc_id, $id);
    $stmt->execute();
    if ($stmt->affected_rows === 0) {
        http_response_code(400);
        echo json_encode(['error' => 'Failed to delete document']);
        exit;
    }
    $stmt->close();
    // Delete the directory associated with the document
    $directory = realpath(__DIR__ . '/../..') . '/DOCS/' . $user_name . '/' . $row['doc_type'] . '/' . $row['title'];
    if (is_dir($directory)) {
        $files = glob($directory . '/*'); // Get all files in the directory
        foreach ($files as $file) {
            if (is_file($file)) {
                unlink($file); // Delete the file
            }
        }
        rmdir($directory); // Remove the directory
    }
    echo json_encode(['success' => true, 'message' => 'Document deleted successfully']);
    
    $conn->close();
}
else {
    http_response_code(405);
    echo json_encode(['error' => 'Method not allowed']);
}
?>