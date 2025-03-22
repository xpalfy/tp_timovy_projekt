<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

require '../checkType.php';
require_once '../config.php';

try {
    $userData = validateToken();
} catch (Exception $e) {
    http_response_code(500);
    echo json_encode(['error' => 'Token validation failed']);
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $post = json_decode(file_get_contents('php://input'), true);

    if (empty($post['data_name']) || empty($post['user_name']) || empty($post['id']) || empty($post['type'])) {
        http_response_code(400);
        echo json_encode(['error' => 'Invalid input data']);
        exit;
    }

    if ($post['user_name'] !== $userData['username'] || $post['id'] !== $userData['id']) {
        http_response_code(400);
        echo json_encode(['error' => 'Invalid user']);
        exit;
    }

    if (!preg_match('/^[a-zA-Z0-9_\-]+$/', $post['data_name'])) {
        http_response_code(400);
        echo json_encode(['error' => 'Invalid file name']);
        exit;
    }

    $data_name = $post['data_name'];
    $user_name = $post['user_name'];
    $id = $post['id'];
    $type = $post['type'];

    $conn = getDatabaseConnection();

    // Fetch existing file path from DB
    $stmt = $conn->prepare('SELECT path FROM pictures WHERE creator = ? AND name = ?');
    $stmt->bind_param('is', $id, $data_name);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows === 0) {
        http_response_code(400);
        echo json_encode(['error' => 'File not found in database', 'statement' => $stmt, 'id' => $id, 'data_name' => $data_name, 'result' => $result]);
        exit;
    }

    $row = $result->fetch_assoc();

    $old_file_path = realpath(__DIR__ . '/..') . $row['path'];

    if (!file_exists($old_file_path)) {
        http_response_code(400);
        echo json_encode(['error' => 'File not found on server', 'old_file_path' => $old_file_path, 'row' => $row]);
        exit;
    }

    // Define new directory and move file
    $new_directory = realpath(__DIR__ . '/..') . '/' . $type . '/' . $user_name;
    if (!is_dir($new_directory)) {
        mkdir($new_directory, 0777, true);
    }

    $new_file_path = $new_directory . '/' . basename($old_file_path);

    if (!rename($old_file_path, $new_file_path)) {
        http_response_code(500);
        echo json_encode(['error' => 'Failed to move file']);
        exit;
    }

    // Update DB with new file path and type
    $new_db_path = '/' . $type . '/' . $user_name . '/' . basename($old_file_path);
    // TODO: Check if picture already exists (problem 2 DB records 1 picture)
    $stmt = $conn->prepare('UPDATE pictures SET path = ?, type = ? WHERE creator = ? AND name = ?');
    $stmt->bind_param('ssis', $new_db_path, $type, $id, $data_name);
    $stmt->execute();

    if ($stmt->affected_rows > 0) {
        http_response_code(200);
        echo json_encode(['success' => true, 'message' => 'File moved, database updated, and type set']);
    } else {
        http_response_code(500);
        echo json_encode(['error' => 'Database update failed']);
    }

    $stmt->close();
    $conn->close();
} else {
    http_response_code(400);
    echo json_encode(['error' => 'Invalid Request']);
}
?>