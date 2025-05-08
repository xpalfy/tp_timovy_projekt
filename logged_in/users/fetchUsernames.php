<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

header('Content-Type: application/json');

require '../../checkType.php';
require '../../config.php';

try {
    $userData = validateToken();
} catch (Exception $e) {
    http_response_code(500);
    $_SESSION['toast'] = ['type' => 'error', 'message' => 'Token validation failed'];
    header('Location: ../../login.php');
    exit();
}

if (isset($_GET['query']) && isset($_GET['picture_id'])) {
    $query = $_GET['query'];
    $id = $_GET['picture_id'];

    $conn = getDatabaseConnection();

    $stmt = $conn->prepare("SELECT username FROM users WHERE username LIKE CONCAT(?, '%') AND username != ? AND id NOT IN (SELECT user_id FROM users_pictures WHERE picture_id = ?) LIMIT 10");
    $stmt->bind_param("ssi", $query, $userData['username'], $id);
    $stmt->execute();
    $result = $stmt->get_result();

    $usernames = [];
    while ($row = $result->fetch_assoc()) {
        $usernames[] = $row;
    }

    $stmt->close();
    $conn->close();

    echo json_encode($usernames);
} else {
    http_response_code(400);
    echo json_encode(['error' => 'Missing query or picture_id']);
}
