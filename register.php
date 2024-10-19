<?php
require_once 'config.php';
if (session_status() === PHP_SESSION_NONE) {
    session_start();
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
        header('Location: register.php');
        $_SESSION['toast'] = ['type' => 'error', 'message' => 'User with this username already exists.'];
        exit();
    }
}

function createUser($username, $password, $email): void
{
    $conn = getDatabaseConnection();
    isAlreadyUser($conn, $username);
    $stmt = $conn->prepare('INSERT INTO users (username, email, password) VALUES (?, ?, ?)');
    $stmt->bind_param('sss', $username, $email, $password);
    $stmt->execute();
    $stmt->close();
    $conn->close();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = filter_input(INPUT_POST, 'username', FILTER_UNSAFE_RAW);
    $password = filter_input(INPUT_POST, 'password', FILTER_UNSAFE_RAW);
    $email = filter_input(INPUT_POST, 'email', FILTER_UNSAFE_RAW);
    $confirmPassword = filter_input(INPUT_POST, 'password_confirm', FILTER_UNSAFE_RAW);

    if ($password !== $confirmPassword) {
        $_SESSION['toast'] = ['type' => 'error', 'message' => 'Passwords do not match.'];
        header('Location: register.php');
        exit();
    }

    if (!is_string($username) || strlen($username) > 255) {
        $_SESSION['toast'] = ['type' => 'error', 'message' => 'Username must be less than 256 characters.'];
        header('Location: register.php');
        exit();
    }

    if (strlen($password) < 8) {
        $_SESSION['toast'] = ['type' => 'error', 'message' => 'Password must be at least 8 characters long.'];
        header('Location: register.php');
        exit();
    }

    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $_SESSION['toast'] = ['type' => 'error', 'message' => 'Invalid email address.'];
        header('Location: register.php');
        exit();
    }

    $hashedPassword = password_hash($password, PASSWORD_DEFAULT);
    createUser($username, $hashedPassword, $email);

    $_SESSION['toast'] = ['type' => 'success', 'message' => 'Registration successful! Please login.'];
    header('Location: login.php');
    exit();
}


?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Register</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.css">
    <link rel="stylesheet" href="css/index.css">
    <link rel="stylesheet" href="css/register.css">
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
                <a class="nav-link" href="https://tptimovyprojekt.ddns.net/">Project</a>
            </li>
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
        <h3 class="mb-3 text-center">Register</h3>
        <form action="register.php" method="POST">
            <div class="form-group">
                <label for="username">Username</label>
                <input type="text" class="form-control" id="username" name="username" required>
            </div>
            <div class="form-group">
                <label for="email">Email</label>
                <input type="email" class="form-control" id="email" name="email" required>
            </div>
            <div class="form-group">
                <label for="password">Password</label>
                <input type="password" class="form-control" id="password" name="password" required>
            </div>
            <div class="form-group">
                <label for="password_confirm">Confirm Password</label>
                <input type="password" class="form-control" id="password_confirm" name="password_confirm" required>
            </div>
            <button type="submit" class="btn btn-primary">Register</button>
        </form>
    </div>
</div>


<footer class="footer bg-dark">
    © Project Site <a href="https://tptimovyprojekt.ddns.net/">tptimovyprojekt.ddns.net</a>
</footer>
</body>
</html>