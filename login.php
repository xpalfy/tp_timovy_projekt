<?php
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

use Firebase\JWT\JWT;
use Firebase\JWT\Key;

require_once 'config.php';
require_once 'vendor/autoload.php';

function getJwtSecret(): string {
    $config = include 'jwt.php';
    if (!isset($config['secret']) || empty($config['secret'])) {
        throw new Exception('JWT secret is not configured');
    }
    return $config['secret'];
}

function generateToken($userId, $username): string {
    $secret = getJwtSecret();
    $payload = [
        'iss' => 'https://test.tptimovyprojekt.software/xpalfy', 
        'aud' => 'https://test.tptimovyprojekt.software/xpalfy',
        'iat' => time(),                                         
        'exp' => time() + 3600,                             
        'data' => [
            'id' => $userId,
            'username' => $username,
        ],
    ];

    return JWT::encode($payload, $secret, 'HS256');
}

function loginUser($username, $password): array {
    $conn = getDatabaseConnection();
    $stmt = $conn->prepare('SELECT id, username, password FROM users WHERE username = ?');
    if (!$stmt) {
        $_SESSION['toast'] = ['type' => 'error', 'message' => 'Database error: Unable to prepare statement'];
    }

    $stmt->bind_param('s', $username);
    $stmt->execute();
    $stmt->store_result();

    if ($stmt->num_rows === 1) {
        $stmt->bind_result($id, $fetchedUsername, $hash);
        $stmt->fetch();
        if (password_verify($password, $hash)) {
            $token = generateToken($id, $fetchedUsername);
            $_SESSION['toast'] = ['type' => 'success', 'message' => 'Login successful!'];
            $_SESSION['token'] = $token;
            header('Location: ./logged_in/main.php');
        } else {
            $_SESSION['toast'] = ['type' => 'error', 'message' => 'Invalid password!'];
            header('Location: login.php');
        }
    } else {
        $_SESSION['toast'] = ['type' => 'error', 'message' => 'Invalid username!'];
        header('Location: login.php');
    }
    $stmt->close();
    $conn->close();
}

$response = null;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = $_POST['username'] ?? null;
    $password = $_POST['password'] ?? null;

    if ($username && $password) {
        try {
            $response = loginUser($username, $password);
        } catch (Exception $e) {
            $_SESSION['toast'] = ['type' => 'error', 'message' => 'An error occurred: ' . $e->getMessage()];
            header('Location: login.php');
        }
    } else {
        $_SESSION['toast'] = ['type' => 'error', 'message' => 'Invalid request: Missing username or password'];
        header('Location: login.php');
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Login</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.css">
    <link rel="stylesheet" href="css/index.css">
    <link rel="stylesheet" href="css/login.css">
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.js"></script>
</head>
<body>
<script>
    function checkToasts() {
        let toast = <?php echo json_encode($_SESSION['toast'] ?? null); ?>;
        if (toast) {
            toastr[toast.type](toast.message);
            <?php unset($_SESSION['toast']); ?>
        }
    }

    checkToasts();
</script>

<nav class="navbar navbar-expand-lg navbar-dark bg-dark">
    <a class="navbar-brand" href="./index.html">Home</a>
    <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarNav"
            aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
        <span class="navbar-toggler-icon"></span>
    </button>
    <div class="collapse navbar-collapse" id="navbarNav">
        <ul class="navbar-nav ml-auto">
            <li class="nav-item">
                <a class="nav-link" href="./login.php">Login</a>
            </li>
            <li class="nav-item">
                <a class="nav-link" href="./register.php">Register</a>
            </li>
        </ul>
    </div>
</nav>

<div class="container cont mb-5">
    <div class="form-container">
        <h3 class="mb-3 text-center">Login</h3>
        <form action="login.php" method="POST">
            <div class="form-group">
                <label for="username">Username</label>
                <input type="text" class="form-control" id="username" name="username" required>
            </div>
            <div class="form-group">
                <label for="password">Password</label>
                <input type="password" class="form-control" id="password" name="password" autocomplete="off" required>
            </div>
            <button type="submit" class="btn btn-primary">Log In</button>
        </form>
    </div>
</div>

<footer class="footer bg-dark">
    © Project Site <a href="https://test.tptimovyprojekt.software/xpalfy/">tptimovyprojekt.software</a>
</footer>
</body>
</html>
