<?php

require_once '../checkType.php';

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

require '../config.php'; // Include your database connection

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
        $usernames[] = $row; // Collect matching usernames
    }

    $stmt->close();
    $conn->close();
    

    echo json_encode( $usernames); // Return results as JSON
}
?>