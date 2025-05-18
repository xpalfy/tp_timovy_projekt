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
    $_SESSION['toast'] = ['type' => 'error', 'message' => 'You have to log in first!'];
    header('Location: ../../login.php');
    exit();
}

if ($_SERVER['REQUEST_METHOD'] === 'DELETE') {
    $input = json_decode(file_get_contents('php://input'), true);

    if (empty($input['user_name']) || empty($input['id']) || empty($input['doc_id'])) {
        http_response_code(400);
        echo json_encode(['error' => 'Invalid input data']);
        exit;
    }

    if ($input['user_name'] !== $userData['username'] || $input['id'] !== $userData['id']) {
        http_response_code(403);
        echo json_encode(['error' => 'Unauthorized user']);
        exit;
    }

    $flask_url = "https://python.tptimovyprojekt.software/documents/delete_document";
    $fullCallerUrl = (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? "https" : "http") .
                     "://$_SERVER[HTTP_HOST]$_SERVER[REQUEST_URI]";

    $ch = curl_init($flask_url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "DELETE");
    curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode([
        'token' => $_SESSION['token'],
        'doc_id' => $input['doc_id'],
        'id' => $input['id'],
    ]));
    curl_setopt($ch, CURLOPT_HTTPHEADER, [
        'Content-Type: application/json',
        "X-Caller-Url: $fullCallerUrl"
    ]);

    $response = curl_exec($ch);
    $httpcode = curl_getinfo($ch, CURLINFO_HTTP_CODE);

    if (curl_errno($ch)) {
        http_response_code(500);
        echo json_encode(['error' => 'Internal server error']);
        curl_close($ch);
        exit;
    }

    curl_close($ch);

    http_response_code($httpcode);
    echo $response;

    if ($httpcode === 200) {
        $_SESSION['toast'] = ['type' => 'success', 'message' => 'Document deleted successfully'];
    } else {
        $_SESSION['toast'] = ['type' => 'error', 'message' => 'Failed to delete document'];
    }
} else {
    http_response_code(405);
    echo json_encode(['error' => 'Method not allowed']);
}
?>
