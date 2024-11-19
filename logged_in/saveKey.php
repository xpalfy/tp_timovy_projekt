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

    if (empty($post['data']) || empty($post['data_name'])) {
        http_response_code(400);
        echo json_encode(['error' => 'Invalid input data']);
        exit;
    }

    $data = $post['data'];
    if (strpos($data, 'data:image/png;base64,') === 0) {
        $data = explode(',', $data)[1]; // Strip the base64 header
    }
    $data = base64_decode($data);

    $directory = realpath(__DIR__ . '/../KEYS/');
    if (!is_dir($directory)) {
        if(mkdir($directory, 0777, true)){
            echo 'Directory created';
        } else {
            http_response_code(400);
            echo json_encode(['error' => 'Failed to create directory']);
            exit;
        }
    }

    $file = $post['data_name'] . '.png';
    echo ini_get('open_basedir');

    
    if (file_put_contents($directory .'/'. $file, $data) !== false) {
        echo json_encode(['success' => 'File saved', 'filename' => $file]);
    } else {
        http_response_code(400);
        echo json_encode(['error' => 'Failed to save file']);
        exit;
    }
}
?>
