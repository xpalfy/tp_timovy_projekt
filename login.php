<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Login - HandScript</title>

  <!-- Tailwind CSS -->
  <script src="https://cdn.tailwindcss.com"></script>
  <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;600;700&display=swap" rel="stylesheet">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.css">
  <link href="https://unpkg.com/aos@2.3.4/dist/aos.css" rel="stylesheet" />

  <style>
    body {
      font-family: 'Inter', sans-serif;
      background: url('img/login.png') no-repeat center center fixed;
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
  </style>
</head>

<body class="text-[#3b2f1d] bg-[#fefbf5]">

  <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
  <script src="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.js"></script>
  <script src="https://unpkg.com/aos@2.3.4/dist/aos.js"></script>
  <script>
    $(document).ready(function () {
      let toast = <?php echo json_encode($toast); ?>;
      if (toast) {
        toastr[toast.type](toast.message);
      }
      AOS.init({ duration: 1000, once: true });
    });
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

  <!-- Login Section -->
  <main class="flex-grow flex items-center justify-center px-4">
    <div class="glass p-10 w-full max-w-md my-10" data-aos="fade-up">
      <h2 class="text-3xl font-bold text-center mb-6">Login to HandScript</h2>
      <form action="login.php" method="POST" class="space-y-4">
        <div>
          <label for="username" class="block mb-1 font-medium">Username</label>
          <input type="text" id="username" name="username" class="w-full p-3 border rounded focus:outline-none focus:ring focus:ring-yellow-400" required>
        </div>
        <div>
          <label for="password" class="block mb-1 font-medium">Password</label>
          <input type="password" id="password" name="password" autocomplete="off" class="w-full p-3 border rounded focus:outline-none focus:ring focus:ring-yellow-400" required>
        </div>
        <button type="submit" class="btn-papyrus w-full py-3 mt-4 rounded font-semibold">Log In</button>
      </form>

      <a href="<?php echo htmlspecialchars($auth_url); ?>" class="w-full flex items-center justify-center mt-4 gap-3 py-3 bg-white border border-gray-300 rounded hover:shadow-md transition">
        <img src="https://developers.google.com/identity/images/g-logo.png" alt="Google Logo" class="w-5 h-5">
        <span class="text-sm font-medium text-gray-800">Sign in with Google</span>
      </a>
    </div>
  </main>

  <!-- Footer -->
  <footer class="bg-[#d7c7a5] text-center py-6 border-t border-yellow-300 text-[#3b2f1d]">
    &copy; 2025 HandScript — <a href="https://tptimovyprojekt.ddns.net/" class="underline">Visit Project Page</a>
  </footer>

</body>
</html>