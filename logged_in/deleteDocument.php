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

$pictureId = $_GET['id'];
$userId = $_GET['user'];

if ($userId == null || $pictureId == null) {
    $_SESSION['toast'] = [
        'message' => 'Invalid URL',
        'type' => 'error'
    ];
    header('Location: documents.php');
    exit();
}

if ($userId != $userData['id']) {
    $_SESSION['toast'] = [
        'message' => 'You can only delete your own documents',
        'type' => 'error'
    ];
    header('Location: documents.php');
    exit();
}


$conn = getDatabaseConnection();

$sql = "SELECT * FROM pictures WHERE ID = ? AND creator = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param('ii', $pictureId, $userId);
$stmt->execute();
$result = $stmt->get_result();
if ($result->num_rows > 0) {
    $row = $result->fetch_assoc();
    $path = $row['path'];
    $stmt->close();
    $sql = "DELETE FROM pictures WHERE ID = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param('i', $pictureId);
    $stmt->execute();
    $stmt->close();
    unlink('../' . $path);
    $_SESSION['toast'] = [
        'message' => 'Document deleted',
        'type' => 'success'
    ];
    header('Location: documents.php');
    exit();
} else {
    $_SESSION['toast'] = [
        'message' => 'Document not found',
        'type' => 'error'
    ];
    header('Location: documents.php');
    exit();
}


?>