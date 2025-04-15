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

    if (empty($post['user_name']) || empty($post['id']) || empty($post['doc_id']) || empty($post['doc_name']) || empty($post['image_name']) || empty($post['type'])) {
        http_response_code(400);
        echo json_encode(['error' => 'Invalid input data']);
        exit;
    }

    if ($post['user_name'] !== $userData['username'] || $post['id'] !== $userData['id']) {
        http_response_code(400);
        echo json_encode(['error' => 'Invalid user']);
        exit;
    }

    if (!preg_match('/^[a-zA-Z0-9_\-]+$/', $post['image_name'])) {
        http_response_code(400);
        echo json_encode(['error' => 'Invalid document name']);
        exit;
    }

    $doc_id = $post['doc_id'];
    $type = $post['type'];
    $doc_name = $post['doc_name'];
    $user_name = $post['user_name'];
    $user_id = $post['id'];
    $image_name = $post['image_name'];

    $conn = getDatabaseConnection();

    // Check if image exists in the pictures table as temp
    $stmt = $conn->prepare('SELECT * FROM pictures WHERE creator = ? AND name = ? AND type = ?');
    $temp = 'temp';
    $stmt->bind_param('iss', $user_id, $image_name, $temp);
    $stmt->execute();
    $result = $stmt->get_result();
    if ($result->num_rows === 0) {
        http_response_code(400);
        echo json_encode(['error' => 'Image not found in database']);
        exit;
    }
    $row = $result->fetch_assoc();
    $stmt->close();
    
    // Check if the new file path already exists
    if (file_exists($new_file_path)) {
        http_response_code(400);
        echo json_encode(['error' => 'File already exists']);
        exit;
    }
    $new_db_file_path = '/DOCS/' . $user_name . '/' . $type . '/' . $doc_name . '/' . $image_name;
    $stmt = $conn->prepare('SELECT * FROM items WHERE document_id = ? AND image_path = ?');
    $stmt->bind_param('is', $doc_id, $new_db_file_path);
    $stmt->execute();
    $result = $stmt->get_result();
    if ($result->num_rows > 0) {
        http_response_code(400);
        echo json_encode(['error' => 'File already exists in items']);
        exit;
    }
    $stmt->close();

    
    $old_file_path = realpath(__DIR__ . '/../..') . $row['path'];

    if (!file_exists($old_file_path)) {
        http_response_code(400);
        echo json_encode(['error' => 'Temp file not found']);
        exit;
    }
    // Define new directory and move file
    $new_directory = realpath(__DIR__ . '/../..') . '/DOCS/' . $user_name . '/' . $type . '/' . $doc_name;
    if (!is_dir($new_directory)) {
        mkdir($new_directory, 0777, true);
    }

    $new_file_path = $new_directory . '/' . basename($old_file_path);

    if (!rename($old_file_path, $new_file_path)) {
        http_response_code(500);
        echo json_encode(['error' => 'Failed to move file']);
        exit;
    }
    // remove the old picture from the database
    $stmt = $conn->prepare('DELETE FROM pictures WHERE creator = ? AND name = ? AND type = ?');
    $temp = 'temp';
    $stmt->bind_param('iss', $user_id, $image_name, $temp);
    $stmt->execute();
    if ($stmt->affected_rows === 0) {
        http_response_code(500);
        echo json_encode(['error' => 'Failed to delete old image from database']);
        exit;
    }
    $stmt->close();
    // Insert the new image into the items table
    $stmt = $conn->prepare('INSERT INTO items (document_id, status, title, description, image_path, publish_date, modified_date) VALUES (?, ?, ?, ?, ?, ?, ?)');
    $status = 'UPLOADED';
    $description = 'Please add a description';
    $date = date('Y-m-d H:i:s');
    $stmt->bind_param('issssss', $doc_id, $status, $image_name, $description, $new_db_file_path, $date, $date);
    if (!$stmt->execute()) {
        http_response_code(500);
        echo json_encode(['error' => 'Failed to insert image into items']);
        exit;
    }
    $stmt->close();
    $conn->close();
    echo json_encode(['success' => true, 'message' => 'File moved and database updated']);
}
else {
    http_response_code(405);
    echo json_encode(['error' => 'Method not allowed']);
}
?>