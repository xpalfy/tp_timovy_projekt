<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);


require '../checkType.php';

if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

check();

require_once '../config.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $post = json_decode(file_get_contents('php://input'), true);

    if (empty($post['data']) || empty($post['data_name']) || empty($post['user_name']) || empty($post['id']) || empty($post['type'])) {
        http_response_code(400);
        echo json_encode(['error' => 'Invalid input data']);
        exit;
    }

    if ($post['user_name'] !== $_SESSION['user']['username'] || $post['id'] !== $_SESSION['user']['id']) {
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
    }
    $data = base64_decode($data);

    $directory_path = realpath(__DIR__.'/..') . '/' . $type . '/' . $user_name;
    if (!is_dir($directory_path)) {
        if(mkdir($directory_path, 0777, true)){
            echo 'Directory created';
        } else {
            http_response_code(400);
            echo json_encode(['error' => 'Failed to create directory']);
            exit;
        }
    }

    $file_path = '/' . $type . '/' . $user_name . '/' . $data_name . '.png';

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

    $stmt = $conn->prepare('INSERT INTO pictures (creator, path, type) VALUES (?, ?, ?)');
    $stmt->bind_param('iss', $id, $file_path, $type);
    $stmt->execute();
    
    
    if (file_put_contents('../'.$file_path, $data) !== false) {
        echo json_encode(['success' => 'True', 'message' => 'File saved']);
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
    

    $stmt->close();
    $conn->close();

    
}

header('Location: ./main.php');

?>
