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

function isEmailUsed($conn, $email): void
{
    $stmt = $conn->prepare('SELECT id FROM users WHERE email = ?');
    $stmt->bind_param('s', $email);
    $stmt->execute();
    $stmt->store_result();
    if ($stmt->num_rows > 0) {
        $stmt->close();
        $conn->close();
        header('Location: register.php');
        $_SESSION['toast'] = ['type' => 'error', 'message' => 'Email address already in use.'];
        exit();
    }
}

function createUser($username, $password, $email): void
{
    $conn = getDatabaseConnection();
    isAlreadyUser($conn, $username);
    isEmailUsed($conn, $email);
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
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Register - HandScript</title>

  <!-- Tailwind CSS -->
  <script src="https://cdn.tailwindcss.com"></script>
  <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;600;700&display=swap" rel="stylesheet">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.css">
  <link href="https://unpkg.com/aos@2.3.4/dist/aos.css" rel="stylesheet" />

  <style>
    body {
      font-family: 'Inter', sans-serif;
      background: url('img/register.png') no-repeat center center fixed;
      background-size: cover;
      min-height: 100vh;
      display: flex;
      flex-direction: column;
    }

    .glass {
      background: rgba(255, 255, 255, 0.6);
      backdrop-filter: blur(10px);
      border-radius: 1rem;
      box-shadow: 0 10px 25px rgba(0, 0, 0, 0.1);
    }

    .btn-papyrus {
      background-color: #bfa97a;
      color: #3b2f1d;
    }

    .btn-papyrus:hover {
      background-color: #a68f68;
    }

    .is-valid {
      border-color: #16a34a !important; /* green */
    }

    .is-invalid {
      border-color: #dc2626 !important; /* red */
    }

    .text-success {
      color: #16a34a;
      font-size: 0.875rem;
      margin-top: 0.25rem;
    }

    .text-danger {
      color: #dc2626;
      font-size: 0.875rem;
      margin-top: 0.25rem;
    }

    input:focus {
      outline: none;
      box-shadow: none;
    }
  </style>
</head>

<body class="text-[#3b2f1d] bg-[#fefbf5]">

  <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
  <script src="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.js"></script>
  <script src="https://unpkg.com/aos@2.3.4/dist/aos.js"></script>
  <script src="./js/regex.js"></script>
  <script>
    function checkToasts() {
        let toast = <?php echo json_encode($_SESSION['toast'] ?? null); ?>;
        if (toast) {
            toastr[toast.type](toast.message);
            <?php unset($_SESSION['toast']); ?>
        }
    }

    checkToasts();
    AOS.init({ duration: 1000, once: true });
  </script>

  <!-- Navbar -->
  <nav class="bg-[#d7c7a5] shadow-md sticky top-0 z-50">
    <div class="max-w-7xl mx-auto px-6 py-4 flex justify-between items-center">
      <a href="./index.html" class="text-2xl font-bold hover:underline">HandScript</a>
      <div class="hidden md:flex space-x-6 text-lg">
        <a href="./login.php" class="hover:underline">Login</a>
        <a href="./register.php" class="hover:underline">Register</a>
      </div>
    </div>
  </nav>

  <!-- Register Section -->
  <main class="flex-grow flex items-center justify-center px-4">
    <div class="glass p-10 w-full max-w-md my-10" data-aos="fade-up">
      <h2 class="text-3xl font-bold text-center mb-6">Create an Account</h2>
      <form action="register.php" method="POST" class="space-y-4">
        <div>
          <label for="username" class="block mb-1 font-medium">Username</label>
          <input type="text" id="username" name="username" oninput="isValidInput(this)" class="w-full p-3 border rounded" required>
        </div>
        <div>
          <label for="email" class="block mb-1 font-medium">Email</label>
          <input type="email" id="email" name="email" oninput="isValidEmail(this)" class="w-full p-3 border rounded" required>
        </div>
        <div>
          <label for="password" class="block mb-1 font-medium">Password</label>
          <input type="password" id="password" name="password" oninput="isValidPassword(this)" autocomplete="off" class="w-full p-3 border rounded" required>
        </div>
        <div>
          <label for="password_confirm" class="block mb-1 font-medium">Confirm Password</label>
          <input type="password" id="password_confirm" name="password_confirm" oninput="isValidPassword(this)" autocomplete="off" class="w-full p-3 border rounded" required>
        </div>
        <button type="submit" class="btn-papyrus w-full py-3 mt-4 rounded font-semibold">Register</button>
      </form>
    </div>
  </main>

  <!-- Footer -->
  <footer class="bg-[#d7c7a5] text-center py-6 border-t border-yellow-300 text-[#3b2f1d]">
    &copy; 2025 HandScript — <a href="https://tptimovyprojekt.ddns.net/" class="underline">Visit Project Page</a>
  </footer>

  <script>
    let form = document.querySelector('form');
    form.addEventListener('submit', checkForm);
  </script>

</body>
</html>