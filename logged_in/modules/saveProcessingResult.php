<?php
// TODO: remove this if not needed
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

require_once '../../checkType.php';
require_once '../../config.php';

header('Content-Type: application/json');

try {
    $userData = validateToken();
} catch (Exception $e) {
    http_response_code(500);
    $_SESSION['toast'] = ['type' => 'error', 'message' => 'You have to log in first!'];
    header('Location: ../../login.php');
    exit();
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(['error' => 'Method not allowed']);
    exit;
}

$post = json_decode(file_get_contents('php://input'), true);

if (empty($post['document_id']) || empty($post['item_id']) || empty($post['user_id']) || empty($post['status'])) {
    http_response_code(400);
    echo json_encode(['error' => 'Invalid input data']);
    exit;
}

if ($post['user_id'] !== $userData['id']) {
    http_response_code(403);
    echo json_encode(['error' => 'Invalid user']);
    exit;
}

$document_id = (int) $post['document_id'];
$item_id = (int) $post['item_id'];
$user_id = (int) $post['user_id'];
$status = $post['status'];
$message = '';
$model_used = 'MODEL1';
$polygonResult = null;

function checkPolygons($post)
{
    if (!isset($post['polygons']) || empty($post['polygons'])) {
        http_response_code(400);
        echo json_encode(['error' => 'Polygon data missing']);
        exit;
    }

    $polygonData = $post['polygons'];
    if (!is_array($polygonData)) {
        http_response_code(400);
        echo json_encode(['error' => 'Invalid polygon JSON']);
        exit;
    }

    foreach ($polygonData as $polygon) {
        foreach ($polygon as $point) {
            if ((!isset($point['x'], $point['y']) || !is_numeric($point['x']) || !is_numeric($point['y'])) && !isset($point['type'])) {
                http_response_code(400);
                echo json_encode(['error' => 'Invalid polygon point']);
                exit;
            }
        }
    }

    return $polygonData;
}

if ($status === 'UPLOADED') {
    $message = 'File uploaded successfully';
} elseif ($status === 'SEGMENTED') {
    $polygonResult = checkPolygons($post);
    $message = 'File segmented successfully';
} elseif ($status === 'CLASSIFIED') {
    $polygonResult = checkPolygons($post);
    $message = 'File analyzed successfully';
} elseif ($status === 'PROCESSED') {
    $polygonResult = checkPolygons($post);
    $message = 'Letters segmented successfully';
} elseif ($status === 'SAVED') {
    $result_json = json_encode($post['jsonData'], JSON_PRETTY_PRINT);
    $message = 'File saved successfully';
} else {
    http_response_code(400);
    echo json_encode(['error' => 'Invalid status']);
    exit;
}

$conn = getDatabaseConnection();

// Check if processing result already exists
$stmt = $conn->prepare('SELECT id FROM processing_results WHERE item_id = ?');
$stmt->bind_param('i', $item_id);
$stmt->execute();
$result = $stmt->get_result();
$exists = $result->num_rows > 0;
$stmt->close();

if (!isset($result_json)) {
    $result_json = json_encode($polygonResult ?? null);
}

$date = date('Y-m-d H:i:s');

if ($exists) {
    // Update existing
    $stmt = $conn->prepare('UPDATE processing_results SET modified_date = ?, result = ?, model_used = ?, status = ? WHERE item_id = ?');
    $stmt->bind_param('ssssi', $date, $result_json, $model_used, $status, $item_id);
} else {
    // Insert new
    $stmt = $conn->prepare('INSERT INTO processing_results (item_id, status, message, result, model_used, created_date, modified_date) VALUES (?, ?, ?, ?, ?, ?, ?)');
    $stmt->bind_param('issssss', $item_id, $status, $message, $result_json, $model_used, $date, $date);
}

if (!$stmt->execute()) {
    http_response_code(500);
    echo json_encode(['error' => 'Database operation failed']);
    exit;
}

$stmt->close();

// Update item status
$stmt = $conn->prepare('UPDATE items SET status = ?, modified_date = ? WHERE id = ? AND document_id = ?');
$stmt->bind_param('ssii', $status, $date, $item_id, $document_id);
if (!$stmt->execute()) {
    http_response_code(500);
    echo json_encode(['error' => 'Failed to update item status']);
    exit;
}
$stmt->close();
$conn->close();

http_response_code(200);
echo json_encode(['success' => true, 'message' => $message]);
exit;
