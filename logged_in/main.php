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
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <title>HandScript</title>

    <!-- Google Fonts & Icons -->
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;600;700&display=swap" rel="stylesheet">

    <!-- Tailwind CSS -->
    <script src="https://cdn.tailwindcss.com"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/flowbite/2.2.1/flowbite.min.js"></script>


    <!-- Toastr Notifications -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.css">
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.6.0/jquery.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.js"></script>

    <!-- GSAP for animations -->
    <!-- GSAP Core -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/gsap/3.12.2/gsap.min.js"></script>
    <!-- GSAP ScrollTrigger Plugin -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/gsap/3.12.2/ScrollTrigger.min.js"></script>

    <!-- Lottie Animation Player -->
    <script src="https://unpkg.com/@dotlottie/player-component@2.7.12/dist/dotlottie-player.mjs" type="module"></script>

    <!-- Custom JS -->
    <script src="../js/segment-rect.js?" type="module"></script>



    <style>
        body {
            font-family: 'Inter', sans-serif;
            background: linear-gradient(to bottom right, #ede1c3, #cdbf9b);
        }

        #previewContainer {
            width: 100%;
            height: 55vh;
            /* Fixed height for all previews */
            display: flex;
            justify-content: center;
            align-items: center;
            position: relative;
            overflow: hidden;
            background-color: rgba(248, 241, 222, 0);
            /* subtle beige background */
            border-radius: 12px;
        }

        #imagePreview {
            max-height: 100%;
            max-width: 100%;
            object-fit: contain;
            display: none;
            border-radius: 12px;
            transition: opacity 0.3s ease;
        }


        .glass {
            background: rgba(236, 225, 193, 0.65);
            backdrop-filter: blur(4px);
            border-radius: 20px;
            box-shadow: 0 8px 24px rgba(0, 0, 0, 0.2);
        }

        .btn-papyrus {
            background-color: #bfa97a;
            color: #3b2f1d;
        }

        .btn-papyrus:hover {
            background-color: #a68f68;
        }

        .text-papyrus {
            color: #3b2f1d;
        }

        .slide-custom {
            padding: 2rem 4rem;
            display: flex;
            flex-direction: column;
        }

        #arrow_down {
            animation: zoom_and_point_down 9s infinite;
        }



        @keyframes zoom_and_point_down {
            0% {
                transform: scale(1);
            }

            5.55% {
                transform: scale(1.2);
            }

            11.1% {
                transform: scale(1);
            }

            16.65% {
                transform: translateY(+10px);
            }

            22.2% {
                transform: translateY(0);
            }

            27.75% {
                transform: translateY(+10px);
            }

            33.3% {
                transform: translateY(0);
            }
        }

        .step-progress-container {
            display: flex;
            align-items: center;
            justify-content: center;
            width: 100%;
            gap: 10px;
        }

        .step-group {
            display: flex;
            flex-direction: column;
            align-items: center;
            transition: transform 0.3s ease;
        }

        /* Hover group to scale both elements */
        .step-group:hover {
            transform: scale(1.2);
        }

        .step {
            width: 42px;
            height: 42px;
            background-color: #cdbf9b;
            color: #3b2f1d;
            border-radius: 50%;
            display: flex;
            justify-content: center;
            align-items: center;
            font-weight: bold;
            z-index: 1;
            box-shadow: 0 2px 6px rgba(0, 0, 0, 0.15);
            transition: background-color 0.6s;
        }

        .step.active {
            background-color: #bfa97a;
            color: white;
        }

        .step-info {
            margin-top: 8px;
            font-size: 0.95rem;
            transition: transform 0.3s ease;
        }

        .line {
            flex: 1;
            height: 4px;
            background-color: #e5d9b6;
            border-radius: 2px;
            transition: background-color 0.6s;
        }

        .not-copyable {
            user-select: none;
            -webkit-user-select: none;
            -moz-user-select: none;
            -ms-user-select: none;
        }

        .not-draggable {
            -webkit-user-drag: none;
            user-select: none;
        }
    </style>

</head>

<body class="min-h-screen flex flex-col">
    <!-- Navbar -->
    <nav class="sticky top-0 z-50 w-full transition-all duration-300 bg-[#d7c7a5] border-b border-yellow-300 shadow-md not-copyable not-draggable"
        id="navbar">
        <div class="container mx-auto flex flex-wrap items-center justify-between py-3 px-4">
            <!-- Logo and brand -->
            <a href="main.php"
                class="flex items-center text-papyrus text-2xl font-bold hover:underline animate-slide-left">
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
            <div class="w-full lg:flex lg:items-center lg:w-auto hidden mt-4 lg:mt-0" id="navbarNav">
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
                        <h3 class="step-info text-papyrus">Store</h3>
                    </div>
                </div>
            </div>
            <!-- Process Info -->
            <h3 id="ProcessInfo" class="not-copyable text-2xl mt-4 font-bold text-center text-papyrus mb-6">Upload &
                Classify Image
            </h3>
            <!-- Image Uploader STEP 0 -->
            <div id="imageUploader" ondrop="handleDrop(event)" ondragover="handleDragOver(event)"
                ondragleave="handleDragLeave()" onclick="document.getElementById('fileInput').click()"
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
                    <img class="imagePreview max-w-full hidden rounded-xlc not-copyable" src="" alt="Preview"
                        draggable="false">
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
            <!-- Image Segmentation STEP 1 -->
            <div class="col-md mt-5 animate-fade-in-slow" id="imageSegmentor" style="display: none;">
                <div class="glass p-6 text-center relative border border-yellow-200 rounded-2xl shadow-lg">
                    <!-- Loading Overlay -->
                    <div class="loading-cont not-copyable not-draggable"
                        style="overflow: hidden; position: absolute; left: 0; right: 0; bottom: 0; top: 0; display: none; justify-content: center; align-items: center; border-radius: 20px; background-color:rgba(115, 124, 133, 0.52); z-index: 3;">
                        <dotlottie-player
                            src="https://lottie.host/4f6b3ace-c7fc-45e9-85a2-c1fe04047ae3/QLPJzOha5m.lottie"
                            background="transparent" speed="1" style="width: 150px; height: 150px;" loop
                            autoplay></dotlottie-player>
                    </div>

                    <!-- Preview Image Container -->
                    <div id="previewContainerSegment" class="relative flex justify-center items-center min-h-[250px]">
                        <!-- Left Nav -->
                        <button
                            class="not-copyable prevBtn absolute left-3 z-20 bg-yellow-100 hover:bg-yellow-200 text-papyrus font-bold px-3 py-1 rounded-full shadow transition duration-200">❮</button>
                        <!-- Image Preview -->
                        <img class="not-copyable imagePreview max-w-full hidden rounded-xl" src="" alt="Preview"
                            draggable="false" />
                        <!-- Right Nav -->
                        <button
                            class="not-copyable nextBtn absolute right-3 z-20 bg-yellow-100 hover:bg-yellow-200 text-papyrus font-bold px-3 py-1 rounded-full shadow transition duration-200">❯</button>
                    </div>
                </div>
            </div>
            <!-- Image Analysis STEP 2 -->
            <div class="col-md mt-5 animate-fade-in-slow" id="imageAnalyzer" style="display: none;">
                <div class="glass p-6 text-center relative border border-yellow-200 rounded-2xl shadow-lg">
                    <!-- Loading Overlay -->
                    <div class="loading-cont not-copyable not-draggable"
                        style="overflow: hidden; position: absolute; left: 0; right: 0; bottom: 0; top: 0; display: none; justify-content: center; align-items: center; border-radius: 20px; background-color:rgba(115, 124, 133, 0.52); z-index: 3;">
                        <dotlottie-player
                            src="https://lottie.host/4f6b3ace-c7fc-45e9-85a2-c1fe04047ae3/QLPJzOha5m.lottie"
                            background="transparent" speed="1" style="width: 150px; height: 150px;" loop
                            autoplay></dotlottie-player>
                    </div>

                    <!-- Preview Image Container -->
                    <div id="previewContainerAnalyze" class="relative flex justify-center items-center min-h-[250px]">
                        <!-- Left Nav -->
                        <button
                            class="not-copyable prevBtn absolute left-3 z-20 bg-yellow-100 hover:bg-yellow-200 text-papyrus font-bold px-3 py-1 rounded-full shadow transition duration-200">❮</button>
                        <!-- Image Preview -->
                        <img class="not-copyable imagePreview max-w-full hidden rounded-xl" src="" alt="Preview"
                            draggable="false" />
                        <!-- Right Nav -->
                        <button
                            class="not-copyable nextBtn absolute right-3 z-20 bg-yellow-100 hover:bg-yellow-200 text-papyrus font-bold px-3 py-1 rounded-full shadow transition duration-200">❯</button>
                    </div>
                    <!-- document name -->
                    <div class="mt-4">
                        <input type="text" id="documentName" class="border border-yellow-300 rounded-lg px-4 py-2"
                            placeholder="Document Name" />
                    </div>
                </div>
            </div>


            <!-- Progress Buttons STEP 0 -> STEP 1 -->
            <div id="SegmentBtns" class="flex justify-center space-x-4 mt-6" style="display: none;">
                <button class="btn-papyrus px-4 py-2 rounded-lg shadow" onclick="segmentKey()">Segment as Key</button>
                <button class="btn-papyrus px-4 py-2 rounded-lg shadow" onclick="segmentCipher()">Segment as
                    Cipher</button>
            </div>

            <!-- Progress Buttons STEP 1 -> STEP 2 -->
            <div id="AnalyzeKeyBtn" class="flex justify-center space-x-4 mt-6" style="display: none;">
                <button class="btn-papyrus px-4 py-2 rounded-lg shadow" onclick="analizeKey()">Analyze Key (Now Save
                    WIP)</button>
            </div>

            <!-- Progress Buttons STEP 1 -> STEP 2 -->
            <div id="AnalyzeCipherBtn" class="flex justify-center space-x-4 mt-6" style="display: none;">
                <button class="btn-papyrus px-4 py-2 rounded-lg shadow" onclick="analizeCipher()">Analyze Cipher (Now
                    Save
                    WIP)</button>
            </div>

            <!-- Progress Buttons STEP 2 -> STEP Final -->
            <div id="SaveKeyBtn" class="flex justify-center space-x-4 mt-6" style="display: none;">
                <button class="btn-papyrus px-4 py-2 rounded-lg shadow" onclick="saveKey()">Save Key</button>
            </div>

            <!-- Progress Buttons STEP 2 -> STEP Final -->
            <div id="SaveCipherBtn" class="flex justify-center space-x-4 mt-6" style="display: none;">
                <button class="btn-papyrus px-4 py-2 rounded-lg shadow" onclick="saveCipher()">Save Cipher</button>
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
        let currentImageId = [];
        let previewImages = [];
        let classificationScores = [];
        let currentPreviewIndex = 0;
        let numOfFiles = 0;
        let lastScrollTop = 0;

        function handleFile(file, shouldShow, first) {
            disableClickUpload();
            if (file.type.match('image.*')) {
                let reader = new FileReader();
                reader.onload = function (e) {
                    if (currentImageId.length > 0 && first) {
                        console.log("Deleting previous unsaved images...");
                        deleteUnsavedImage(currentImageId);
                    }
                    image_name = file.name.split('.')[0].split(' ').join('_').toLowerCase();
                    previewImages.push([e.target.result, image_name]);
                    console.log("Image uploaded:", previewImages);
                    if (shouldShow) {
                        currentPreviewIndex = previewImages.length - 1;
                        updatePreview();
                    }
                    saveImage(e.target.result, image_name);
                };
                reader.readAsDataURL(file);
            } else {
                toastr.error('Please upload image files only.');
            }
        }

        function handleDrop(event) {
            event.preventDefault();
            const files = event.dataTransfer.files;
            showLoading();
            numOfFiles = files.length;
            if (files.length > 1) {
                showScrollBtns();
            } else {
                hideScrollBtns();
            }
            for (let i = 0; i < files.length; i++) {
                handleFile(files[i], i === files.length - 1, i === 0);
            }
        }

        function uploadImageButton(event) {
            const files = event.target.files;
            showLoading();
            numOfFiles = files.length;
            if (files.length > 1) {
                showScrollBtns();
            } else {
                hideScrollBtns();
            }
            for (let i = 0; i < files.length; i++) {
                handleFile(files[i], i === files.length - 1, i === 0);
            }
        }

        function handleDragOver(event) {
            event.preventDefault();
            document.getElementById('imageUploader').style.border = '4px dashed #eab308';
        }

        function handleDragLeave() {
            document.getElementById('imageUploader').style.border = '#eab308 dashed 4px';
        }

        function updatePreview() {
            let imageElements = document.getElementsByClassName('imagePreview');
            for (imageElement of imageElements) {
                if (previewImages.length === 0) {
                    imageElement.style.display = 'none';
                    continue;
                }
                imageElement.src = previewImages[currentPreviewIndex][0];
                imageElement.style.display = 'block';
            }
        }

        function showScrollBtns() {
            buttons = document.getElementsByClassName('prevBtn');
            for (let button of buttons) {
                button.style.visibility = 'visible';
            }
            buttons = document.getElementsByClassName('nextBtn');
            for (let button of buttons) {
                button.style.visibility = 'visible';
            }
        }

        function hideScrollBtns() {
            buttons = document.getElementsByClassName('prevBtn');
            for (let button of buttons) {
                button.style.visibility = 'hidden';
            }
            buttons = document.getElementsByClassName('nextBtn');
            for (let button of buttons) {
                button.style.visibility = 'hidden';
            }
        }

        buttons = document.getElementsByClassName('prevBtn');
        for (let button of buttons) {
            button.addEventListener('click', function () {
                if (previewImages.length === 0) return;
                currentPreviewIndex = (currentPreviewIndex - 1 + previewImages.length) % previewImages.length;
                updatePreview();
            });
        }

        buttons = document.getElementsByClassName('nextBtn');
        for (let button of buttons) {
            button.addEventListener('click', function () {
                if (previewImages.length === 0) return;
                currentPreviewIndex = (currentPreviewIndex + 1) % previewImages.length;
                updatePreview();
            });
        }

        function saveData(type) {
            console.log(previewImages);
            if (previewImages.length === 0) {
                handleError('Please upload an image first.');
                return;
            }
            doc_name = document.getElementById('documentName').value;
            if (doc_name === '') {
                handleError('Please enter a name for the document.');
                return;
            }

            console.log(doc_name);

            fetch('documents/createDocument.php', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json'
                },
                body: JSON.stringify({
                    doc_name: doc_name,
                    type: type,
                    user_name: <?php echo json_encode($userData['username']); ?>,
                    id: <?php echo json_encode($userData['id']); ?>
                })
            })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        toastr.success(data.message);
                        console.log("Document created successfully.");
                        doc_id = data.document_id;
                        console.log("Document ID:", doc_id);
                        for (let [data, image_name] of previewImages) {
                            fetch('items/createItem.php', {
                                method: 'POST',
                                headers: {
                                    'Content-Type': 'application/json'
                                },
                                body: JSON.stringify({
                                    image_name: image_name,
                                    doc_id: doc_id,
                                    doc_name: doc_name,
                                    type: type,
                                    user_name: <?php echo json_encode($userData['username']); ?>,
                                    id: <?php echo json_encode($userData['id']); ?>
                                })
                            })
                                .then(response => response.json())
                                .then(data => {
                                    if (data.success) {
                                        currentImageId = null; // Image is saved, no need to delete it
                                    } else {
                                        handleError(data.error);
                                    }
                                });
                        }
                        toastr.success('Images uploaded successfully.');
                        // reset window
                        currentImageId = [];
                        previewImages = [];
                        classificationScores = [];
                        currentPreviewIndex = 0;
                        updatePreview();
                        hideSegmentBtns();
                        hideAnalyzeKeyBtn();
                        hideAnalyzeCipherBtn();
                        hideSaveKeyBtn();
                        hideSaveCipherBtn();
                        hideLoading();
                        hideSystemMessage();
                        setStep(0);
                    } else {
                        handleWarning(data.error);
                        return;
                    }
                });



        }

        function saveKey() {
            saveData('KEY');
        }

        function saveCipher() {
            saveData('CIPHER');
        }

        function saveImage(data, image_name) {
            fetch('savePicture.php', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json'
                },
                body: JSON.stringify({
                    data: data,
                    data_name: image_name,
                    user_name: <?php echo json_encode($userData['username']); ?>,
                    type: 'temp',
                    id: <?php echo json_encode($userData['id']); ?>
                })
            }).then(response => response.json())
                .then(data => {
                    if (data.success) {
                        toastr.success(data.message);

                        currentImageId.push(data.picture_id); // Store the temporary image ID
                        console.log("Image uploaded successfully. ID:", currentImageId);
                        classificationScores.push(classifyPicture(data.path));
                        if (classificationScores.length === numOfFiles && classificationScores.length > 0) {
                            showSegmentBtns();
                            applyClassificationStyle(classificationScores);
                        }
                    } else {
                        handleError(data.error);
                    }
                });
        }

        function showSegmentBtns() {
            document.getElementById('SegmentBtns').style.display = 'flex';
        }

        function hideSegmentBtns() {
            document.getElementById('SegmentBtns').style.display = 'none';
        }

        function showAnalyzeKeyBtn() {
            document.getElementById('AnalyzeKeyBtn').style.display = 'flex';
        }

        function hideAnalyzeKeyBtn() {
            document.getElementById('AnalyzeKeyBtn').style.display = 'none';
        }

        function showAnalyzeCipherBtn() {
            document.getElementById('AnalyzeCipherBtn').style.display = 'flex';
        }

        function hideAnalyzeCipherBtn() {
            document.getElementById('AnalyzeCipherBtn').style.display = 'none';
        }

        function showSaveKeyBtn() {
            document.getElementById('SaveKeyBtn').style.display = 'flex';
        }

        function hideSaveKeyBtn() {
            document.getElementById('SaveKeyBtn').style.display = 'none';
        }

        function showSaveCipherBtn() {
            document.getElementById('SaveCipherBtn').style.display = 'flex';
        }

        function hideSaveCipherBtn() {
            document.getElementById('SaveCipherBtn').style.display = 'none';
        }

        function deleteUnsavedImage(imageId) {
            console.log("Deleting unsaved images...");
            for (let id of imageId) {
                fetch(`deleteDocument.php?id=${id}&user=${<?php echo json_encode($userData['id']); ?>}`, {
                    method: 'GET'
                }).then(response => {
                    if (response.ok) {
                        console.log("Unsaved image deleted successfully.");
                    } else {
                        console.error("Failed to delete unsaved image.");
                    }
                }).catch(error => {
                    handleError("Error deleting unsaved image:" + error);
                });
            }
            currentImageId = [];
            previewImages = [];
            classificationScores = [];
            currentPreviewIndex = 0;
            updatePreview();
            hideSegmentBtns();
            hideAnalyzeKeyBtn();
            hideAnalyzeCipherBtn();
            hideSaveKeyBtn();
            hideSaveCipherBtn();
            hideLoading();
            hideSystemMessage();
        }

        async function classifyPicture(path) {
            const url = 'https://python.tptimovyprojekt.software/classify';
            console.log("Sending request to Flask server...");

            try {
                const response = await fetch(url, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json'
                    },
                    body: JSON.stringify({ path })  // Sending JSON data
                });

                if (!response.ok) {
                    throw new Error(`HTTP error! Status: ${response.status}`);
                    handleError();
                }

                const data = await response.json();

                if (data.classification) {
                    console.log("Classification:", data.classification);
                    toastr.success(`Classification: ${data.classification}`);
                    return data.classification;
                } else {
                    console.error("Error in classification response.");
                    handleError();
                }
            } catch (error) {
                console.error("Error sending request to Flask server:", error.message);
                handleError();
            }
        }

        function checkToasts() {
            let toast = <?php echo json_encode($_SESSION['toast'] ?? null); ?>;
            if (toast) {
                toastr[toast.type](toast.message);
                <?php unset($_SESSION['toast']); ?>
            }
        }

        function applyClassificationStyle(classification_score) {
            let parentOfBtns = document.getElementById('SegmentBtns');
            let segmentKeyBtn = parentOfBtns.children[0];
            let segmentCipherBtn = parentOfBtns.children[1];
            let messageContainer = document.getElementById('SystemMessage');

            score = 0;
            Promise.all(classificationScores).then(value => {
                for (let i = 0; i < value.length; i++) {
                    score += value[i];
                }
                classification_score = score / classification_score.length;

                // Reset styles
                segmentCipherBtn.style.border = "2px solidrgb(0, 0, 0)";
                segmentCipherBtn.style.padding = "9px";
                segmentKeyBtn.style.border = "2px solidrgb(0, 0, 0)";
                segmentKeyBtn.style.padding = "9px";

                if (classification_score > 50) {
                    segmentCipherBtn.style.border = "2px solid green";
                    segmentCipherBtn.style.padding = "9px";
                    messageContainer.innerHTML = `The classifier thinks the images are ${classification_score}% ciphertexts.`;
                } else {
                    segmentKeyBtn.style.border = "2px solid green";
                    segmentKeyBtn.style.padding = "9px";
                    messageContainer.innerHTML = `The classifier thinks the images are ${100 - classification_score}% keys.`;
                }
                hideLoading();
                showSystemMessage();
            })
        }

        function showLoading() {
            loadings = document.getElementsByClassName('loading-cont');
            for (let loading of loadings) {
                loading.style.display = 'flex';
            }
        }

        function hideLoading() {
            loadings = document.getElementsByClassName('loading-cont');
            for (let loading of loadings) {
                loading.style.display = 'none';
            }
        }

        function showSystemMessage() {
            document.getElementById('SystemMessage').style.display = 'block';
        }

        function hideSystemMessage() {
            document.getElementById('SystemMessage').style.display = 'none';
        }

        function disableClickUpload() {
            document.getElementById('imageUploader').removeAttribute('onclick');
            document.getElementById('uploadBtn').setAttribute('onclick', 'document.getElementById("fileInput").click()');
        }

        function setStep(index) {
            const steps = document.querySelectorAll('.step');
            const lines = document.querySelectorAll('.line');

            steps.forEach((step, i) => {
                if (i <= index) {
                    step.classList.add('active');
                } else {
                    step.classList.remove('active');
                }
            });

            lines.forEach((line, i) => {
                if (i < index) {
                    line.style.backgroundColor = '#bfa97a';
                } else {
                    line.style.backgroundColor = '#cdbf9b';
                }
            });
        }

        function handleError(error_message) {
            hideLoading();
            document.getElementById('SegmentBtns').style.display = 'none';
            document.getElementById('classificationMessage').style.display = 'none';
            previewImages = [];
            currentImageId = [];
            classificationScores = [];
            currentPreviewIndex = 0;
            updatePreview();
            toastr.error(error_message || 'An error occurred. Please try again.');
        }

        function handleWarning(warning_message) {
            hideLoading();
            toastr.warning(warning_message || 'An warning occurred. Please try again.');
        }

        function segmentCipher() {
            setStep(1);
            hideSegmentBtns();
            hideSystemMessage();
            showAnalyzeCipherBtn();
            document.getElementById('imageUploader').style.display = 'none';
            document.getElementById('imageSegmentor').style.display = 'block';
            document.getElementById('ProcessInfo').innerHTML = 'Wait for the system to process the images.<br>If the system made some mistakes, feel free to correct them.';
            // got to #Dashboard
            scrollToBookmark('bookmark');
            updatePreview();
            CalculateSegmentation('Cipher');
        }

        function segmentKey() {
            setStep(1);
            hideSegmentBtns();
            hideSystemMessage();
            showAnalyzeKeyBtn();
            document.getElementById('imageUploader').style.display = 'none';
            document.getElementById('imageSegmentor').style.display = 'block';
            document.getElementById('ProcessInfo').innerHTML = 'Wait for the system to process the images.<br>If the system made some mistakes, feel free to correct them.';
            // got to #Dashboard
            scrollToBookmark('bookmark');
            updatePreview();
            CalculateSegmentation('Key');
        }

        function analizeKey() {
            setStep(2);
            hideAnalyzeKeyBtn();
            hideSystemMessage();
            showSaveKeyBtn();
            document.getElementById('imageSegmentor').style.display = 'none';
            document.getElementById('imageAnalyzer').style.display = 'block';
            document.getElementById('ProcessInfo').innerHTML = 'Wait for the system to analyze the images.<br>If the system made some mistakes, feel free to correct them.';
            // got to #Dashboard
            scrollToBookmark('bookmark');
            updatePreview();
            CalculateAnalization('Key');
        }

        function analizeCipher() {
            setStep(2);
            hideAnalyzeCipherBtn();
            hideSystemMessage();
            showSaveCipherBtn();
            document.getElementById('imageSegmentor').style.display = 'none';
            document.getElementById('imageAnalyzer').style.display = 'block';
            document.getElementById('ProcessInfo').innerHTML = 'Wait for the system to analyze the images.<br>If the system made some mistakes, feel free to correct them.';
            // got to #Dashboard
            scrollToBookmark('bookmark');
            updatePreview();
            CalculateAnalization('Cipher');
        }

        function CalculateSegmentation(type) {
            // TODO: Implement image segmentation
            // For now, just wait 5secs and hide loading, then show the buttons and rect
            showLoading();
            setTimeout(() => {
                hideLoading();
                let Rect = [98, 33, 770, 504];
                appendSegmentedRect(Rect);
            }, 5000);
        }

        function appendSegmentedRect(Rect) {
            // Rect should follow pattern [x1, y1, x2, y2], two diagonal points of the rectangle
            if (Rect.length !== 4) {
                console.error('Invalid Rect:', Rect);
                return;
            }
            let parent = document.getElementById('previewContainerSegment');
            // calculate othe two points
            let x2 = Rect[0];
            let y2 = Rect[3];
            let x4 = Rect[2];
            let y4 = Rect[1];

            let newRect = document.createElement('segment-rect');
            newRect.setAttribute('x1', Rect[0]);
            newRect.setAttribute('y1', Rect[1]);
            newRect.setAttribute('x2', x2);
            newRect.setAttribute('y2', y2);
            newRect.setAttribute('x3', Rect[2]);
            newRect.setAttribute('y3', Rect[3]);
            newRect.setAttribute('x4', x4);
            newRect.setAttribute('y4', y4);
            newRect.setAttribute('style', 'position: absolute; width: 100%; height: 100%;');
            newRect.classList.add('rounded-xl');
            parent.appendChild(newRect);
        }

        function CalculateAnalization(type) {
            // TODO: Implement image analization
            // For now, just wait 5secs and hide loading, then show the buttons and rects
            showLoading();
            setTimeout(() => {
                hideLoading();
                let Rects = [[131, 143, 243, 389], [135, 60, 404, 146], [452, 61, 755, 94], [455, 131, 570, 236], [615, 105, 734, 133], [596, 140, 739, 173]];
                appendAnalizedRects(Rects);
            }, 5000);
        }

        function appendAnalizedRects(Rects) {
            // Rects should follow pattern [[x1, y1, x2, y2], [x1, y1, x2, y2], ...]
            let parent = document.getElementById('previewContainerAnalyze');
            for (let Rect of Rects) {
                if (Rect.length !== 4) {
                    console.error('Invalid Rect:', Rect);
                    return;
                }
                // calculate othe two points
                let x2 = Rect[0];
                let y2 = Rect[3];
                let x4 = Rect[2];
                let y4 = Rect[1];

                let newRect = document.createElement('segment-rect');
                newRect.setAttribute('x1', Rect[0]);
                newRect.setAttribute('y1', Rect[1]);
                newRect.setAttribute('x2', x2);
                newRect.setAttribute('y2', y2);
                newRect.setAttribute('x3', Rect[2]);
                newRect.setAttribute('y3', Rect[3]);
                newRect.setAttribute('x4', x4);
                newRect.setAttribute('y4', y4);
                newRect.setAttribute('style', 'position: absolute; width: 100%; height: 100%;');
                newRect.classList.add('rounded-xl');
                parent.appendChild(newRect);
            }
        }

        function scrollEvent() {
            const currentScroll = window.pageYOffset || document.documentElement.scrollTop;
            const navbar = document.getElementById('navbar');
            if (currentScroll > lastScrollTop) {
                // Scrolling down
                navbar.style.top = '-80px';
            } else {
                // Scrolling up
                navbar.style.top = '0';
            }
            lastScrollTop = currentScroll <= 0 ? 0 : currentScroll; // Avoid negative values
        }

        function scrollToBookmark(BookmarkId) {
            window.removeEventListener('scroll', scrollEvent);
            document.getElementById(BookmarkId).scrollIntoView({ behavior: 'smooth' });
            setTimeout(() => {
                window.addEventListener('scroll', scrollEvent);
            }, 800);
        }

        window.addEventListener("beforeunload", function () {
            console.log("Page is being unloaded..., deleting currentImageId:", currentImageId);

            if (currentImageId) {
                deleteUnsavedImage(currentImageId);
            }
        });

        window.addEventListener('scroll', scrollEvent);

        document.getElementById('navbarToggle').addEventListener('click', function () {
            const nav = document.getElementById('navbarNav');
            nav.classList.toggle('hidden');
        });

        gsap.registerPlugin(ScrollTrigger);
        // GSAP Animations
        gsap.from(".animate-top-fade", {
            opacity: 0,
            y: -80,
            duration: 2,
            ease: "power3.out",
            stagger: 0.5
        });

        gsap.from(".animate-fade-in-slow", {
            scrollTrigger: {
                trigger: ".animate-fade-in-slow",
                start: "top 90%",
                toggleActions: "play none none none"
            },
            opacity: 0,
            transform: "scale(0.5)",
            duration: 2,
            ease: "power3.out",
        });

        gsap.from(".animate-slide-left", {
            scrollTrigger: {
                trigger: ".animate-slide-left",
                start: "top 85%",
                toggleActions: "play none none none"
            },
            x: -200,
            opacity: 0,
            duration: 1.5,
            ease: "power2.out"
        });

        gsap.from(".animate-slide-right", {
            scrollTrigger: {
                trigger: ".animate-slide-right",
                start: "top 85%",
                toggleActions: "play none none none"
            },
            x: 200,
            opacity: 0,
            duration: 1.5,
            ease: "power2.out"
        });

        hideLoading();
        setStep(0);
        checkToasts();

    </script>
</body>

</html>