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

if ($_SERVER['REQUEST_METHOD'] == 'POST') {

    $pictureId = $_POST['id'];
    $creatorId = $_POST['user'];
    $pictureName = $_POST['name'];
    $sharedUsers = explode(',', $_POST['sharedUsers']);

    if ($creatorId != $_SESSION['user']['id']) {
        $_SESSION['toast'] = [
            'message' => 'You can only edit your own documents',
            'type' => 'error'
        ];
        header('Location: documents.php');
        exit();
    }

    if (!empty($pictureName)) {
        $conn = getDatabaseConnection();

        $stmt = $conn->prepare("SELECT * FROM pictures WHERE name = ? AND creator = ? AND ID != ?");
        $stmt->bind_param('sii', $pictureName, $creatorId, $pictureId);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result->num_rows > 0) {
            $_SESSION['toast'] = [
                'message' => 'Document name already exists',
                'type' => 'error'
            ];
            header('Location: editDocument.php?user=' . $creatorId . '&id=' . $pictureId);
            exit();
        }


        $stmt = $conn->prepare("SELECT * FROM pictures WHERE ID = ? AND creator = ?");
        $stmt->bind_param('ii', $pictureId, $creatorId);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result->num_rows > 0) {
            $row = $result->fetch_assoc();
            $old_picture_name = $row['name'];
            $old_picture_path = $row['path'];

            if ($old_picture_name != $pictureName) {
                $stmt = $conn->prepare("UPDATE pictures SET name = ? WHERE ID = ? AND creator = ?");
                $stmt->bind_param('sii', $pictureName, $pictureId, $creatorId);
                $stmt->execute();

                $new_picture_path = str_replace($old_picture_name, $pictureName, $old_picture_path);

                if (file_exists('../' . $old_picture_path) && is_writable(dirname('../' . $old_picture_path))) {
                    rename('../' . $old_picture_path, '../' . $new_picture_path);
                } else {
                    $_SESSION['toast'] = [
                        'message' => 'Failed to rename file: Path is invalid or not writable.',
                        'type' => 'error'
                    ];
                    header('Location: editDocument.php?user=' . $creatorId . '&id=' . $pictureId);
                    exit();
                }


                $stmt = $conn->prepare("UPDATE pictures SET path = ? WHERE ID = ? AND creator = ?");
                $stmt->bind_param('sii', $new_picture_path, $pictureId, $creatorId);
                $stmt->execute();
            }
        }

        $stmt->close();
    }

    if (!empty($sharedUsers)) {
        require_once '../config.php';
        $conn = getDatabaseConnection();
        foreach ($sharedUsers as $sharedUser) {
            $stmt = $conn->prepare('SELECT * FROM users WHERE username = ?');
            $stmt->bind_param('s', $sharedUser);
            $stmt->execute();
            $result = $stmt->get_result();

            if ($result->num_rows > 0) {
                $row = $result->fetch_assoc();
                $sharedUserId = $row['id'];

                $stmt = $conn->prepare('SELECT * FROM users_pictures WHERE picture_id = ? AND user_id = ?');
                $stmt->bind_param('ii', $pictureId, $sharedUserId);
                $stmt->execute();
                $result = $stmt->get_result();
                if ($result->num_rows > 0) {
                    continue;
                }
                $stmt = $conn->prepare('INSERT INTO users_pictures (user_id, picture_id) VALUES (?, ?)');
                $stmt->bind_param('ii', $sharedUserId, $pictureId);
                $stmt->execute();
            }
        }
    }

    $conn->close();
    header('Location: documents.php');
    exit();
}

?>