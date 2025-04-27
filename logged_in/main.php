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
    <script src="../js/letter-rect.js?" type="module"></script>



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
                </div>
            </div>

            <!-- Image Letters STEP 3 -->
            <div class="col-md mt-5 animate-fade-in-slow" id="imageLetters" style="display: none;">
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
                    <div id="previewContainerLetter" class="relative flex justify-center items-center min-h-[250px]">
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

            <!-- Image JSON STEP 4 -->
            <div class="col-md mt-5 animate-fade-in-slow" id="imageJSON" style="display: none;">
                <div class="glass p-6 text-center relative border border-yellow-200 rounded-2xl shadow-lg">
                    <!-- Loading Overlay -->
                    <div class="loading-cont not-copyable not-draggable"
                        style="overflow: hidden; position: absolute; left: 0; right: 0; bottom: 0; top: 0; display: none; justify-content: center; align-items: center; border-radius: 20px; background-color:rgba(115, 124, 133, 0.52); z-index: 3;">
                        <dotlottie-player
                            src="https://lottie.host/4f6b3ace-c7fc-45e9-85a2-c1fe04047ae3/QLPJzOha5m.lottie"
                            background="transparent" speed="1" style="width: 150px; height: 150px;" loop
                            autoplay></dotlottie-player>
                    </div>

                     <!-- JSON Textarea Editor -->
                    <div class="mt-4 text-left">
                        <label for="jsonEditor" class="block text-sm font-medium text-yellow-100 mb-1">Edit JSON:</label>
                        <textarea id="jsonEditor" rows="14" class="w-full border border-yellow-300 rounded-lg p-3 font-mono bg-white text-gray-800"
                            placeholder="{ ... }">{}</textarea>
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
                <button class="btn-papyrus px-4 py-2 rounded-lg shadow" onclick="segmentKey()">Process as Key</button>
                <button class="btn-papyrus px-4 py-2 rounded-lg shadow" onclick="segmentCipher()">Process as
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

            <!-- Progress Buttons STEP 2 -> STEP 3 -->
            <div id="LettersKeyBtn" class="flex justify-center space-x-4 mt-6" style="display: none;">
                <button class="btn-papyrus px-4 py-2 rounded-lg shadow" onclick="lettersKey()">Segment Letters</button>
            </div>

            <!-- Progress Buttons STEP 2 -> STEP 3 -->
            <div id="LetterCipherBtn" class="flex justify-center space-x-4 mt-6" style="display: none;">
                <button class="btn-papyrus px-4 py-2 rounded-lg shadow" onclick="lettersCipher()">Segment Letters</button>
            </div>

            <!-- Progress Buttons STEP 3 -> STEP 4 -->
            <div id="EditJSONKeyBtn" class="flex justify-center space-x-4 mt-6" style="display: none;">
                <button class="btn-papyrus px-4 py-2 rounded-lg shadow" onclick="editJSONKey()">Edit JSON</button>
            </div>

            <!-- Progress Buttons STEP 3 -> STEP 4 -->
            <div id="EditJSONCipherBtn" class="flex justify-center space-x-4 mt-6" style="display: none;">
                <button class="btn-papyrus px-4 py-2 rounded-lg shadow" onclick="editJSONCipher()">Edit JSON</button>
            </div>
            
            <!-- Progress Buttons STEP 4 -> STEP Final -->
            <div id="SaveKeyBtns" class="flex justify-center space-x-4 mt-6" style="display: none;">
                <button class="btn-papyrus px-4 py-2 rounded-lg shadow" onclick="saveKey()">Save Key</button>
                <button class="btn-papyrus px-4 py-2 rounded-lg shadow" onclick="downloadJSON()">Download JSON</button>
            </div>

            <!-- Progress Buttons STEP 4 -> STEP Final -->
            <div id="SaveCipherBtns" class="flex justify-center space-x-4 mt-6" style="display: none;">
                <button class="btn-papyrus px-4 py-2 rounded-lg shadow" onclick="saveCipher()">Save Cipher</button>
                <button class="btn-papyrus px-4 py-2 rounded-lg shadow" onclick="downloadJSON()">Download JSON</button>
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
    <script src="js/main.js?v=<?= time() ?>"></script> <!-- in PHP -->
</body>

</html>