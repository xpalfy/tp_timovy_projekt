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
    $_SESSION['toast'] = ['type' => 'error', 'message' => 'Token validation failed'];
    header('Location: ../../login.php');
    exit();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $post = json_decode(file_get_contents('php://input'), true);

    if (empty($post['user_name']) || empty($post['id']) || empty($post['item_id'])) {
        http_response_code(400);
        echo json_encode(['error' => 'Invalid input data']);
        exit;
    }

    if ($post['user_name'] !== $userData['username'] || $post['id'] !== $userData['id']) {
        http_response_code(400);
        echo json_encode(['error' => 'Invalid user']);
        exit;
    }

    if (!empty($post['title']) && !preg_match('/^[a-zA-Z0-9_\-]+$/', $post['title'])) {
        http_response_code(400);
        echo json_encode(['error' => 'Invalid item name']);
        exit;
    }

    $errors = [];
    $item_id = $post['item_id'];
    $status = $post['status'] ?? null;
    $title = $post['title'] ?? null;
    $description = $post['description'] ?? null;
    $user_name = $post['user_name'];
    $user_id = $post['id'];
    $conn = getDatabaseConnection();

    // Check if item exists
    $stmt = $conn->prepare('SELECT * FROM items WHERE id = ?');
    $stmt->bind_param('i', $item_id);
    $stmt->execute();
    $result = $stmt->get_result();
    if ($result->num_rows === 0) {
        http_response_code(400);
        echo json_encode(['error' => 'Item not found']);
        exit;
    }
    $row = $result->fetch_assoc();

    // Check if item belongs to the user
    $stmt = $conn->prepare('SELECT * FROM documents WHERE id = ? AND author_id = ?');
    $stmt->bind_param('ii', $row['document_id'], $user_id);
    $stmt->execute();
    $result = $stmt->get_result();
    if ($result->num_rows === 0) {
        http_response_code(400);
        echo json_encode(['error' => 'Items document does not belong to user']);
        exit;
    }

    $status_change = false;
    if ($row['status'] !== $status && !empty($status)) {
        $status_change = true;
    }
    $title_change = false;
    if ($row['title'] !== $title && !empty($title)) {
        $title_change = true;
    }
    $description_change = false;
    if ($row['description'] !== $description && !empty($description)) {
        $description_change = true;
    }
    $stmt->close();

    if ($status_change){
        // Check if status is valid
        $valid_statuses = ['UPLOADED', 'SEGMENTED', 'CLASSIFIED', 'PROCESSED', 'ERROR'];
        if (!in_array($status, $valid_statuses)) {
            $errors[] = 'Invalid status';
        } else{
            // Update item status
            $stmt = $conn->prepare('UPDATE items SET status = ? WHERE id = ?');
            $stmt->bind_param('si', $status, $item_id);
            if (!$stmt->execute()) {
                $errors[] = 'Failed to update item status';
            }
            $stmt->close();
        }
    }

    if ($title_change){
        // check if title already exists
        $stmt = $conn->prepare('SELECT * FROM items WHERE title = ? AND document_id = ?');
        $stmt->bind_param('si', $title, $row['document_id']);
        $stmt->execute();
        $result = $stmt->get_result();
        if ($result->num_rows > 0) {
            $errors[] = 'Item with this name already exists';
        } else {
            // Update item title
            $stmt = $conn->prepare('UPDATE items SET title = ? WHERE id = ?');
            $stmt->bind_param('si', $title, $item_id);
            if (!$stmt->execute()) {
                $errors[] = 'Failed to update item title';
            }
            $stmt->close();
        }
    }

    if ($description_change){
        // Update item description
        $stmt = $conn->prepare('UPDATE items SET description = ? WHERE id = ?');
        $stmt->bind_param('si', $description, $item_id);
        if (!$stmt->execute()) {
            $errors[] = 'Failed to update item description';
        }
        $stmt->close();
    }
    $conn->close();
    if (empty($errors)) {
        http_response_code(200);
        echo json_encode(['success' => true, 'message' => 'Item updated successfully']);
    } else {
        http_response_code(400);
        echo json_encode(['error' => 'Failed to update item', 'details' => $errors]);
    }

    


}
else {
    http_response_code(405);
    echo json_encode(['error' => 'Method not allowed']);
}
?>