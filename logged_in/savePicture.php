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
    $_SESSION['toast'] = ['type' => 'error', 'message' => 'Token validation failed'];
    header('Location: login.php');
}


if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $post = json_decode(file_get_contents('php://input'), true);

    if (empty($post['data']) || empty($post['data_name']) || empty($post['user_name']) || empty($post['id']) || empty($post['type'])) {
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

    $data = $post['data'];
    $data_name = $post['data_name'];
    $user_name = $post['user_name'];
    $id = $post['id'];
    $type = $post['type'];

    if (strpos($data, 'data:image/png;base64,') === 0) {
        $data = explode(',', $data)[1];
        $extension = '.png';
    } elseif (strpos($data, 'data:image/jpeg;base64,') === 0) {
        $data = explode(',', $data)[1];
        $extension = '.jpg';
    } else {
        echo json_encode(['success' => false, 'message' => 'Unsupported image format.']);
        exit;
    }

    $data = base64_decode($data);

    $directory_path = realpath(__DIR__ . '/..') . '/' . $type . '/' . $user_name;
    if (!is_dir($directory_path)) {
        if (mkdir($directory_path, 0777, true)) {
        } else {
            http_response_code(400);
            echo json_encode(['error' => 'Failed to create directory']);
            exit;
        }
    }

    $file_path = '/' . $type . '/' . $user_name . '/' . $data_name . $extension;

    $conn = getDatabaseConnection();
    $stmt = $conn->prepare('SELECT * FROM pictures WHERE creator = ? AND path = ?');
    $stmt->bind_param('is', $id, $file_path);
    $stmt->execute();
    $result = $stmt->get_result();
    if ($result->num_rows > 0) {
        http_response_code(400);
        echo json_encode(['error' => 'File already exists']);
        $stmt->close();
        $conn->close();
        exit;
    }

    $stmt = $conn->prepare('INSERT INTO pictures (creator, path, type, name) VALUES (?, ?, ?, ?)');
    $stmt->bind_param('isss', $id, $file_path, $type, $data_name);
    $stmt->execute();
    $picture_id = $conn->insert_id; // Get the inserted picture ID


    if (file_put_contents('../' . $file_path, $data) !== false) {
        http_response_code(200);
        echo json_encode(['success' => 'True', 'message' => 'File saved', 'path' => $file_path, 'picture_id' => $picture_id]);
        exit;
    } else {
        http_response_code(400);
        echo json_encode(['error' => 'Failed to save file']);
        // delete from DB
        $stmt = $conn->prepare('DELETE FROM pictures WHERE creator = ? AND path = ?');
        $stmt->bind_param('is', $id, $file_path);
        $stmt->execute();
        $stmt->close();
        $conn->close();
        exit;
    }


}

header('Location: ./main.php');

?>
