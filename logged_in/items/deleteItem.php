<?php
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

    if (empty($post['user_name']) || empty($post['id']) || empty($post['item_id']) || empty($post['doc_id'])) {
        http_response_code(400);
        echo json_encode(['error' => 'Invalid input data']);
        exit;
    }

    if ($post['user_name'] !== $userData['username'] || $post['id'] !== $userData['id']) {
        http_response_code(400);
        echo json_encode(['error' => 'Invalid user']);
        exit;
    }

    $user_name = $post['user_name'];
    $user_id = $post['id'];
    $item_id = $post['item_id'];
    $doc_id = $post['doc_id'];

    $conn = getDatabaseConnection();

    // Check if doc of item is the user's
    $stmt = $conn->prepare('SELECT * FROM items WHERE id = ? AND document_id = ?');
    $stmt->bind_param('ii', $item_id, $doc_id);
    $stmt->execute();
    $result = $stmt->get_result();
    if ($result->num_rows === 0) {
        http_response_code(400);
        echo json_encode(['error' => 'Item not found']);
        exit;
    }
    $row = $result->fetch_assoc();
    $stmt = $conn->prepare('SELECT * FROM documents WHERE id = ? AND author_id = ?');
    $stmt->bind_param('ii', $doc_id, $user_id);
    $stmt->execute();
    $result = $stmt->get_result();
    if ($result->num_rows === 0) {
        http_response_code(400);
        echo json_encode(['error' => 'Document not found']);
        exit;
    }
    $row = $result->fetch_assoc();
    $stmt->close();
    $sql = "DELETE FROM items WHERE id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param('i', $item_id);
    $stmt->execute();
    $stmt->close();
    $conn->close();
    echo json_encode(['success' => true, 'message' => 'Item deleted successfully']);
}
else {
    http_response_code(405);
    echo json_encode(['error' => 'Method not allowed']);
}
?>