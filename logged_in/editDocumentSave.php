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
    exit();
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $pictureId = $_POST['id'];
    $creatorId = $_POST['user'];
    $pictureName = $_POST['name'];
    $sharedUsers = $_POST['sharedUsers'];

    if ($creatorId != $userData['id']) {
        $_SESSION['toast'] = [
            'message' => 'You can only edit your own documents',
            'type' => 'error'
        ];
        header('Location: ownCipherDocuments.php');
        exit();
    }

    $postData = [
        'id' => $pictureId,
        'user' => $creatorId,
        'name' => $pictureName,
        'sharedUsers' => $sharedUsers
    ];

    $flaskUrl = 'https://python.tptimovyprojekt.software/update_document'; // Flask endpoint
    $fullCallerUrl = (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? "https" : "http") .
                 "://$_SERVER[HTTP_HOST]$_SERVER[REQUEST_URI]";

    $ch = curl_init($flaskUrl);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($postData));
    curl_setopt($ch, CURLOPT_POST, true);
    curl_setopt($ch, CURLOPT_HTTPHEADER, [
        "Referer: $fullCallerUrl"
    ]);
    $response = curl_exec($ch);
    $httpcode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    curl_close($ch);

    if ($httpcode == 200) {
        $_SESSION['toast'] = [
            'message' => 'Document updated successfully',
            'type' => 'success'
        ];
        header('Location: ownCipherDocuments.php');
        exit();
    } else {
        $_SESSION['toast'] = [
            'message' => 'Failed to update document',
            'type' => 'error'
        ];
        header('Location: editDocument.php?user=' . $creatorId . '&id=' . $pictureId);
        exit();
    }
}
?>
