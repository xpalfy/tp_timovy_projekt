<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

require '../../checkType.php';

try {
    $userData = validateToken();
} catch (Exception $e) {
    http_response_code(500);
    $_SESSION['toast'] = ['type' => 'error', 'message' => 'You have to log in first!'];
    header('Location: ../../login.php');
    exit();
}

?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <title>Segment</title>

    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://code.jquery.com/ui/1.13.2/themes/base/jquery-ui.css">
    <!-- ✅ jQuery UI styles -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.css">
    <link rel="stylesheet" href="../../css/main.css?v=<?php echo time() ?>">

    <script src="https://cdn.tailwindcss.com"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/flowbite/2.2.1/flowbite.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.6.0/jquery.min.js"></script>
    <script src="https://code.jquery.com/ui/1.13.2/jquery-ui.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/gsap/3.12.2/gsap.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/gsap/3.12.2/ScrollTrigger.min.js"></script>
    <script src="https://unpkg.com/@dotlottie/player-component@2.7.12/dist/dotlottie-player.mjs" type="module"></script>

</head>

<body class="min-h-screen flex flex-col not-copyable not-draggable text-papyrus">

    <!-- Navbar -->
    <nav class="sticky top-0 z-50 w-full transition-all duration-300 bg-[#d7c7a5] border-b border-yellow-300 shadow-md not-copyable not-draggable"
        id="navbar">
        <div class="container mx-auto flex flex-wrap items-center justify-between py-3 px-4">
            <a href="../main.php"
                class="flex items-center text-papyrus text-2xl font-bold hover:underline animate-slide-left">
                <img src="../../img/avatars/avatar_<?php echo $userData['avatarId']; ?>.png" alt="Logo"
                    class="w-10 h-10 mr-6 mb-2">
                Dashboard
            </a>
            <button class="lg:hidden text-papyrus focus:outline-none" id="navbarToggle">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"
                    stroke-linecap="round" stroke-linejoin="round">
                    <path d="M4 6h16M4 12h16M4 18h16" />
                </svg>
            </button>

            <div class="w-full lg:flex lg:items-center lg:w-auto hidden lg:mt-1" id="navbarNav">
                <ul
                    class="flex flex-col lg:flex-row w-full text-lg font-medium text-papyrus animate-slide-right gap-x-6">
                    <li class="flex items-center">
                        <a href="../../index.php" class="nav-link flex items-center hover:underline">
                            Home
                            <img src="https://cdn-icons-png.flaticon.com/512/25/25694.png" alt="home"
                                class="w-6 h-6 ml-2 mr-1"
                                style="filter: brightness(0) saturate(100%) invert(15%) sepia(56%) saturate(366%) hue-rotate(357deg) brightness(98%) contrast(93%);">
                        </a>
                    </li>

                    <li class="flex items-center">
                        <a href="../profile.php" class="nav-link flex items-center hover:underline">
                            Profile
                            <img src="../../img/account.png" alt="profile" class="w-6 h-6 ml-2 mr-1"
                                style="filter: brightness(0) saturate(100%) invert(15%) sepia(56%) saturate(366%) hue-rotate(357deg) brightness(98%) contrast(93%);">
                        </a>
                    </li>

                    <li class="flex items-center">
                        <div class="relative flex items-center">
                            <button id="dropdownDocumentsButton" data-dropdown-toggle="dropdownDocuments"
                                class="hover:underline flex items-center">
                                Library
                                <img src="../../img/document.png" alt="document" class="w-6 h-6 ml-2 mb-1"
                                    style="filter: brightness(0) saturate(100%) invert(15%) sepia(56%) saturate(366%) hue-rotate(357deg) brightness(98%) contrast(93%);">
                                <svg class="w-2.5 h-2.5 ml-2.5" fill="none" viewBox="0 0 10 6">
                                    <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round"
                                        stroke-width="2" d="M1 1l4 4 4-4" />
                                </svg>
                            </button>
                            <div id="dropdownDocuments"
                                class="z-10 hidden font-normal bg-[#d7c7a5] divide-y divide-gray-100 rounded-lg shadow w-44 absolute top-full mt-2">
                                <ul class="py-2 text-sm text-[#3b2f1d]">
                                    <li><a href="../ownKeyDocuments.php"
                                            class="block px-4 py-2 hover:bg-[#cbbd99]">Cipher
                                            Keys</a></li>
                                    <li><a href="../ownCipherDocuments.php"
                                            class="block px-4 py-2 hover:bg-[#cbbd99]">Encrypted
                                            Documents</a></li>
                                </ul>
                            </div>
                        </div>
                    </li>

                    <li class="flex items-center">
                        <div class="relative flex items-center">
                            <button id="dropdownToolsButton" data-dropdown-toggle="dropdownTools"
                                class="hover:underline flex items-center">
                                Tools
                                <img src="../../img/tools.png" alt="tools" class="w-6 h-6 ml-2 mb-1"
                                    style="filter: brightness(0) saturate(100%) invert(15%) sepia(56%) saturate(366%) hue-rotate(357deg) brightness(98%) contrast(93%);">
                                <svg class="w-2.5 h-2.5 ml-2.5" fill="none" viewBox="0 0 10 6">
                                    <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round"
                                        stroke-width="2" d="M1 1l4 4 4-4" />
                                </svg>
                            </button>
                            <div id="dropdownTools"
                                class="z-10 hidden font-normal bg-[#d7c7a5] divide-y divide-gray-100 rounded-lg shadow w-44 absolute top-full mt-2">
                                <ul class="py-2 text-sm text-[#3b2f1d]">
                                    <li><a href="./segmentModule.php"
                                            class="block px-4 py-2 hover:bg-[#cbbd99]">Segment</a>
                                    </li>
                                    <li><a href="./analyzeModule.php"
                                            class="block px-4 py-2 hover:bg-[#cbbd99]">Analyze</a>
                                    </li>
                                    <li><a href="./lettersModule.php"
                                            class="block px-4 py-2 hover:bg-[#cbbd99]">Letters</a>
                                    </li>
                                    <li><a href="./editJsonModule.php" class="block px-4 py-2 hover:bg-[#cbbd99]">Edit
                                            Json</a></li>
                                    <li><a href="./decipherModule.php"
                                            class="block px-4 py-2 hover:bg-[#cbbd99]">Decipher</a>
                                    </li>
                                </ul>
                            </div>
                        </div>
                    </li>

                    <li class="flex items-center">
                        <a href="../../logout.php" class="nav-link flex items-center hover:underline">
                            Logout
                            <img src="../../img/logout.png" alt="logout" class="w-6 h-6 ml-2 mb-1"
                                style="filter: brightness(0) saturate(100%) invert(15%) sepia(56%) saturate(366%) hue-rotate(357deg) brightness(98%) contrast(93%);">
                        </a>
                    </li>
                </ul>
            </div>

        </div>
    </nav>

    <!-- Process Area -->
    <main id="ProcessArea" class="flex-grow container mx-auto px-4 mt-10">

        <div class="glass max-w-4xl mx-auto animate-fade-in-slow border-yellow-300 border">
            <!-- Loading Overlay -->
            <div class="loading-cont not-copyable not-draggable"
                style="overflow: hidden; position: absolute; left: 0; right: 0; bottom: 0; top: 0; display: none; justify-content: center; align-items: center; border-radius: 20px; background-color:rgba(115, 124, 133, 0.52); z-index: 3;">
                <dotlottie-player src="https://lottie.host/4f6b3ace-c7fc-45e9-85a2-c1fe04047ae3/QLPJzOha5m.lottie"
                    background="transparent" speed="1" style="width: 150px; height: 150px;" loop
                    autoplay></dotlottie-player>
            </div>
            <!-- Process Info -->
            <h3 id="ProcessInfo" class="not-copyable text-2xl mt-4 font-bold text-center text-papyrus mb-6">
                Decrypt your Cipher Documents
            </h3>
            <p id="ProcessInfoMini" class="text-center text-gray-600 mb-4 pr-10 pl-10">
                This module allows you to decrypt your previously processed Cipher documents using Key documents. 
                To use it, first search for and select the encrypted Cipher document you want to decrypt, then preview the document to confirm your selection. 
                Next, search for and select the appropriate Key document; our AI will recommend the best matching keys for your chosen Cipher document. 
                Once both documents are selected, click the "Start Decipher" button to begin the decryption process. 
                The decrypted result will be displayed below, and you can copy the text to your clipboard or navigate to your documents library. 
                <br><b>Note:</b> Only processed Cipher and Key documents will appear in the selectors. If you do not see your files, please upload and process them first.
                <br>Need help? Check our <a href="../../faq.php"><b><u>FAQ</u></b></a> page.
            </p>

            <p id="noCipherDocs" class="text-center text-gray-600 mt-4 pr-10 pl-10"
                style="display: none; transition: transform 0.5s ease;">
                No processed Cipher documents found. Please upload a Cipher document first and process it.
            </p>

            <p id="noKeyDocs" class="text-center text-gray-600 mt-4 pr-10 pl-10"
                style="display: none; transition: transform 0.5s ease;">
                No processed Key documents found. Please upload a Key document first and process it.
            </p>

            <div class="flex flex-col items-center justify-center gap-4">
                <div id="leftSide" class="flex flex-col items-center justify-center p-6 max-w-8xl mx-auto">
                    <p>Please choose the Document you want to decrypt</p>
                    <!-- Document Selector -->
                    <div class="flex justify-center items-center mt-5">
                        <input type="text" placeholder="Search for a document"
                            class="bg-[#d7c7a5] text-papyrus border border-yellow-300 rounded-lg p-2"
                            id="documentSearchCipher">


                    </div>
                    <!-- Item selector -->
                    <div class="flex justify-center items-center mt-5">
                        <select id="itemSelectorCipher"
                            class="bg-[#d7c7a5] text-papyrus border border-yellow-300 rounded-lg p-2" disabled
                            style="display: none;">
                            <option value="" disabled selected>Select an item</option>
                        </select>
                    </div>

                    <!-- Image Preview -->
                    <div class="flex justify-center items-center mt-5">
                        <img id="imagePreviewCipher"
                            class="imagePreview w-[30rem] h-auto object-cover rounded-lg shadow-lg" src=""
                            alt="Image Preview" style="display: none;">
                    </div>
                </div>
                <div id="rightSide" class="flex flex-col items-center justify-center p-6 max-w-8xl mx-auto"
                    style="display: none;">
                    <p>Please choose the Key you want to use for decryption</p>
                    <!-- Document Selector -->
                    <div class="flex justify-center items-center mt-5">
                        <input type="text" placeholder="Search for a document"
                            class="bg-[#d7c7a5] text-papyrus border border-yellow-300 rounded-lg p-2"
                            id="documentSearchKey">
                    </div>
                    <!-- Item selector -->
                    <div class="flex justify-center items-center mt-5">
                        <select id="itemSelectorKey"
                            class="bg-[#d7c7a5] text-papyrus border border-yellow-300 rounded-lg p-2" disabled
                            style="display: none;">
                            <option value="" disabled selected>Select an item</option>
                        </select>
                    </div>

                    <!-- Image Preview -->
                    <div class="flex justify-center items-center mt-5">
                        <img id="imagePreviewKey"
                            class="imagePreview w-[30rem] h-auto object-cover rounded-lg shadow-lg" src=""
                            alt="Image Preview" style="display: none;">
                    </div>

                    <h1 id="recommendMessage" style="font-size: x-large;">Our AI is recommending the following keys for
                        your cipher document:</h1>

                    <div id="KeySelector" class="flex justify-center items-center mt-5 flex-wrap gap-4">
                        <!-- Here comes the Keys from the fetch -->
                    </div>
                </div>
                <div class="flex justify-center items-center mt-5 mb-5">
                    <button id="startDecipherBtn"
                        class="bg-[#d7c7a5] text-papyrus border border-yellow-300 rounded-lg p-2 mt-2 transition duration-300 hover:bg-yellow-300 hover:text-[#d7c7a5]"
                        style="display: none;" onclick="startDecipher()">Start Dechiper</button>
                </div>

                <!-- Result Area -->
                <div id="resultArea" class="flex justify-center items-center mb-5 mt-5 w-full relative"
                    style="display: none;">
                    <h3 class="text-2xl font-bold text-center text-papyrus mb-6">Decrypted Result</h3>
                    <div class="bg-[#d7c7a5] text-papyrus border border-yellow-300 rounded-lg p-4 mr-5 ml-5">
                        <textarea id="resultText" class="w-full h-auto resize-none" rows="15"
                            style="padding: 5px; padding-right: 25px; min-height: 60px;">Decrypted text will be shown here.</textarea>
                        <button id="copyToClipboardBtn"
                            class="rounded-lg p-1 transition duration-300 hover:bg-gray-100 absolute"
                            style="top: 78px; right: 40px;" onclick="copyToClipboard()"><img src="../../img/copy.png"
                                width="20px" height="20px"></button>
                    </div>
                </div>

                <div id="navigationBtn" class="flex justify-center items-center mt-5 mb-5" style="display: none;">
                    <a href="../ownCipherDocuments.php"
                        class="bg-[#d7c7a5] text-papyrus border border-yellow-300 rounded-lg p-2 mt-2 transition duration-300 hover:bg-yellow-300 hover:text-[#d7c7a5]">Go
                        to your Documents</a>
                </div>
            </div>
        </div>
    </main>

    <footer
        class="bg-[#d7c7a5] text-papyrus text-center py-4 mt-10 border-t border-yellow-300 not-copyable not-draggable">
        &copy; 2025 HandScript – All rights reserved.
    </footer>

    <script>
        // Pass PHP session toast data to JS
        window.toastData = <?php echo json_encode($_SESSION['toast'] ?? null); ?>;
        <?php unset($_SESSION['toast']); ?> // Clear toast after reading
    </script>
    <script>
        // Pass PHP data to JS
        window.userData = {
            username: <?= json_encode($userData['username']) ?>,
            id: <?= json_encode($userData['id']) ?>
        };
        window.phpToken = <?= json_encode($_SESSION['token']) ?>;
    </script>
    <script type="module" src="../js/decryptModule.js?v=<?= time() ?>"></script>
</body>

</html>