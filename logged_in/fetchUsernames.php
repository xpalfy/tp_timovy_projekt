<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

require '../checkType.php';
require '../config.php';

try {
    $userData = validateToken();
} catch (Exception $e) {
    http_response_code(500);
    $_SESSION['toast'] = ['type' => 'error', 'message' => 'Token validation failed'];
    header('Location: login.php');
}


if (isset($_GET['query'])) {
    $query = $_GET['query'];
    $id = $_GET['picture_id'];


    $conn = getDatabaseConnection();

    $stmt = $conn->prepare("SELECT username FROM users WHERE username LIKE (CONCAT( ? , '%')) AND username != ? AND id NOT IN (SELECT user_id FROM users_pictures WHERE picture_id = ?) LIMIT 10");
    $stmt->bind_param("ssi", $query, $_SESSION['user']['username'], $id);
    $stmt->execute();
    $result = $stmt->get_result();

    $usernames = [];
    while ($row = $result->fetch_assoc()) {
        $usernames[] = $row;
    }

    $stmt->close();
    $conn->close();


    echo json_encode($usernames);
}
?>