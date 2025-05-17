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
  <title>HandScript: Task</title>

  <script src="https://cdn.tailwindcss.com"></script>
  <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;600;700&display=swap" rel="stylesheet">
  <link href="https://unpkg.com/aos@2.3.4/dist/aos.css" rel="stylesheet" />
  <link href="https://unpkg.com/flowbite@1.6.5/dist/flowbite.min.css" rel="stylesheet" />
  <link rel="stylesheet" href="./css/task.css" />
  <link rel="stylesheet" href="./css/team.css" />
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
        <a href="./logout.php" class="hover:underline">Logout</a>
      <?php endif; ?>

    </div>
  </div>
</nav>

  <!-- Hero -->
  <header class="relative w-full h-[60vh] overflow-hidden mb-16">
    <img src="img/task.jpg" alt="Hero" class="w-full h-full object-cover" />
    <div class="absolute inset-0 bg-black bg-opacity-40 flex justify-center items-center px-4">
      <div
        class="glass p-8 border border-white/20 text-[#f8f6f1] text-center w-full max-w-2xl transition duration-300 hover:scale-105 hover:shadow-xl"
        style="background: rgba(255, 255, 255, 0.15); backdrop-filter: blur(10px);" data-aos="zoom-in">
        <h1 class="text-3xl md:text-4xl font-bold mb-3">Project Task</h1>
        <p class="text-base md:text-lg">Overview of the HandScript Project</p>
      </div>
    </div>
  </header>

  <!-- Download Offer Section -->
<section class="max-w-6xl mx-auto px-6 mb-12 text-center" data-aos="fade-up">
  <div class="glass p-8 rounded-2xl border shadow-md card-hover">
    <h2 class="text-3xl font-bold mb-8">📥 Download Resources</h2>

    <div class="grid grid-cols-1 md:grid-cols-2 gap-8 text-left">
      <!-- Project Offer -->
      <div class="p-4 bg-[#ede0c6] rounded-xl shadow-inner border border-yellow-200">
        <h3 class="text-xl font-semibold mb-2">🎓 Project Offer</h3>
        <p class="mb-4 text-gray-800">
          Download the official HandScript project offer created for the teacher or academic review.
        </p>
        <a href="./docs/pdf/handscript_project_offer.pdf" download
          class="btn-papyrus px-6 py-2 text-lg rounded-lg shadow hover:scale-105 transition inline-block">
          📄 Download Offer (PDF)
        </a>
      </div>

      <!-- Web Documentation -->
      <div class="p-4 bg-[#ede0c6] rounded-xl shadow-inner border border-yellow-200">
        <h3 class="text-xl font-semibold mb-2">🌐 Web App Documentation</h3>
        <p class="mb-4 text-gray-800">
          Get the technical documentation of the HandScript web app, including setup, features, and usage notes.
        </p>
        <a href="./docs/pdf/handscript_web_documentation.pdf" download
          class="btn-papyrus px-6 py-2 text-lg rounded-lg shadow hover:scale-105 transition inline-block">
          📘 Download Docs (PDF)
        </a>
      </div>
    </div>
  </div>
</section>

  <!-- Task Content -->
  <section class="max-w-6xl mx-auto px-6 mb-24">

    <div class="glass p-8 mb-16 hover-shadow text-lg space-y-6" data-aos="fade-up" data-aos-delay="300">
      <h2 class="text-3xl font-bold mb-6">Project Description</h2>
      <p class="text-lg mb-4">
        <strong>Sponsor of the team project:</strong><br>
        Ing. Stanislav Marochok (email: <a href="mailto:stanislav.marochok@stuba.sk"
          class="underline">stanislav.marochok@stuba.sk</a>)
      </p>
      <p class="text-lg mb-4">
        <strong>Topic:</strong><br>
        Web Application for Historical Handwritten Documents Processing
      </p>
      <p class="text-lg mb-6">
        Your task is to develop a web application for processing historical handwritten documents, encrypted texts and
        substitution cipher keys – using simulated AI modules.
        The application should be built on the .NET platform, with Python used for AI simulations, allowing future
        integration of real AI models.
      </p>
      <p class="text-lg mb-6">
        The application must classify uploaded documents as either encrypted texts or substitution cipher keys using a
        simulated AI module.
        For encrypted texts, users should localize the text, transcribe it, and decrypt it using brute-force algorithms
        or uploaded substitution cipher keys.
        For cipher keys, the application should localize and classify substitution sub-systems, detect substitution
        items, and map plain text to cipher text.
        The system must allow users to apply a new cipher key to existing encrypted texts to check compatibility and
        vice versa.
        The application must also include an interactive tutorial to guide new users on how to use the system
        effectively.
      </p>
      <p class="text-lg mb-6">
        Students must develop their own solutions for various challenges, thinking critically about design, integration,
        and functionality.
        This project requires creativity and independent problem-solving.
      </p>
      <p class="text-lg mb-6">
        The front-end should be responsive and visually appealing, enhancing user experience.
        The back-end must communicate with Python-based AI modules and implement JWT Bearer authorization for security.
        Users should also be able to save their progress and resume it later.
      </p>
      <p class="text-lg mb-6">
        Deliverables include a functional web application, source code, technical documentation, a user manual, and a
        presentation.
        Collaborate effectively using version control, follow coding standards, and ensure thorough testing.
      </p>
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