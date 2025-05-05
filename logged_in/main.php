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
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <title>HandScript</title>

    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;600;700&display=swap" rel="stylesheet">
    <script src="https://cdn.tailwindcss.com"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/flowbite/2.2.1/flowbite.min.js"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.css">
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.6.0/jquery.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/gsap/3.12.2/gsap.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/gsap/3.12.2/ScrollTrigger.min.js"></script>
    <script src="https://unpkg.com/@dotlottie/player-component@2.7.12/dist/dotlottie-player.mjs" type="module"></script>
    <script src="../js/segment-rect.js?v=<?php echo time() ?>" type="module"></script>
    <script src="../js/letter-rect.js?v=<?php echo time() ?>" type="module"></script>
    <link rel="stylesheet" href="../css/main.css?v=<?php echo time() ?>">
</head>

<body class="min-h-screen flex flex-col not-copyable not-draggable text-papyrus">
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
                    <path d="M4 6h16M4 12h16M4 18h16" />
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
                                        stroke-width="2" d="M1 1l4 4 4-4" />
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
                                        stroke-width="2" d="M1 1l4 4 4-4" />
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


    <!-- Welcome Hero Section -->
    <section class="text-center py-12 px-4 bg-[#e3d3b3] border-b border-yellow-200 not-copyable not-draggable">
        <h2 class="text-4xl font-extrabold text-papyrus animate-top-fade">Welcome, <?php echo $userData['username']; ?>
            👋</h2>
        <p class="text-lg text-papyrus mt-2 animate-top-fade">Start uploading and classifying your encrypted
            documents
            like an ancient scroll.</p>
    </section>

    <!-- Tutorial Section -->
    <div id="tutorial-carousel" class="relative w-full max-w-4xl mx-auto mt-10 animate-top-fade mb-20"
        data-carousel="static">
        <!-- Carousel wrapper -->
        <div class="relative h-auto overflow-hidden rounded-xl bg-[#f1e4c5] border border-yellow-300 shadow-lg p-6 text-papyrus"
            style="height: 350px;">
            <!-- Slide 1 -->
            <div class="hidden duration-700 ease-in-out slide-custom" data-carousel-item>
                <h3 class="text-xl font-bold mb-2">🔍 Welcome to HandScript</h3>
                <p class="mb-2">We are a modern web platform designed to help you decrypt handwritten coded texts using
                    the power of AI and pattern recognition.</p>
                <div style="display: flex; justify-content: center;">
                    <img class="mb-2" src="../img/info1.png?" alt="">
                </div>

                <p>Whether it's a personal cipher or historical encryption, we provide tools to reveal what's behind the
                    ink.</p>
            </div>
            <!-- Slide 2 -->
            <div class="hidden duration-700 ease-in-out slide-custom" data-carousel-item>
                <h3 class="text-xl font-bold mb-2">📂 Upload & Classify Your Documents</h3>
                <p class="mb-2">Users can upload scanned or photographed documents, and assign them as either:</p>
                <ul class="mb-2">
                    <li> &emsp;&emsp; 🔑 Keys – used to decode patterns</li>
                    <li> &emsp;&emsp; 🔐 Ciphers – encrypted text images ready for analysis</li>
                </ul>
                <p>This step is the entry point to our decoding pipeline.</p>
                <img src="../img/info2.png??" alt="" width="250px"
                    style="position: absolute; right: 110px; bottom: 90px;">
            </div>
            <!-- Slide 3 -->
            <div class="hidden duration-700 ease-in-out slide-custom" data-carousel-item>
                <h3 class="text-xl font-bold mb-2">🧠 AI Analysis & Editable Segmentation</h3>
                <p>Our system first analyzes the uploaded image, detects text areas, and segments each character
                    individually.</p>
                <div style="display: flex; justify-content: center;" class="mb-2">
                    <img src="../img/info3.png" alt="">
                </div>
                <p class="mb-2">You can review and manually adjust the results, making the recognition process more
                    accurate and interactive.</p>
            </div>
            <!-- Slide 4 -->
            <div class="hidden duration-700 ease-in-out slide-custom" data-carousel-item>
                <h3 class="text-xl font-bold mb-2">💾 Store, Edit & Share</h3>
                <p class="mb-2">Your uploaded documents are securely stored in your personal workspace.</p>
                <p class="mb-2">You can return later to edit results, continue processing, or share documents with other
                    users for collaboration.</p>
                <div style="display: flex; justify-content: center;">
                    <img src="../img/info4.png" alt="" width="350px">
                </div>
            </div>
        </div>

        <!-- Indicators -->
        <div class="absolute z-30 flex -translate-x-1/2 bottom-4 left-1/2 space-x-3 rtl:space-x-reverse">
            <button type="button" class="w-3 h-3 rounded-full bg-yellow-800" aria-current="true" aria-label="Slide 1"
                data-carousel-slide-to="0"></button>
            <button type="button" class="w-3 h-3 rounded-full bg-yellow-800" aria-label="Slide 2"
                data-carousel-slide-to="1"></button>
            <button type="button" class="w-3 h-3 rounded-full bg-yellow-800" aria-label="Slide 3"
                data-carousel-slide-to="2"></button>
            <button type="button" class="w-3 h-3 rounded-full bg-yellow-800" aria-label="Slide 4"
                data-carousel-slide-to="3"></button>
        </div>

        <!-- Navigation buttons -->
        <button type="button" class="absolute top-0 start-0 z-30 flex items-center justify-center h-full px-4"
            data-carousel-prev>
            <span
                class="inline-flex items-center justify-center w-10 h-10 rounded-full bg-yellow-100 hover:bg-yellow-200 duration-200">
                <svg class="w-4 h-4 text-brown-900" fill="none" viewBox="0 0 6 10">
                    <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M5 1 1 5l4 4" />
                </svg>
                <span class="sr-only">Previous</span>
            </span>
        </button>
        <button type="button" class="absolute top-0 end-0 z-30 flex items-center justify-center h-full px-4"
            data-carousel-next>
            <span
                class="inline-flex items-center justify-center w-10 h-10 rounded-full bg-yellow-100 hover:bg-yellow-200 duration-200">
                <svg class="w-4 h-4 text-brown-900" fill="none" viewBox="0 0 6 10">
                    <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="m1 9 4-4-4-4" />
                </svg>
                <span class="sr-only">Next</span>
            </span>
        </button>
    </div>

    <hr style="border: 1px solid #bfa97a;">
    <a onclick="scrollToBookmark('bookmark')"
        class="mt-10 mb-10 animate-top-fade text-papyrus not-copyable not-draggable"
        style="display: flex; align-items: center; flex-direction: column; ">
        <h1 style="font-size: x-large; cursor: pointer;">Start Uploading</h1>
        <img src="../img/arrow_down.png" alt="Try now"
            style="cursor: pointer; width: 50px; filter: invert(19%) sepia(29%) saturate(610%) hue-rotate(357deg) brightness(88%) contrast(95%);"
            id="arrow_down">
    </a>




    <!-- Process Area -->
    <div id="bookmark" class="mb-10"></div>
    <main id="ProcessArea" class="flex-grow container mx-auto px-4 mt-10">
        <div class="glass max-w-4xl mx-auto animate-fade-in-slow border-yellow-300 border">
            <!-- Progress Steps -->
            <div
                style="display: flex; flex-direction: column; justify-content: center; border: #bfa97a4a 1px solid; border-radius: 20px 20px 0 0 ; padding: 10px 10px 5px 10px;">
                <div class="step-progress-container not-copyable not-draggable">
                    <div class="step-group">
                        <div class="step active">1</div>
                        <h3 class="step-info text-papyrus">Upload</h3>
                    </div>
                    <div class="line"></div>
                    <div class="step-group">
                        <div class="step">2</div>
                        <h3 class="step-info text-papyrus">Segment</h3>
                    </div>
                    <div class="line"></div>
                    <div class="step-group">
                        <div class="step">3</div>
                        <h3 class="step-info text-papyrus">Analyze</h3>
                    </div>
                    <div class="line"></div>
                    <div class="step-group">
                        <div class="step">4</div>
                        <h3 class="step-info text-papyrus">Letters</h3>
                    </div>
                    <div class="line"></div>
                    <div class="step-group">
                        <div class="step">5</div>
                        <h3 class="step-info text-papyrus">Save</h3>
                    </div>
                </div>
            </div>
            <!-- Process Info -->
            <h3 id="ProcessInfo" class="not-copyable text-2xl mt-4 font-bold text-center text-papyrus mb-6">Upload &
                Classify Image
            </h3>
            <!-- Image Uploader STEP 0 -->
            <div id="imageUploader" onclick="document.getElementById('fileInput').click()"
                class="border-4 border-dashed border-yellow-400 hover:border-yellow-500 rounded-xl p-6 m-2 text-center transition-all duration-300">
                <div class="loading-cont"
                    style="overflow: hidden; position: absolute; left: 0; right: 0; bottom: 0; top: 0; display: none; justify-content: center; align-items: center; border-radius: 20px; background-color:rgba(115, 124, 133, 0.52); z-index: 3;">
                    <dotlottie-player src="https://lottie.host/4f6b3ace-c7fc-45e9-85a2-c1fe04047ae3/QLPJzOha5m.lottie"
                        background="transparent" speed="1" style="width: 150px; height: 150px;" loop
                        autoplay></dotlottie-player>
                </div>
                <div
                    style="z-index: -1; position: absolute; width: 92%; height: 68%; display: flex; align-items: center; justify-content: center; margin-top: 1rem;">
                    <p id="uploadInstruction" class="not-copyable text-papyrus">Drag and drop an image here or click to
                        upload</p>
                </div>
                <div id="previewContainer" class="relative mt-4 flex justify-center items-center">
                    <img class="imagePreview hidden rounded-xl not-copyable"
                        style="width: 100%; height: 100%; max-width: 100%; max-height: 100%; object-fit: contain;"
                        src="" alt="Preview" draggable="false">
                    <button
                        class="prevBtn not-copyable absolute left-3 z-2 bg-yellow-100 hover:bg-yellow-200 text-papyrus font-bold px-3 py-1 rounded-full shadow transition duration-200"
                        style="visibility: hidden;">❮</button>

                    <button
                        class="nextBtn not-copyable absolute right-3 z-2 bg-yellow-100 hover:bg-yellow-200 text-papyrus font-bold px-3 py-1 rounded-full shadow transition duration-200"
                        style="visibility: hidden;">❯</button>
                </div>

                <input type="file" id="fileInput" accept="image/*" multiple hidden onchange="uploadImageButton(event)">
                <button id="uploadBtn" class="mt-4 btn-papyrus px-6 py-2 rounded-lg shadow not-copyable"
                    onclick="console.log('Upload')">
                    Select Image(s)
                </button>
            </div>

            

            <!-- Progress Buttons STEP 0 -> STEP 1 -->
            <div id="CreateBtns" class="flex flex-col items-center space-y-4 mt-6" style="display: none;">
                <!-- Input row -->
                <div>
                    <input type="text" id="documentName" class="border border-yellow-300 rounded-lg px-4 py-2"
                        placeholder="Document Name" />
                </div>

                <!-- Buttons row -->
                <div class="flex space-x-4" id="CreateBtnsBtns">
                    <button class="btn-papyrus px-4 py-2 rounded-lg shadow" onclick="saveKey()">Create Key
                        Document</button>
                    <button class="btn-papyrus px-4 py-2 rounded-lg shadow" onclick="saveCipher()">Create Cipher
                        Document</button>
                </div>
            </div>

            <!-- Progress Buttons STEP 0 -> STEP 1 -->
            <div id="SegmentBtns" class="flex justify-center space-x-4 mt-6" style="display: none;">
                <button class="btn-papyrus px-4 py-2 rounded-lg shadow" onclick="goToSegmentation()">Process the document</button>
            </div>

            <!-- System Message -->
            <div id="SystemMessage" class="mt-4 .bg-\[\#f1e4c5\] p-3 rounded-lg text-center text-papyrus"
                style="border-radius: 0 0 20px 20px; font-weight: bold; font-size: 1.3rem;">
            </div>
        </div>
    </main>

    <footer
        class="bg-[#d7c7a5] text-papyrus text-center py-4 mt-10 border-t border-yellow-300 not-copyable not-draggable">
        &copy; 2025 HandScript – <a href="https://tptimovyprojekt.ddns.net/" class="underline">Visit Project Page</a>
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
    </script>
    <script type="module" src="js/main.js?v=<?= time() ?>"></script>
</body>

</html>