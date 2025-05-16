<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

require './checkType.php';

try {
    $userData = validateToken();
} catch (Exception $e) {
    $userData = null;
    session_unset();
    session_destroy();
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8"/>
    <meta name="viewport" content="width=device-width, initial-scale=1"/>
    <title>HandScript: Historical Document Processing</title>

    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://unpkg.com/flowbite@1.6.5/dist/flowbite.min.css" rel="stylesheet"/>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;600;700&display=swap" rel="stylesheet">
    <link href="https://unpkg.com/aos@2.3.4/dist/aos.css" rel="stylesheet"/>
    <link rel="stylesheet" href="./css/index.css"/>
</head>

<body class="text-[#3b2f1d]">

<!-- Navbar -->
<nav class="bg-[#d7c7a5] shadow sticky top-0 z-50">
  <div class="max-w-7xl mx-auto px-6 py-4 flex justify-between items-center">
    <a href="./index.php" class="text-2xl font-bold hover:underline">HandScript</a>
    <div class="hidden md:flex space-x-6 text-lg items-center">
      <a href="./index.php" class="hover:underline">Home</a>

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

      <!-- Account or Dashboard -->
      <?php if ($userData === null): ?>
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
      <?php else: ?>
        <!-- Dashboard Link -->
        <a href="./logged_in/main.php" class="hover:underline">Dashboard</a>
      <?php endif; ?>

    </div>
  </div>
</nav>

<!-- Hero -->
<header class="relative w-full h-[60vh] overflow-hidden mb-16">
    <img src="img/1.jpg" alt="Hero" class="w-full h-full object-cover"/>
    <div class="absolute inset-0 bg-black bg-opacity-40 flex justify-center items-center px-4">
        <div
                class="glass p-8 border border-white/20 text-[#f8f6f1] text-center w-full max-w-2xl transition duration-300 hover:scale-105 hover:shadow-xl"
                style="background: rgba(255, 255, 255, 0.15); backdrop-filter: blur(10px);" data-aos="zoom-in">
            <h1 class="text-3xl md:text-4xl font-bold mb-3">Decipher the Past with AI</h1>
            <p class="text-base md:text-lg mb-3">Upload, analyze, and decrypt historical manuscripts like never
                before.</p>
            <div>
                <div id="time" class="text-xl font-medium"></div>
                <div id="date" class="text-sm mt-1 opacity-90"></div>
            </div>
        </div>
    </div>
</header>

<!-- About Section -->
<section class="max-w-6xl mx-auto px-6 mb-20">
    <div class="grid md:grid-cols-2 gap-12 items-center">
        <div class="glass p-6 card-hover" data-aos="fade-right">
            <h2 class="text-3xl font-bold mb-4">About the Project</h2>
            <p class="mb-4">HandScript equips researchers, students, and hobbyists with modern tools to digitize and
                decrypt
                ancient texts. It blends AI handwriting analysis with cipher solving and text reconstruction.</p>
            <p class="mb-4">Our mission is to preserve history, unlock secrets, and make manuscript analysis engaging
                and
                collaborative. Whether you're analyzing a cryptic letter from the 18th century or trying to decode a
                family
                heirloom, HandScript provides you the tools to explore it intelligently.</p>
            <p>We aim to bridge the gap between traditional historical research and modern technology by offering a
                user-friendly platform that supports learning, discovery, and preservation at every step.</p>
        </div>
        <div class="flex justify-center" data-aos="fade-left">
            <div class="glass p-4 rounded-xl image-card">
                <img src="img/1_1.png" alt="Encrypted Example" class="rounded-lg max-w-xs h-auto"/>
            </div>
        </div>
    </div>
</section>

<!-- Features Section -->
<section class="max-w-6xl mx-auto px-6 mb-20">
    <div class="grid md:grid-cols-2 gap-12 items-center">
        <div class="flex justify-center order-2 md:order-1" data-aos="fade-right">
            <div class="glass p-4 rounded-xl image-card">
                <img src="img/1_2.png" alt="Cipher" class="rounded-lg max-w-xs h-auto"/>
            </div>
        </div>
        <div class="glass p-6 order-1 md:order-2 card-hover" data-aos="fade-left">
            <h2 class="text-3xl font-bold mb-4">AI + Cryptography</h2>
            <p class="mb-4">Our neural network deciphers handwritten glyphs and helps you reconstruct texts using
                substitution keys, pattern matching, or brute-force strategies.</p>
            <p class="mb-4">By combining machine learning with traditional cryptographic approaches, we enable a more
                dynamic and adaptable method of decryption. This allows users to visualize results, apply custom logic,
                and
                get recommendations based on evolving AI models.</p>
            <p>The integration of AI with classical methods opens up new pathways for education, research, and
                exploration
                in both historical and fictional cipher studies.</p>
        </div>
    </div>
</section>

<!-- How It Works -->
<section class="max-w-5xl mx-auto px-6 mb-24 text-center" data-aos="fade-up">
    <h2 class="text-3xl font-bold mb-10">How It Works</h2>
    <div class="grid md:grid-cols-3 gap-6">
        <div class="glass p-6 rounded-xl shadow card-hover" data-aos="zoom-in">
            <h3 class="text-xl font-semibold mb-2">📤 Upload</h3>
            <p>Drop your scanned manuscript or cipher into our secure interface.</p>
        </div>
        <div class="glass p-6 rounded-xl shadow card-hover" data-aos="zoom-in" data-aos-delay="100">
            <h3 class="text-xl font-semibold mb-2">🧠 Analyze</h3>
            <p>We segment and classify handwritten characters using machine learning.</p>
        </div>
        <div class="glass p-6 rounded-xl shadow card-hover" data-aos="zoom-in" data-aos-delay="200">
            <h3 class="text-xl font-semibold mb-2">🔓 Decrypt</h3>
            <p>Choose cipher strategies or input your own key to reveal the hidden message.</p>
        </div>
    </div>
</section>

<?php if ($userData === null): ?>
<!-- Login/Register CTA -->
<section class="max-w-3xl mx-auto px-6 mb-24 text-center" data-aos="fade-up">
    <div class="glass p-8 rounded-2xl border border-yellow-300 shadow-md card-hover">
        <h2 class="text-3xl font-bold mb-4">Start Your Decryption Journey</h2>
        <p class="mb-6 text-lg">Log in or create an account to unlock full access: upload, analyze, and preserve your
            historical documents.</p>
        <div class="flex justify-center gap-6">
            <a href="./login.php" class="btn-papyrus px-6 py-2 text-lg rounded-lg shadow hover:scale-105 transition">🔐
                Login</a>
            <a href="./register.php" class="btn-papyrus px-6 py-2 text-lg rounded-lg shadow hover:scale-105 transition">📝
                Register</a>
        </div>
    </div>
</section>
<?php endif; ?>

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
<!-- Cookie Consent Modal -->
<div id="cookieConsentModal" class="fixed inset-0 bg-black bg-opacity-70 flex justify-center items-center z-50 hidden">
    <div class="bg-[#fff8e1] rounded-lg shadow-xl max-w-md p-6 text-[#3b2f1d] text-center space-y-4 mx-4">
        <h2 class="text-xl font-semibold">🍪 We Use Cookies</h2>
        <p>This website uses cookies to ensure you get the best experience on our platform.</p>
        <button onclick="acceptCookies()"
                class="mt-4 bg-[#d7c7a5] hover:bg-[#cbbd99] text-[#3b2f1d] font-semibold py-2 px-4 rounded transition">
            Accept Cookies
        </button>
    </div>
</div>


<!-- Clock Script -->
<script>
    function updateClock() {
        const now = new Date();
        document.getElementById("time").innerText = now.toLocaleTimeString();
        document.getElementById("date").innerText = now.toLocaleDateString(undefined, {
            year: 'numeric',
            month: 'long',
            day: 'numeric'
        });
    }

    function setCookie(name, value, days) {
        const expires = new Date(Date.now() + days * 864e5).toUTCString();
        document.cookie = name + '=' + encodeURIComponent(value) + '; expires=' + expires + '; path=/';
    }

    function getCookie(name) {
        return document.cookie.split('; ').find(row => row.startsWith(name + '='))?.split('=')[1];
    }

    function acceptCookies() {
        setCookie('cookiesAccepted', 'yes', 30);
        document.getElementById('cookieConsentModal').classList.add('hidden');
        document.body.classList.remove('overflow-hidden'); 
    }

    window.addEventListener('DOMContentLoaded', () => {
        if (!getCookie('cookiesAccepted')) {
            document.getElementById('cookieConsentModal').classList.remove('hidden');
            document.body.classList.add('overflow-hidden')
        }
        updateClock();
        setInterval(updateClock, 1000);
    });
</script>

</script>

<!-- AOS Init -->
<script src="https://unpkg.com/aos@2.3.4/dist/aos.js"></script>
<script>
    AOS.init({duration: 1000, once: true});
</script>
<!-- Flowbite Script -->
<script src="https://unpkg.com/flowbite@1.6.5/dist/flowbite.min.js"></script>

</body>

</html>