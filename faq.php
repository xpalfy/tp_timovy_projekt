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
    <title>HandScript: FAQ</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.css">
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.6.0/jquery.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.js"></script>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;600;700&display=swap" rel="stylesheet">
    <link href="https://unpkg.com/aos@2.3.4/dist/aos.css" rel="stylesheet"/>
    <link rel="stylesheet" href="./css/task.css"/>
    <link rel="stylesheet" href="./css/team.css"/>
</head>

<body class="text-[#3b2f1d] bg-[#f5f3eb]">

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
    <img src="img/faq.jpg" alt="Hero" class="w-full h-full object-cover"/>
    <div class="absolute inset-0 bg-black bg-opacity-40 flex justify-center items-center px-4">
        <div
                class="glass p-8 border border-white/20 text-[#f8f6f1] text-center w-full max-w-2xl transition duration-300 hover:scale-105 hover:shadow-xl"
                style="background: rgba(255, 255, 255, 0.15); backdrop-filter: blur(10px);" data-aos="zoom-in">
            <h1 class="text-3xl md:text-4xl font-bold mb-3">Frequently Asked Questions</h1>
            <p class="text-base md:text-lg">Overview and guidance on the HandScript platform</p>
        </div>
    </div>
</header>


<!-- YouTube Tutorial -->
<section class="max-w-4xl mx-auto px-6 mb-16" data-aos="fade-up">
    <h2 class="text-3xl font-bold mb-10 text-center">Tutorial Video</h2>
    <div class="rounded-xl overflow-hidden shadow-lg">
        <iframe class="w-full h-96" src="https://www.youtube.com/embed/8uJ_K5_xlFg" title="Tutorial" frameborder="0"
                allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture; web-share"
                allowfullscreen></iframe>
    </div>
</section>


<!-- FAQ Accordion -->
<section class="max-w-4xl mx-auto px-6 mb-16" data-aos="fade-up" id="faq-section">
    <h2 class="text-3xl font-bold mb-6 text-center">Questions</h2>

    <div id="faqAccordion" data-accordion="collapse">
        <!-- Dynamically inserted FAQs -->
    </div>

    <!-- Pagination Placeholder -->
    <div class="flex justify-center mt-6">
        <nav id="faqPagination" class="inline-flex space-x-2"></nav>
    </div>

        <!-- Ask Question Form -->
    <section class="max-w-4xl mx-auto px-6 mt-16" data-aos="fade-up">
        <div class="bg-white p-6 rounded-xl shadow-md">
            <h3 class="text-2xl font-semibold mb-4">Ask a Question</h3>
            <form id="askQuestionForm">
        <textarea name="question" id="questionInput" rows="4"
                  class="w-full border border-gray-300 rounded-lg p-3 mb-4 resize-none" placeholder="Write your question here..."
                  required></textarea>
                <button type="submit"
                        class="bg-[#3b2f1d] text-white px-6 py-2 rounded-lg hover:bg-[#5a452e] transition">Submit
                </button>
            </form>
        </div>
    </section>
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


<!-- Scripts -->
<script src="https://unpkg.com/aos@2.3.4/dist/aos.js"></script>
<script src="https://unpkg.com/flowbite@1.6.5/dist/flowbite.min.js"></script>
<script>
    AOS.init({duration: 1000, once: true});
</script>

<script>
    // Fetch and render FAQs with pagination
    function fetchFAQs(page = 1) {
        fetch(`faq_api/getFaqs.php?page=${page}`)
            .then(res => res.json())
            .then(data => {
                if (!data.success) throw new Error('Failed to fetch FAQs');

                const faqContainer = document.getElementById('faqAccordion');
                const paginationContainer = document.getElementById('faqPagination');
                faqContainer.innerHTML = '';
                paginationContainer.innerHTML = '';

                // Render FAQ items
                data.faqs.forEach(faq => {
                    const id = `faq-${faq.id}`;
                    faqContainer.innerHTML += `
                        <div class="accordion-item mb-4 bg-white border border-gray-300 rounded-xl shadow-sm hover:shadow-md transition">
                            <button type="button"
                                class="accordion-btn w-full px-6 py-4 flex justify-between items-center text-left font-semibold text-lg focus:outline-none"
                                data-target="#${id}">
                                <span>${faq.question}</span>
                                <svg class="w-5 h-5 text-gray-500 transition-transform duration-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                                </svg>
                            </button>
                            <div id="${id}" class="hidden border-t border-gray-200">
                                <div class="px-6 py-4 space-y-4">
                                    <p class="text-sm font-medium text-gray-500">Asked:</p>
                                    <p class="text-base">${faq.question}</p>
                                    <p class="text-sm font-medium text-gray-500 mt-4">Answer:</p>
                                    <p class="text-base ${faq.answer ? 'text-green-800' : 'italic text-gray-400'}">
                                        ${faq.answer ? faq.answer : 'No answer yet.'}
                                    </p>
                                </div>
                            </div>
                        </div>`;
                });

                // Render pagination buttons
                for (let i = 1; i <= data.total_pages; i++) {
                    paginationContainer.innerHTML += `
                        <button onclick="fetchFAQs(${i})"
                            class="px-3 py-1 rounded ${i === page ? 'bg-[#3b2f1d] text-white' : 'bg-gray-200 text-black'} hover:bg-[#5a452e] transition">
                            ${i}
                        </button>`;
                }
            })
            .catch(err => {
                console.error('Error:', err);
                document.getElementById('faqAccordion').innerHTML =
                    `<p class="text-red-600 font-semibold">Could not load questions. Please try again later.</p>`;
                toastr.error('Could not load questions. Please try again later.');
            });
    }

    // Submit new question
    document.getElementById('askQuestionForm').addEventListener('submit', function (e) {
        e.preventDefault();
        const question = document.getElementById('questionInput').value.trim();

        if (!question) return;

        fetch('faq_api/postFaq.php', {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify({ question })
        })
            .then(res => res.json())
            .then(data => {
                if (data.success) {
                    toastr.success('Question submitted!');
                    document.getElementById('askQuestionForm').reset();
                } else {
                    toastr.error(data.error || 'Something went wrong.');
                }
            })
            .catch(err => {
                console.error('Submission error:', err);
                toastr.error('There was a problem submitting your question.');
            });
    });

    // Accordion toggle logic
    document.addEventListener('click', function (e) {
        const btn = e.target.closest('.accordion-btn');
        if (!btn) return;

        const target = document.querySelector(btn.dataset.target);
        const icon = btn.querySelector('svg');

        const isOpen = !target.classList.contains('hidden');

        // Close all
        document.querySelectorAll('.accordion-btn').forEach(b => {
            const c = document.querySelector(b.dataset.target);
            const i = b.querySelector('svg');
            c.classList.add('hidden');
            b.classList.remove('bg-blue-50');
            if (i) i.classList.remove('rotate-180');
        });

        // Open clicked
        if (!isOpen) {
            target.classList.remove('hidden');
            btn.classList.add('bg-blue-50');
            if (icon) icon.classList.add('rotate-180');
        }
    });

    // Initial load
    fetchFAQs();
</script>

</body>

</html>
