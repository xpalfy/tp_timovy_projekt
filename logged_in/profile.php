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
    header('Location: ../login.php');
    exit();
}

$fullCallerUrl = (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? 'https' : 'http') .
    '://' . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI'];


?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8"/>
    <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
    <title>HandScript - Profile</title>

    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://unpkg.com/aos@2.3.1/dist/aos.css" rel="stylesheet">
    <script src="https://unpkg.com/aos@2.3.1/dist/aos.js"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.css"/>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.6.0/jquery.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/flowbite/2.2.1/flowbite.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <link rel="stylesheet" href="../css/profile.css"/>
</head>

<body class="min-h-screen flex flex-col select-none">

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

<!-- Navbar -->
<nav class="sticky top-0 z-50 w-full transition-all duration-300 bg-[#d7c7a5] border-b border-yellow-300 shadow-md not-copyable not-draggable"
     id="navbar">
    <div class="container mx-auto flex flex-wrap items-center justify-between py-3 px-4">
        <a href="main.php"
           class="flex items-center text-papyrus text-2xl font-bold hover:underline animate-slide-left">
            <img src="../img/logo.png" alt="Logo" class="w-10 h-10 mr-3"
                 style="filter: filter: brightness(0) saturate(100%) invert(15%) sepia(56%) saturate(366%) hue-rotate(357deg) brightness(98%) contrast(93%);">
            HandScript
        </a>
        <button class="lg:hidden text-papyrus focus:outline-none" id="navbarToggle">
            <svg class="w-6 h-6" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"
                 stroke-linecap="round" stroke-linejoin="round">
                <path d="M4 6h16M4 12h16M4 18h16"/>
            </svg>
        </button>
        <div class="w-full lg:flex lg:items-center lg:w-auto hidden mt-4 lg:mt-0" id="navbarNav">
            <ul class="flex flex-col lg:flex-row w-full text-lg font-medium text-papyrus animate-slide-right">
                <li class="flex items-center">
                    <a href="profile.php" class="nav-link flex items-center hover:underline">
                        Profile
                        <img src="../img/account.png" alt="profile" class="w-6 h-6 ml-2"
                             style="filter: brightness(0) saturate(100%) invert(15%) sepia(56%) saturate(366%) hue-rotate(357deg) brightness(98%) contrast(93%);">
                    </a>
                </li>
                <li class="flex items-center ml-6">
                    <div class="relative flex items-center">
                        <button id="dropdownDocumentsButton" data-dropdown-toggle="dropdownDocuments"
                                class="hover:underline flex items-center">
                            Documents
                            <img src="../img/document.png" alt="document" class="w-6 h-6 ml-2"
                                 style="filter: brightness(0) saturate(100%) invert(15%) sepia(56%) saturate(366%) hue-rotate(357deg) brightness(98%) contrast(93%);">
                            <svg class="w-2.5 h-2.5 ml-2.5" aria-hidden="true" xmlns="http://www.w3.org/2000/svg"
                                 fill="none" viewBox="0 0 10 6">
                                <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round"
                                      stroke-width="2" d="M1 1l4 4 4-4"/>
                            </svg>
                        </button>
                        <div id="dropdownDocuments"
                             class="z-10 hidden font-normal bg-[#d7c7a5] divide-y divide-gray-100 rounded-lg shadow w-44 absolute top-full mt-2">
                            <ul class="py-2 text-sm text-[#3b2f1d]" aria-labelledby="dropdownDocumentsButton">
                                <li>
                                    <a href="ownKeyDocuments.php" class="block px-4 py-2 hover:bg-[#cbbd99]">Key
                                        Documents</a>
                                </li>
                                <li>
                                    <a href="ownCipherDocuments.php"
                                       class="block px-4 py-2 hover:bg-[#cbbd99]">Cipher Documents</a>
                                </li>
                            </ul>
                        </div>
                    </div>
                </li>
                <li class="flex items-center ml-6">
                    <div class="relative flex items-center">
                        <button id="dropdownToolsButton" data-dropdown-toggle="dropdownTools"
                                class="hover:underline flex items-center">
                            Tools
                            <img src="../img/tools.png" alt="tools" class="w-6 h-6 ml-2"
                                 style="filter: brightness(0) saturate(100%) invert(15%) sepia(56%) saturate(366%) hue-rotate(357deg) brightness(98%) contrast(93%);">
                            <svg class="w-2.5 h-2.5 ml-2.5" aria-hidden="true" xmlns="http://www.w3.org/2000/svg"
                                 fill="none" viewBox="0 0 10 6">
                                <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round"
                                      stroke-width="2" d="M1 1l4 4 4-4"/>
                            </svg>
                        </button>
                        <div id="dropdownTools"
                             class="z-10 hidden font-normal bg-[#d7c7a5] divide-y divide-gray-100 rounded-lg shadow w-44 absolute top-full mt-2">
                            <ul class="py-2 text-sm text-[#3b2f1d]" aria-labelledby="dropdownToolsButton">
                                <li>
                                    <a href="./modules/segmentModule.php"
                                       class="block px-4 py-2 hover:bg-[#cbbd99]">Segment</a>
                                </li>
                                <li>
                                    <a href="./modules/analyzeModule.php"
                                       class="block px-4 py-2 hover:bg-[#cbbd99]">Analyze</a>
                                </li>
                                <li>
                                    <a href="./modules/lettersModule.php"
                                       class="block px-4 py-2 hover:bg-[#cbbd99]">Letters</a>
                                </li>
                                <li>
                                    <a href="./modules/editJsonModule.php"
                                       class="block px-4 py-2 hover:bg-[#cbbd99]">Edit Json</a>
                                </li>
                                <li>
                                    <a href="./modules/decipherModule.php"
                                        class="block px-4 py-2 hover:bg-[#cbbd99]">Decipher</a>
                                </li>
                            </ul>
                        </div>
                    </div>
                </li>
                <li class="flex items-center ml-6">
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

<main class="min-h-[90vh] flex justify-center items-start pt-16 px-4 bg-transparent">
    <section class="container mx-auto max-w-6xl mt-10">
        <div class="grid grid-cols-1 md:grid-cols-2 gap-20 items-start justify-center mt-4">

            <!-- Profile Update Form -->
            <form action="users/profileUpdate.php" method="post"
                  class="glass rounded-xl p-10 space-y-6 shadow-lg w-full">

                <div class="flex items-center gap-4 mb-10">
                    <h1 class="text-4xl font-bold text-papyrus">Change Information</h1>
                </div>

                <div>
                    <label class="block text-papyrus mb-1 font-semibold">Username</label>
                    <input type="text" name="username" id="username" placeholder="Username" oninput="isValidInput(this)"
                           class="w-full border rounded px-4 py-3"/>
                </div>

                <div>
                    <label class="block text-papyrus mb-1 font-semibold">Email</label>
                    <input type="email" name="email" id="email" placeholder="Email" oninput="isValidEmail(this)"
                           class="w-full border rounded px-4 py-3"/>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-papyrus mb-1 font-semibold">New Password</label>
                        <input type="password" name="password" id="password" oninput="isValidPassword(this)"
                               autocomplete="off" placeholder="New Password"
                               class="w-full border rounded px-4 py-3"/>
                    </div>
                    <div>
                        <label class="block text-papyrus mb-1 font-semibold">Repeat New Password</label>
                        <input type="password" name="password_confirm" id="password_confirm" oninput="isValidPassword(this)"
                               autocomplete="off" placeholder="New Password Again"
                               class="w-full border rounded px-4 py-3"/>
                    </div>
                </div>

                <div class="text-center">
                    <button type="submit" class="btn-papyrus px-8 py-3 rounded font-semibold transition text-white mt-6">
                        Submit
                    </button>
                    <button type="button" id="deleteAccountButton" onClick="deleteUser()"
                            class="btn-papyrus px-8 py-3 rounded font-semibold transition text-white mt-6 ml-4">
                        Delete Account
                    </button>
                </div>
            </form>

            <!-- Profile Overview Panel -->
            <div class="glass rounded-2xl p-10 shadow-xl flex flex-col items-center space-y-6 w-full">
                <img 
                    src="../img/avatars/avatar_<?php echo $userData['avatarId']; ?>.png" 
                    alt="User Avatar" 
                    class="rounded-full object-cover mb-6"
                    id="currentAvatarImage"
                >

                <h2 class="text-3xl font-bold text-papyrus">📄 Profile Overview</h2>

                <div class="grid grid-cols-2 gap-x-4 w-full text-papyrus">
                    <p class="font-semibold mt-2">Username:</p>
                    <p id="currentUsername"><?php echo $userData['username']; ?></p>

                    <p class="font-semibold mt-2">Email:</p>
                    <p id="currentEmail"><?php echo $userData['email']; ?></p>
                </div>
            </div>

        </div>
    </section>
</main>


<!-- Footer -->
<footer class="bg-[#d7c7a5] border-t border-yellow-300 text-[#3b2f1d] py-6">
    <div class="max-w-7xl mx-auto px-4 flex flex-col md:flex-row justify-between items-center space-y-4 md:space-y-0">
        <p class="text-center md:text-left">&copy; 2025 HandScript</p>
        <div class="flex space-x-4 text-sm">
            <a href="https://tptimovyprojekt.ddns.net/" class="underline hover:text-[#5a452e] transition">Visit Project
                Page</a>
            <a href="../faq.html" target="_blank" rel="noopener noreferrer" class="underline hover:text-[#5a452e] transition">FAQ</a>
        </div>
    </div>
</footer>

<script src="../js/regex.js?v=2"></script>
<script>
    document.querySelector('form').addEventListener('submit', async function (e) {
        e.preventDefault();
        const form = e.target;
        if (!checkForm(form)) return;
        e.target.submit();
    });

    function deleteUser() {
        const callerUrl = "<?php echo $fullCallerUrl; ?>";
        const token = '<?php echo $_SESSION['token']; ?>'
        const userId = "<?php echo $userData['id']; ?>";

        Swal.fire({
            title: 'Are you sure?',
            text: "You won't be able to revert this!",
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#d33',
            cancelButtonColor: '#3085d6',
            confirmButtonText: 'Yes, delete it!'
        }).then((result) => {
            if (result.isConfirmed) {
                $.ajax({
                    url: 'https://python.tptimovyprojekt.software/delete_user',
                    type: 'DELETE',
                    contentType: 'application/json',
                    data: JSON.stringify({
                        token: token,
                        user_id: userId
                    }),
                    headers: {
                        'X-Caller-Url': callerUrl
                    },
                    success: function (response) {
                        if (response.success === true) {
                            Swal.fire(
                                'Deleted!',
                                'Your account has been deleted.',
                                'success'
                            ).then(() => {
                                document.cookie = "cookiesAccepted=; expires=Thu, 01 Jan 1970 00:00:00 UTC; path=/;";
                                window.location.href = '../logout.php';
                            });
                        } else {
                            Swal.fire(
                                'Error!',
                                response.error || 'There was an error deleting your account.',
                                'error'
                            );
                        }
                    },
                    error: function (xhr) {
                        const errorMessage = xhr.responseJSON?.error || 'There was an error deleting your account.';
                        Swal.fire(
                            'Error!',
                            errorMessage,
                            'error'
                        );
                    }
                });
            }
        });
    }

    

</script>

</body>

</html>