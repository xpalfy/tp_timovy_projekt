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
    $_SESSION['toast'] = ['type' => 'error', 'message' => 'You have to log in first!'];
    header('Location: ../../login.php');
    exit();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $post = json_decode(file_get_contents('php://input'), true);

    if (empty($post['user_name']) || empty($post['id']) || empty($post['image_id'])) {
        http_response_code(400);
        echo json_encode(['error' => 'Invalid input data']);
        echo json_encode(['data' => $post]);
        exit;
    }

    if ($post['user_name'] !== $userData['username'] || $post['id'] !== $userData['id']) {
        http_response_code(400);
        echo json_encode(['error' => 'Invalid user']);
        exit;
    }
    $succes = false;
    $user_name = $post['user_name'];
    $user_id = $post['id'];
    $image_id = $post['image_id'];
    $item_id = $post['item_id'];

    $conn = getDatabaseConnection();
    $stmt = $conn->prepare('SELECT * FROM pictures WHERE id = ?');
    $stmt->bind_param('i', $image_id);
    $stmt->execute();
    $result = $stmt->get_result();
    if ($result->num_rows  != 0) {
        $row = $result->fetch_assoc();
        $stmt->close();
        $sql = "DELETE FROM pictures WHERE id = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param('i', $image_id);
        $stmt->execute();
        $stmt->close();
        echo json_encode(['success' => true, 'message' => 'Image deleted successfully']);
        $succes = true;
    }
    if ($item_id != null) {
        $stmt = $conn->prepare('SELECT * FROM items WHERE id = ?');
        $stmt->bind_param('ii', $item_id);
        $stmt->execute();
        $result = $stmt->get_result();
        if ($result->num_rows  != 0) {
            $row = $result->fetch_assoc();
            $stmt->close();
            $sql = "DELETE FROM items WHERE id = ?";
            $stmt = $conn->prepare($sql);
            $stmt->bind_param('i', $item_id);
            $stmt->execute();
            $stmt->close();
            echo json_encode(['success' => true, 'message' => 'Item deleted successfully']);
            $succes = true;
        }
    }
    $conn->close();
    if ($succes === false) {
        http_response_code(400);
        echo json_encode(['error' => 'Item or Image not found']);
    }
}
else {
    http_response_code(405);
    echo json_encode(['error' => 'Method not allowed']);
}
?>