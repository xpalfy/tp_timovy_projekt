<?php session_start(); ?>
<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Login - HandScript</title>

  <script src="https://cdn.tailwindcss.com"></script>
  <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;600;700&display=swap" rel="stylesheet">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.css">
  <link href="https://unpkg.com/aos@2.3.4/dist/aos.css" rel="stylesheet" />
  <link href="https://unpkg.com/flowbite@1.6.5/dist/flowbite.min.css" rel="stylesheet" />
  <link rel="stylesheet" href="./css/login.css?v=<?php echo time(); ?>" />
</head>

<body class="text-[#3b2f1d] bg-[#fefbf5]">

  <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
  <script src="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.js"></script>
  <script src="https://unpkg.com/aos@2.3.4/dist/aos.js"></script>
  <script>
    function checkToasts() {
        let toast = <?php echo json_encode($_SESSION['toast'] ?? null); ?>;
        if (toast) {
            toastr[toast.type](toast.message);
            <?php unset($_SESSION['toast']); ?>
        }
    }
    
    AOS.init({ duration: 1000, once: true });
    checkToasts();
  </script>

<!-- Navbar -->
<nav class="bg-[#d7c7a5] shadow sticky top-0 z-50">
  <div class="max-w-7xl mx-auto px-6 py-4 flex justify-between items-center">
    <a href="./index.php" class="text-2xl font-bold hover:underline">HandScript</a>
    <div class="hidden md:flex space-x-6 text-lg items-center">
      <!-- Docs Dropdown -->
      <div class="relative">
        <button id="docsDropdownButton" data-dropdown-toggle="docsDropdown"
          class="hover:underline flex items-center">
          Resources
          <svg class="w-2.5 h-2.5 ml-2.5" aria-hidden="true" fill="none" viewBox="0 0 10 6">
            <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
              d="M1 1l4 4 4-4" />
          </svg>
        </button>
        <div id="docsDropdown"
          class="z-10 hidden font-normal bg-[#d7c7a5] divide-y divide-gray-100 rounded-lg shadow w-44">
          <ul class="py-2 text-sm text-[#3b2f1d]" aria-labelledby="docsDropdownButton">
            <li>
              <a href="https://tptimovyprojekt.ddns.net/" class="block px-4 py-2 hover:bg-[#cbbd99]">Minutes</a>
            </li>
            <li>
              <a href="https://python.tptimovyprojekt.software/apidocs/" class="block px-4 py-2 hover:bg-[#cbbd99]">Swagger</a>
            </li>
          </ul>
        </div>
      </div>

      <!-- Project Dropdown -->
      <div class="relative">
        <button id="projectDropdownButton" data-dropdown-toggle="projectDropdown"
          class="hover:underline flex items-center">
          Project
          <svg class="w-2.5 h-2.5 ml-2.5" aria-hidden="true" fill="none" viewBox="0 0 10 6">
            <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
              d="M1 1l4 4 4-4" />
          </svg>
        </button>
        <div id="projectDropdown"
          class="z-10 hidden font-normal bg-[#d7c7a5] divide-y divide-gray-100 rounded-lg shadow w-44">
          <ul class="py-2 text-sm text-[#3b2f1d]" aria-labelledby="projectDropdownButton">
            <li>
              <a href="./task.php" class="block px-4 py-2 hover:bg-[#cbbd99]">Description</a>
            </li>
            <li>
              <a href="./team.php" class="block px-4 py-2 hover:bg-[#cbbd99]">Team Members</a>
            </li>
            <li>
              <a href="./faq.php" class="block px-4 py-2 hover:bg-[#cbbd99]">FAQ</a>
            </li>
          </ul>
        </div>
      </div>

      <!-- Account Dropdown -->
      <div class="relative">
        <button id="accountDropdownButton" data-dropdown-toggle="accountDropdown"
          class="hover:underline flex items-center">
          Account
          <svg class="w-2.5 h-2.5 ml-2.5" aria-hidden="true" fill="none" viewBox="0 0 10 6">
            <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
              d="M1 1l4 4 4-4" />
          </svg>
        </button>
        <div id="accountDropdown"
          class="z-10 hidden font-normal bg-[#d7c7a5] divide-y divide-gray-100 rounded-lg shadow w-44">
          <ul class="py-2 text-sm text-[#3b2f1d]" aria-labelledby="accountDropdownButton">
            <li>
              <a href="./login.php" class="block px-4 py-2 hover:bg-[#cbbd99]">Login</a>
            </li>
            <li>
              <a href="./register.php" class="block px-4 py-2 hover:bg-[#cbbd99]">Register</a>
            </li>
          </ul>
        </div>
      </div>
    </div>
  </div>
</nav>

  <!-- Login Section -->
  <main class="flex-grow flex items-center justify-center px-4">
    <div class="glass p-10 w-full max-w-md my-10" data-aos="fade-down">
      <h2 class="text-3xl font-bold text-center mb-6">Login to HandScript</h2>
      <form id="loginForm" class="space-y-4">
        <div>
          <label for="username" class="block mb-1 font-medium">Username</label>
          <input type="text" id="username" name="username" class="w-full p-3 border rounded" required>
        </div>
        <div>
          <label for="password" class="block mb-1 font-medium">Password</label>
          <input type="password" id="password" name="password" autocomplete="off" class="w-full p-3 border rounded"
            required>
        </div>
        <button type="submit" class="btn-papyrus w-full py-3 mt-4 rounded font-semibold">Log In</button>
      </form>

      <a href="<?php
      require_once 'vendor/autoload.php';
      $client = new Google\Client();
      $client->setAuthConfig('./Google/credentials.json');
      $client->setRedirectUri("https://test.tptimovyprojekt.software/tp_timovy_projekt/Google/redirect.php");
      $client->addScope("email");
      $client->addScope("profile");
      echo htmlspecialchars($client->createAuthUrl());
      ?>"
        class="w-full flex items-center justify-center mt-4 gap-3 py-3 bg-white border border-gray-300 rounded hover:shadow-md transition">
        <img src="https://developers.google.com/identity/images/g-logo.png" alt="Google Logo" class="w-5 h-5">
        <span class="text-sm font-medium text-gray-800">Sign in with Google</span>
      </a>
    </div>
  </main>

  <!-- Verification Modal -->
  <div id="loginVerificationModal"
    class="fixed inset-0 bg-black bg-opacity-80 hidden flex items-center justify-center z-50">
    <div class="bg-white rounded-lg p-6 w-full max-w-sm modal-animate-in">
      <h3 class="text-xl font-semibold mb-4">Verify your email</h3>
      <p class="mb-3 text-sm text-gray-700">We've sent a verification code to your email. Please enter it below.</p>
      <form id="verifyForm" class="space-y-4">
        <input type="hidden" name="verify_email" id="verifyEmail">
        <input type="text" name="verify_code" id="verifyCode" class="w-full p-3 border rounded"
          oninput="isValidCode(this)" autocomplete="off" placeholder="Enter verification code" required>
        <button type="submit" class="btn-papyrus w-full py-2 rounded font-semibold">Verify</button>
      </form>
    </div>
  </div>

<!-- Footer -->
<footer class="bg-[#d7c7a5] border-t border-yellow-300 text-[#3b2f1d] py-6">
    <div class="max-w-7xl mx-auto px-4 flex flex-col md:flex-row justify-between items-center space-y-4 md:space-y-0">
        <p class="text-center md:text-left">&copy; 2025 HandScript</p>
        <div class="flex space-x-4 text-sm">
            <a href="https://tptimovyprojekt.ddns.net/" class="underline hover:text-[#5a452e] transition">Visit Project
                Page</a>
            <a href="./faq.php" class="underline hover:text-[#5a452e] transition">FAQ</a>
        </div>
    </div>
</footer>

  <!-- JS Logic -->
  <script src="./js/regex.js?v=2"></script>
  <script>
    document.getElementById("loginForm").addEventListener("submit", async function (e) {
      e.preventDefault();

      const username = document.getElementById("username").value.trim();
      const password = document.getElementById("password").value;

      const res = await fetch("./cust_mang/ajax_login.php", {
        method: "POST",
        headers: { "Content-Type": "application/json" },
        body: JSON.stringify({ username, password })
      });

      const data = await res.json();
      if (data.success) {
        window.location.href = "./logged_in/main.php";
      } else if (data.unverified) {
        toastr.warning(data.message);
        document.getElementById("verifyEmail").value = data.email;
        document.getElementById("loginVerificationModal").classList.remove("hidden");
      } else {
        console.log(data);
        toastr.error(data.message);
      }
    });

    document.getElementById("verifyForm").addEventListener("submit", async function (e) {
      e.preventDefault();
      const form = e.target;
      if (!checkForm(form)) return;

      const email = document.getElementById("verifyEmail").value;
      const code = document.getElementById("verifyCode").value;

      const res = await fetch("./cust_mang/ajax_verify.php", {
        method: "POST",
        headers: { "Content-Type": "application/json" },
        body: JSON.stringify({ email, code })
      });

      const data = await res.json();
      if (data.success) {
        toastr.success(data.message);
        document.getElementById("loginVerificationModal").classList.add("hidden");
      } else {
        toastr.error(data.message);
      }
    });
  </script>
  <script src="https://unpkg.com/flowbite@1.6.5/dist/flowbite.min.js"></script>

</body>

</html>