<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

require '../../checkType.php';

try {
    $userData = validateToken();
} catch (Exception $e) {
    http_response_code(500);
    $_SESSION['toast'] = ['type' => 'error', 'message' => 'Token validation failed'];
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
    <script src="../../js/segment-rect.js?v=<?php echo time() ?>" type="module"></script>

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

    <div id="polygonModal" class="modal" style="display:none;">
        <div class="modal-content">
            <span class="close" onclick="document.getElementById('polygonModal').style.display='none'">&times;</span>
            <div id="polygonModalContent"></div>
        </div>
    </div>

    <!-- Process Area -->
    <main id="ProcessArea" class="flex-grow container mx-auto px-4 mt-10">
        <div class="glass max-w-4xl mx-auto animate-fade-in-slow border-yellow-300 border"
            style="transition: height 0.5s ease;">
            <!-- Loading Overlay -->
            <div class="loading-cont not-copyable not-draggable"
                style="overflow: hidden; position: absolute; left: 0; right: 0; bottom: 0; top: 0; display: none; justify-content: center; align-items: center; border-radius: 20px; background-color:rgba(115, 124, 133, 0.52); z-index: 3;">
                <dotlottie-player src="https://lottie.host/4f6b3ace-c7fc-45e9-85a2-c1fe04047ae3/QLPJzOha5m.lottie"
                    background="transparent" speed="1" style="width: 150px; height: 150px;" loop
                    autoplay></dotlottie-player>
            </div>
            <div
                style="display: flex; flex-direction: column; justify-content: center; border: #bfa97a4a 1px solid; border-radius: 20px 20px 0 0 ; padding: 10px 10px 5px 10px;">
                <div class="step-progress-container not-copyable not-draggable">
                    <a class="step-group" href="../main.php#bookmark">
                        <div class="step">1</div>
                        <h3 class="step-info text-papyrus">Upload Image</h3>
                    </a>
                    <div class="line"></div>
                    <a class="step-group" href="#">
                        <div class="step active">2</div>
                        <h3 class="step-info text-papyrus">Segment Page</h3>
                    </a>
                    <div class="line"></div>
                    <a class="step-group" href="analyzeModule.php">
                        <div class="step">3</div>
                        <h3 class="step-info text-papyrus">Segment Sections</h3>
                    </a>
                    <div class="line"></div>
                    <a class="step-group" href="lettersModule.php">
                        <div class="step">4</div>
                        <h3 class="step-info text-papyrus">Segment Letters</h3>
                    </a>
                    <div class="line"></div>
                    <a class="step-group" href="editJsonModule.php">
                        <div class="step">5</div>
                        <h3 class="step-info text-papyrus">Save Document</h3>
                    </a>
                </div>
            </div>
            <!-- Process Info -->
            <h3 id="ProcessInfo" class="not-copyable text-2xl mt-4 font-bold text-center text-papyrus mb-6">
                Document position adjustment
            </h3>
            <p id="ProcessInfoMini" class="text-center text-gray-600 mb-4 pr-10 pl-10">
                The uploaded image has been analyzed, and the document's position is detected. Adjust the boundaries and
                position of the extracted document to ensure accurate content capture and further processing.
                <br>Need help? Click "Show Polygon Help" for detailed instructions or check our <a href="../../faq.php"><b><u>FAQ</u></b></a> page.
            </p> 

            <p id="noDocs"
                class="text-center text-gray-600 mt-4 pr-10 pl-10"
                style="display: none; transition: transform 0.5s ease;">
                No documents found. Please upload a document first.
            </p>

            <!-- Document Selector -->
            <div class="flex justify-center items-center mt-5">
                <input type="text" placeholder="Search for a document"
                    class="bg-[#d7c7a5] text-papyrus border border-yellow-300 rounded-lg p-2 w-1/2" id="documentSearch">
            </div>
            <!-- Item selector -->
            <div class="flex justify-center items-center mt-5">
                <select id="itemSelector" class="bg-[#d7c7a5] text-papyrus border border-yellow-300 rounded-lg p-2"
                    disabled style="display: none;">
                    <option value="" disabled selected>Select an item</option>
                </select>
            </div>

            <div id="helpBtnCont" class="flex justify-center items-center mt-4" style="display: none;">
                <button id="helpToggleButton" class="btn-papyrus text-papyrus rounded-lg p-2 transition duration-300">
                    Show Polygon Help
                </button>
            </div>
            <div id="helpContent"
                class="mt-4 mr-8 ml-8 px-10 py-4 bg-[#d7c7a5] border border-yellow-300 rounded-lg text-papyrus text-sm"
                style="visibility: hidden;">
                <h4 class="font-bold mb-2">Polygon Handling Instructions:</h4>
                <ul class="list-disc ml-5">
                    <li>The user can move the entire polygon by dragging it.</li>
                    <li>The corners of the polygon can be adjusted individually by clicking and dragging each point.
                    </li>
                    <li>Click on a polygon to view its data, change its type, or delete it.</li>
                    <li>To add a new polygon, click the "Add Segmentation Zone" button. The new polygon will be added in
                        a fixed position and can be adjusted manually.</li>
                    <li>Click "Save Segmentation" to finalize the segmentation data.</li>
                </ul>
            </div>

            <!-- Image Segmentation -->
            <div class="col-md mt-5" id="imageSegmentor" style="display: none; transition: transform 0.5s ease;">
                <div class="glass p-6 text-center relative border border-yellow-200 rounded-2xl shadow-lg">
                    <!-- Preview Image Container -->
                    <div id="previewContainerSegment" class="relative flex justify-center items-center min-h-[250px]"
                        style="position: relative;">
                        <img class="imagePreview hidden rounded-xl not-copyable"
                            style="width: 100%; height: 100%; max-width: 100%; max-height: 100%; object-fit: contain;"
                            src="" alt="Preview" draggable="false">
                    </div>
                </div>
            </div>

            <!-- Save Button -->
            <div id="btns" class="flex justify-center items-center mt-5 mb-5 gap-2"
                style="transition: transform 0.5s ease;">
                <button id="addRectButton" class="bg-[#d7c7a5] text-papyrus border border-yellow-300 rounded-lg p-2"
                    style="display: none;" onclick="addNewRect('previewContainerSegment')">Add Segmentation
                    Zone</button>
                <button id="loadItemButton" class="bg-[#d7c7a5] text-papyrus border border-yellow-300 rounded-lg p-2"
                    style="display: none;" onclick="saveSegmentionData()">Save Segmentation</button>
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
        window.phpToken = '<?php echo $_SESSION["token"]; ?>';
    </script>
    <script type="module" src="../js/segmentModule.js?v=<?= time() ?>"></script>
</body>

</html>