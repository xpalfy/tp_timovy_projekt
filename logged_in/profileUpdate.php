<?php
require_once '../config.php';
require '../checkType.php';

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

check();

function isAlreadyUser($conn, $username): void
{
    $stmt = $conn->prepare('SELECT id FROM users WHERE username = ?');
    $stmt->bind_param('s', $username);
    $stmt->execute();
    $stmt->store_result();
    if ($stmt->num_rows > 0) {
        $stmt->close();
        $conn->close();
        header('Location: profile.php');
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
        header('Location: profile.php');
        $_SESSION['toast'] = ['type' => 'error', 'message' => 'Email address already in use.'];
        exit();
    }
}

function UpdateUser($username, $new_username, $password, $email): void
{
    $conn = getDatabaseConnection();

    // Base query
    $query = 'UPDATE users SET ';
    $params = [];
    $types = '';

    // Add fields based on input
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

    // Remove trailing comma and space, and add WHERE clause
    $query = rtrim($query, ', ') . ' WHERE username = ?';
    $params[] = $username;
    $types .= 's';

    // Prepare and bind the statement
    $stmt = $conn->prepare($query);
    if (!$stmt) {
        die("Statement preparation failed: " . $conn->error);
    }

    // Dynamically bind parameters
    $stmt->bind_param($types, ...$params);

    // Execute the query and handle errors
    if (!$stmt->execute()) {
        die("Execution failed: " . $stmt->error);
    }

    echo "Update successful!";
    $stmt->close();
    $conn->close();
}


if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = $_SESSION['user']['username'];
    $new_username = filter_input(INPUT_POST, 'username', FILTER_UNSAFE_RAW);
    $password = filter_input(INPUT_POST, 'password', FILTER_UNSAFE_RAW);
    $email = filter_input(INPUT_POST, 'email', FILTER_UNSAFE_RAW);
    $confirmPassword = filter_input(INPUT_POST, 'password_confirm', FILTER_UNSAFE_RAW);

    if($new_username === '' && $password === '' && $email === ''){
        $_SESSION['toast'] = ['type' => 'error', 'message' => 'No fields were updated.'];
        header('Location: profile.php');
        exit();
    }

    if($password !== '' && $confirmPassword === '' || $password === '' && $confirmPassword !== ''){
        $_SESSION['toast'] = ['type' => 'error', 'message' => 'Please fill out both password tiles.'];
        header('Location: profile.php');
        exit();
    }

    if ($password !== $confirmPassword && $password !== '' && $confirmPassword !== '') {
        $_SESSION['toast'] = ['type' => 'error', 'message' => 'Passwords do not match.'];
        header('Location: profile.php');
        exit();
    }

    if (!is_string($new_username) || strlen($new_username) > 255 && $new_username !== '') {
        $_SESSION['toast'] = ['type' => 'error', 'message' => 'Username must be less than 256 characters.'];
        header('Location: profile.php');
        exit();
    }

    if (strlen($password) < 8 && $password !== '') {
        $_SESSION['toast'] = ['type' => 'error', 'message' => 'Password must be at least 8 characters long.'];
        header('Location: profile.php');
        exit();
    }

    if ($email !== '' && !filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $_SESSION['toast'] = ['type' => 'error', 'message' => 'Invalid email address.'];
        header('Location: profile.php');
        exit();
    }

    UpdateUser($username, $new_username, $password, $email);
    if ($new_username !== ''){
        $_SESSION['user']['username'] = $new_username;
    }
    $_SESSION['toast'] = ['type' => 'success', 'message' => 'Update successful!'];
    header('Location: profile.php');
    exit();
}

?>