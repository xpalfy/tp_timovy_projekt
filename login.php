<?php
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

use Firebase\JWT\JWT;
use Firebase\JWT\Key;

require_once 'vendor/autoload.php';
require_once 'config.php';
require_once 'checkType.php';


function loginUser($username, $password): void {
    $conn = getDatabaseConnection();
    $stmt = $conn->prepare('SELECT id, username, password FROM users WHERE username = ?');
    if (!$stmt) {
        $_SESSION['toast'] = ['type' => 'error', 'message' => 'Database error: Unable to prepare statement'];
        header('Location: login.php');
        exit();
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

$client = new Google\Client();
try {
    $client->setAuthConfig('./Google/credentials.json');
} catch (\Google\Exception $e) {
    // Handle exception or logging
}

$redirect_uri = "https://test.tptimovyprojekt.software/tp_timovy_projekt/Google/redirect.php";
$client->setRedirectUri($redirect_uri);
$client->addScope("email");
$client->addScope("profile");

$auth_url = $client->createAuthUrl();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = $_POST['username'] ?? null;
    $password = $_POST['password'] ?? null;

    if ($username && $password) {
        try {
            loginUser($username, $password);
        } catch (Exception $e) {
            $_SESSION['toast'] = ['type' => 'error', 'message' => 'An error occurred: ' . $e->getMessage()];
            header('Location: login.php');
        }
    } else {
        $_SESSION['toast'] = ['type' => 'error', 'message' => 'Invalid request: Missing username or password'];
        header('Location: login.php');
    }
    exit();
}

$toast = $_SESSION['toast'] ?? null;
unset($_SESSION['toast']);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.css">
    <link rel="stylesheet" href="css/index.css">
    <link rel="stylesheet" href="css/login.css">
    <script src="https://code.jquery.com/jquery-3.5.1.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.js"></script>
</head>
<body>

<script>
    $(document).ready(function () {
        let toast = <?php echo json_encode($toast); ?>;
        if (toast) {
            toastr[toast.type](toast.message);
        }
    });
</script>

<nav class="navbar navbar-expand-lg navbar-dark bg-dark">
    <a class="navbar-brand" href="./index.html">Home</a>
    <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarNav"
            aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
        <span class="navbar-toggler-icon"></span>
    </button>
    <div class="collapse navbar-collapse" id="navbarNav">
        <ul class="navbar-nav ml-auto">
            <li class="nav-item"><a class="nav-link" href="./login.php">Login</a></li>
            <li class="nav-item"><a class="nav-link" href="./register.php">Register</a></li>
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
            <button type="submit" class="btn btn-primary btn-block">Log In</button>
        </form>

        <hr class="my-4">
        <a href="<?php echo htmlspecialchars($auth_url); ?>" class="btn btn-danger btn-block">Login with Google</a>
    </div>
</div>

<footer class="footer bg-dark text-center text-light py-3">
    © Project Site <a href="https://tptimovyprojekt.ddns.net/">tptimovyprojekt.ddns.net</a>
</footer>

</body>
</html>
