<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);


require_once '../checkType.php';

if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

check();

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

if ($userId != $_SESSION['user']['id']) {
    $_SESSION['toast'] = [
        'message' => 'You can only delete your own documents',
        'type' => 'error'
    ];
    header('Location: documents.php');
    exit();
}

require_once '../config.php';

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
    unlink('../'.$path);
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