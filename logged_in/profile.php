<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

require '../checkType.php';

try {
    $userData = validateToken();
} catch (Exception $e) {
    http_response_code(500);
    $_SESSION['toast'] = ['type' => 'error', 'message' => 'Token validation failed'];
    header('Location: login.php');
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>HandScript - Profile</title>

    <!-- Tailwind CSS -->
    <script src="https://cdn.tailwindcss.com"></script>

    <!-- AOS for animations -->
    <link href="https://unpkg.com/aos@2.3.1/dist/aos.css" rel="stylesheet">
    <script src="https://unpkg.com/aos@2.3.1/dist/aos.js"></script>

    <!-- Toastr -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.css" />
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.6.0/jquery.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.js"></script>

    <style>
        body {
            font-family: 'Inter', sans-serif;
            background: linear-gradient(to bottom right, #ede1c3, #cdbf9b);
        }

        .text-papyrus {
            color: #3b2f1d;
        }

        input:focus {
            outline: none !important;
            box-shadow: 0 0 0 2px #bfa97a;
        }
    </style>
</head>

<body class="min-h-screen flex flex-col select-none">

    <!-- Navbar -->
    <nav class="sticky top-0 z-50 w-full transition-all duration-300 bg-[#d7c7a5] border-b border-yellow-300 shadow-md not-copyable not-draggable"
        id="navbar">
        <div class="container mx-auto flex flex-wrap items-center justify-between py-3 px-4">
            <!-- Logo and brand -->
            <a href="main.php"
                class="flex items-center text-papyrus text-2xl font-bold hover:underline animate-slide-left"
                data-aos="fade-right" data-aos-delay="150">
                <img src="../img/logo.png" alt="Logo" class="w-10 h-10 mr-3"
                    style="filter: filter: brightness(0) saturate(100%) invert(15%) sepia(56%) saturate(366%) hue-rotate(357deg) brightness(98%) contrast(93%);">
                HandScript
            </a>

            <!-- Toggler button -->
            <button class="lg:hidden text-papyrus focus:outline-none" id="navbarToggle">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"
                    stroke-linecap="round" stroke-linejoin="round">
                    <path d="M4 6h16M4 12h16M4 18h16" />
                </svg>
            </button>

            <!-- Navigation links -->
            <div class="w-full lg:flex lg:items-center lg:w-auto hidden mt-4 lg:mt-0" id="navbarNav"
                data-aos="fade-left" data-aos-delay="150">
                <ul
                    class="flex flex-col lg:flex-row lg:space-x-6 w-full text-lg font-medium text-papyrus animate-slide-right">
                    <li class="flex items-center">
                        <a href="profile.php" class="nav-link flex items-center hover:underline">
                            Profile
                            <img src="../img/account.png" alt="profile" class="w-6 h-6 ml-2"
                                style="filter: brightness(0) saturate(100%) invert(15%) sepia(56%) saturate(366%) hue-rotate(357deg) brightness(98%) contrast(93%);">
                        </a>
                    </li>
                    <li class="flex items-center">
                        <a href="documents.php" class="nav-link flex items-center hover:underline">
                            Documents
                            <img src="../img/document.png" alt="document" class="w-6 h-6 ml-2"
                                style="filter: brightness(0) saturate(100%) invert(15%) sepia(56%) saturate(366%) hue-rotate(357deg) brightness(98%) contrast(93%);">
                        </a>
                    </li>
                    <li class="flex items-center">
                        <a href="https://tptimovyprojekt.ddns.net/" class="nav-link flex items-center hover:underline">
                            Project
                            <img src="../img/web.png" alt="project" class="w-6 h-6 ml-2"
                                style="filter: brightness(0) saturate(100%) invert(15%) sepia(56%) saturate(366%) hue-rotate(357deg) brightness(98%) contrast(93%);">
                        </a>
                    </li>
                    <li class="flex items-center">
                        <a href="../logout.php" class="nav-link flex items-center hover:underline">
                            Logout
                            <img src="../img/logout.png" alt="logout" class="w-6 h-6 ml-2"
                                style="filter: brightness(0) saturate(100%) invert(15%) sepia(56%) saturate(366%) hue-rotate(357deg) brightness(98%) contrast(93%);">
                        </a>
                    </li>
                </ul>
            </div>
        </div>
    </nav>

    <!-- Content -->
    <main class="flex-grow">
        <section class="container mx-auto px-6 py-10">
            <h1 class="text-4xl font-bold text-center text-papyrus mb-6" data-aos="fade-up">👤 Profile</h1>
            <p class="text-center text-lg mb-10 text-papyrus" data-aos="fade-up" data-aos-delay="100">
                Welcome back, <span class="font-semibold"><?php echo $userData['username']; ?></span>
            </p>

            <form action="profileUpdate.php" method="post" class="max-w-2xl mx-auto rounded-xl shadow-lg p-8 space-y-6"
                data-aos="fade-up" data-aos-delay="200"
                style="background-color: #fef9e4; border: 1px solid #3b2f1d;">
                <div>
                    <label class="block text-papyrus mb-1 font-semibold">Username</label>
                    <input type="text" name="username" id="username" placeholder="Username"
                        class="w-full border border-yellow-300 rounded px-4 py-2" />
                </div>
                <div>
                    <label class="block text-papyrus mb-1 font-semibold">Email</label>
                    <input type="email" name="email" id="email" placeholder="Email" oninput="isValidEmail(this)"
                        class="w-full border border-yellow-300 rounded px-4 py-2" />
                </div>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-papyrus mb-1 font-semibold">New Password</label>
                        <input type="password" name="password" id="password" placeholder="New Password"
                            class="w-full border border-yellow-300 rounded px-4 py-2" />
                    </div>
                    <div>
                        <label class="block text-papyrus mb-1 font-semibold">Repeat New Password</label>
                        <input type="password" name="password_confirm" id="password_confirm"
                            placeholder="New Password Again"
                            class="w-full border border-yellow-300 rounded px-4 py-2" />
                    </div>
                </div>
                <div class="text-center">
                    <button type="submit"
                        class="bg-[#bfa97a] text-white px-6 py-2 rounded hover:bg-[#a68f68] transition">Submit</button>
                </div>
            </form>
        </section>
    </main>

    <!-- Footer -->
    <footer class="bg-[#d7c7a5] text-center text-papyrus py-4 mt-10 border-t border-yellow-300">
        &copy; 2025 HandScript – <a href="https://tptimovyprojekt.ddns.net/" class="underline">Visit Project Page</a>
    </footer>

    <script>
        AOS.init({ duration: 800, once: true });

        function checkToasts() {
            let toast = <?php echo json_encode($_SESSION['toast'] ?? null); ?>;
            if (toast) {
                toastr[toast.type](toast.message);
                <?php unset($_SESSION['toast']); ?>
            }
        }

        checkToasts();
    </script>
</body>

</html>