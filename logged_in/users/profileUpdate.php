<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

require '../../checkType.php';
require_once '../../config.php';

try {
    $userData = validateToken();
} catch (Exception $e) {
    http_response_code(500);
    $_SESSION['toast'] = ['type' => 'error', 'message' => 'Token validation failed'];
    header('Location: ../login.php');
}


function isAlreadyUser($conn, $username): void
{
    $stmt = $conn->prepare('SELECT id FROM users WHERE username = ?');
    $stmt->bind_param('s', $username);
    $stmt->execute();
    $stmt->store_result();
    if ($stmt->num_rows > 0) {
        $stmt->close();
        $conn->close();
        header('Location: ../profile.php');
        $_SESSION['toast'] = ['type' => 'error', 'message' => 'User with this username already exists.'];
        exit();
    }
}

function isEmailUsed($conn, $email): void
{
    $stmt = $conn->prepare('SELECT id FROM users WHERE email = ?');
    $stmt->bind_param('s', $email);
    $stmt->execute();
    $stmt->store_result();
    if ($stmt->num_rows > 0) {
        $stmt->close();
        $conn->close();
        header('Location: ../profile.php');
        $_SESSION['toast'] = ['type' => 'error', 'message' => 'Email address already in use.'];
        exit();
    }
}

function updateIsVerified($conn, $username): void
{
    $stmt = $conn->prepare('UPDATE users SET is_verified = 0 WHERE username = ?');
    $stmt->bind_param('s', $username);
    $stmt->execute();
}

function UpdateUser($username, $new_username, $password, $email): void
{
    $conn = getDatabaseConnection();

    $query = 'UPDATE users SET ';
    $params = [];
    $types = '';

    if ($new_username !== '') {
        isAlreadyUser($conn, $new_username);
        $query .= 'username = ?, ';
        $params[] = $new_username;
        $types .= 's';
    }
    if ($email !== '') {
        isEmailUsed($conn, $email);
        $query .= 'email = ?, ';
        $params[] = $email;
        $types .= 's';
    }
    if ($password !== '') {
        $hashedPassword = password_hash($password, PASSWORD_DEFAULT);
        $query .= 'password = ?, ';
        $params[] = $hashedPassword;
        $types .= 's';
    }

    $query = rtrim($query, ', ') . ' WHERE username = ?';
    $params[] = $username;
    $types .= 's';

    $stmt = $conn->prepare($query);
    if (!$stmt) {
        die("Statement preparation failed: " . $conn->error);
    }

    $stmt->bind_param($types, ...$params);

    if (!$stmt->execute()) {
        die("Execution failed: " . $stmt->error);
    }

    updateIsVerified($conn, $new_username);

    echo "Update successful!";
    $stmt->close();
    $conn->close();
}


if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = $userData['username'];
    $new_username = filter_input(INPUT_POST, 'username', FILTER_UNSAFE_RAW);
    $password = filter_input(INPUT_POST, 'password', FILTER_UNSAFE_RAW);
    $email = filter_input(INPUT_POST, 'email', FILTER_UNSAFE_RAW);
    $confirmPassword = filter_input(INPUT_POST, 'password_confirm', FILTER_UNSAFE_RAW);

    if ($new_username === '' && $password === '' && $email === '') {
        $_SESSION['toast'] = ['type' => 'error', 'message' => 'No fields were updated.'];
        header('Location: ../profile.php');
        exit();
    }

    if ($password !== '' && $confirmPassword === '' || $password === '' && $confirmPassword !== '') {
        $_SESSION['toast'] = ['type' => 'error', 'message' => 'Please fill out both password tiles.'];
        header('Location: ../profile.php');
        exit();
    }

    if ($password !== $confirmPassword && $password !== '' && $confirmPassword !== '') {
        $_SESSION['toast'] = ['type' => 'error', 'message' => 'Passwords do not match.'];
        header('Location: ../profile.php');
        exit();
    }

    if (!is_string($new_username) || strlen($new_username) > 255 && $new_username !== '') {
        $_SESSION['toast'] = ['type' => 'error', 'message' => 'Username must be less than 256 characters.'];
        header('Location: ../profile.php');
        exit();
    }

    if (strlen($password) < 8 && $password !== '') {
        $_SESSION['toast'] = ['type' => 'error', 'message' => 'Password must be at least 8 characters long.'];
        header('Location: ../profile.php');
        exit();
    }

    if ($email !== '' && !filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $_SESSION['toast'] = ['type' => 'error', 'message' => 'Invalid email address.'];
        header('Location: ../profile.php');
        exit();
    }

    UpdateUser($username, $new_username, $password, $email);

    require_once '../../logout.php';
    exit();
}

?>