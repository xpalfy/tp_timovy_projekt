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
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1" />
  <title>HandScript: Meet the Team</title>

  <script src="https://cdn.tailwindcss.com"></script>
  <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;600;700&display=swap" rel="stylesheet">
  <link href="https://unpkg.com/aos@2.3.4/dist/aos.css" rel="stylesheet" />
  <link href="https://unpkg.com/flowbite@1.6.5/dist/flowbite.min.css" rel="stylesheet" />
  <link rel="stylesheet" href="./css/team.css" />

</head>

<body class="text-[#3b2f1d]">

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
        <a href="./logout.php" class="hover:underline">Logout</a>
      <?php endif; ?>

    </div>
  </div>
</nav>


  <!-- Hero -->
  <header class="relative w-full h-[60vh] overflow-hidden mb-16">
    <img src="img/team_tp.jpg" alt="Hero" class="w-full h-full object-cover" />
    <div class="absolute inset-0 bg-black bg-opacity-40 flex justify-center items-center px-4">
      <div
        class="glass p-8 border border-white/20 text-[#f8f6f1] text-center w-full max-w-2xl transition duration-300 hover:scale-105 hover:shadow-xl"
        style="background: rgba(255, 255, 255, 0.15); backdrop-filter: blur(10px);" data-aos="zoom-in">
        <h1 class="text-3xl md:text-4xl font-bold mb-3">Meet Our Team</h1>
        <p class="text-base md:text-lg">The minds behind HandScript</p>
      </div>
    </div>
  </header>

  <!-- Introduction Section -->
  <section class="max-w-6xl mx-auto px-6 mb-16 text-center" data-aos="fade-up">
    <div class="glass p-8 rounded-2xl hover-shadow">
      <h2 class="text-3xl font-bold mb-4">About Us</h2>
      <p class="text-lg">We are a passionate group of Applied Informatics graduates from STU Bratislava, blending
        expertise in security systems and intelligent software solutions. Our team is dedicated to creating innovative
        applications that preserve historical heritage using modern technologies. Together, we strive for quality,
        creativity, and technological excellence.</p>
    </div>
  </section>

  <!-- Team Section -->
  <section class="max-w-6xl mx-auto px-6 mb-24">
    <div class="flex flex-wrap justify-center gap-12">

      <!-- Team Members -->
      <div class="glass p-6 hover-scale flex flex-col w-[320px] " data-aos="fade-up">
        <h2 class="text-2xl font-semibold mb-2 text-center">👤 Bc. Bence Both</h2>
        <p class="text-center mb-2 text-sm">Email: <a href="mailto:xbothb@stuba.sk"
            class="underline">xbothb@stuba.sk</a></p>
        <div class="text-left">
          <p><strong>Skills:</strong></p>
          <ul class="list-disc list-inside">
            <li>Expert: Python, Java, Linux</li>
            <li>Advanced: JavaScript, C, C++, SQL</li>
            <li>Experience: HTML, CSS, WordPress</li>
          </ul>
        </div>
      </div>

      <div class="glass p-6 hover-scale flex flex-col w-[320px] " data-aos="fade-up" data-aos-delay="100">
        <h2 class="text-2xl font-semibold mb-2 text-center">👤 Bc. Matyas Horváth</h2>
        <p class="text-center mb-2 text-sm">Email: <a href="mailto:xhorvathm2@stuba.sk"
            class="underline">xhorvathm2@stuba.sk</a></p>
        <div class="text-left">
          <p><strong>Skills:</strong></p>
          <ul class="list-disc list-inside">
            <li>Expert: Linux, Python, C#, .NET</li>
            <li>Advanced: HTML, CSS, JavaScript, Java</li>
            <li>Experience: JQuery, Bootstrap, C, C++</li>
          </ul>
        </div>
      </div>

      <div class="glass p-6 hover-scale flex flex-col w-[320px] " data-aos="fade-up" data-aos-delay="200">
        <h2 class="text-2xl font-semibold mb-2 text-center">👤 Bc. Jozef Nyitrai</h2>
        <p class="text-center mb-2 text-sm">Email: <a href="mailto:xnyitrai@stuba.sk"
            class="underline">xnyitrai@stuba.sk</a></p>
        <div class="text-left">
          <p><strong>Skills:</strong></p>
          <ul class="list-disc list-inside">
            <li>Expert: HTML, CSS, JavaScript, Linux, PHP</li>
            <li>Advanced: SQL, C, C++, Java, Python</li>
            <li>Experience: WordPress, JQuery, Bootstrap</li>
          </ul>
        </div>
      </div>

      <div class="glass p-6 hover-scale flex flex-col w-[320px] " data-aos="fade-up" data-aos-delay="300">
        <h2 class="text-2xl font-semibold mb-2 text-center">👤 Bc. Áron Tükör</h2>
        <p class="text-center mb-2 text-sm">Email: <a href="mailto:xtukor@stuba.sk"
            class="underline">xtukor@stuba.sk</a></p>
        <div class="text-left">
          <p><strong>Skills:</strong></p>
          <ul class="list-disc list-inside">
            <li>Expert: Python, HTML, JavaScript, PHP</li>
            <li>Advanced: CSS, Java, C, C++, Linux</li>
            <li>Experience: WordPress, JQuery, Bootstrap</li>
          </ul>
        </div>
      </div>

      <div class="glass p-6 hover-scale flex flex-col w-[320px] " data-aos="fade-up" data-aos-delay="400">
        <h2 class="text-2xl font-semibold mb-2 text-center">👤 Bc. Vincent Pálfy</h2>
        <p class="text-center mb-2 text-sm">Email: <a href="mailto:xpalfy@stuba.sk"
            class="underline">xpalfy@stuba.sk</a></p>
        <div class="text-left">
          <p><strong>Skills:</strong></p>
          <ul class="list-disc list-inside">
            <li>Expert: HTML, CSS, JavaScript, SQL, PHP</li>
            <li>Advanced: Linux, C, C++, Java, Python</li>
            <li>Experience: WordPress, JQuery, Bootstrap</li>
          </ul>
        </div>
      </div>
    </div>

    <!-- Team Leader -->
    <div class="glass p-8 mt-16 hover-scale" data-aos="fade-up">
      <h3 class="text-2xl font-semibold mb-2 text-center">👨‍🏫 Ing. Stanislav Marochok (Team Leader)</h3>
      <p class="text-center mb-2 text-sm">Email: <a href="mailto:stanislav.marochok@stuba.sk"
          class="underline">stanislav.marochok@stuba.sk</a></p>
      <p class="text-center">Project Supervisor for Web Application for Historical Handwritten Documents Processing.</p>
    </div>
  </section>

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

  <!-- AOS Init -->
  <script src="https://unpkg.com/aos@2.3.4/dist/aos.js"></script>
  <script>
    AOS.init({ duration: 1000, once: true });
  </script>
  <script src="https://unpkg.com/flowbite@1.6.5/dist/flowbite.min.js"></script>
</body>

</html>