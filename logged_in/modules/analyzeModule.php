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
    <title>Analyze</title>

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
    <script src="../../js/letter-rect.js?v=<?php echo time() ?>" type="module"></script>

</head>

<body class="min-h-screen flex flex-col not-copyable not-draggable text-papyrus">
    <!-- Navbar -->
    <nav class="sticky top-0 z-50 w-full transition-all duration-300 bg-[#d7c7a5] border-b border-yellow-300 shadow-md not-copyable not-draggable"
        id="navbar">
        <div class="container mx-auto flex flex-wrap items-center justify-between py-3 px-4">
            <a href="../main.php"
                class="flex items-center text-papyrus text-2xl font-bold hover:underline animate-slide-left">
                <img src="../../img/logo.png" alt="Logo" class="w-10 h-10 mr-3"
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
                        <a href="../profile.php" class="nav-link flex items-center hover:underline">
                            Profile
                            <img src="../../img/account.png" alt="profile" class="w-6 h-6 ml-2"
                                style="filter: brightness(0) saturate(100%) invert(15%) sepia(56%) saturate(366%) hue-rotate(357deg) brightness(98%) contrast(93%);">
                        </a>
                    </li>
                    <li class="flex items-center ml-6">
                        <div class="relative flex items-center">
                            <button id="dropdownDocumentsButton" data-dropdown-toggle="dropdownDocuments"
                                class="hover:underline flex items-center">
                                Documents
                                <img src="../../img/document.png" alt="document" class="w-6 h-6 ml-2"
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
                                        <a href="../ownKeyDocuments.php" class="block px-4 py-2 hover:bg-[#cbbd99]">Key
                                            Documents</a>
                                    </li>
                                    <li>
                                        <a href="../ownCipherDocuments.php"
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
                                <img src="../../img/tools.png" alt="tools" class="w-6 h-6 ml-2"
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
                                        <a href="./segmentModule.php"
                                            class="block px-4 py-2 hover:bg-[#cbbd99]">Segment</a>
                                    </li>
                                    <li>
                                        <a href="./analyzeModule.php"
                                            class="block px-4 py-2 hover:bg-[#cbbd99]">Analyze</a>
                                    </li>
                                    <li>
                                        <a href="./lettersModule.php"
                                            class="block px-4 py-2 hover:bg-[#cbbd99]">Letters</a>
                                    </li>
                                    <li>
                                        <a href="./editJsonModule.php" class="block px-4 py-2 hover:bg-[#cbbd99]">Edit
                                            Json</a>
                                    </li>
                                </ul>
                            </div>
                        </div>
                    </li>
                    <li class="flex items-center ml-6">
                        <a href="../../logout.php" class="nav-link flex items-center hover:underline">
                            Logout
                            <img src="../../img/logout.png" alt="logout" class="w-6 h-6 ml-2"
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
        <!-- Loading Overlay -->
        <div class="loading-cont not-copyable not-draggable"
            style="overflow: hidden; position: absolute; left: 0; right: 0; bottom: 0; top: 0; display: none; justify-content: center; align-items: center; border-radius: 20px; background-color:rgba(115, 124, 133, 0.52); z-index: 3;">
            <dotlottie-player src="https://lottie.host/4f6b3ace-c7fc-45e9-85a2-c1fe04047ae3/QLPJzOha5m.lottie"
                background="transparent" speed="1" style="width: 150px; height: 150px;" loop
                autoplay></dotlottie-player>
        </div>
        <div class="glass max-w-4xl mx-auto animate-fade-in-slow border-yellow-300 border">
            <div
                style="display: flex; flex-direction: column; justify-content: center; border: #bfa97a4a 1px solid; border-radius: 20px 20px 0 0 ; padding: 10px 10px 5px 10px;">
                <div class="step-progress-container not-copyable not-draggable">
                    <div class="step-group">
                        <div class="step active">1</div>
                        <h3 class="step-info text-papyrus">Upload</h3>
                    </div>
                    <div class="line" style="background-color: #bfa97a;"></div>
                    <div class="step-group">
                        <div class="step active">2</div>
                        <h3 class="step-info text-papyrus">Segment</h3>
                    </div>
                    <div class="line" style="background-color: #bfa97a;"></div>
                    <div class="step-group">
                        <div class="step active">3</div>
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
            <h3 id="ProcessInfo" class="not-copyable text-2xl mt-4 font-bold text-center text-papyrus mb-6">
                Analyze owned document
            </h3>

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

            <!-- Image Analyzer -->
            <div class="col-md mt-5 animate-fade-in-slow" id="imageAnalyzer" style="display: none;">
                <div class="glass p-6 text-center relative border border-yellow-200 rounded-2xl shadow-lg">
                    <!-- Preview Image Container -->
                    <div id="previewContainerAnalyze" class="relative flex justify-center items-center min-h-[250px]"
                        style="position: relative;">
                        <img class="imagePreview hidden rounded-xl not-copyable"
                            style="width: 100%; height: 100%; max-width: 100%; max-height: 100%; object-fit: contain;"
                            src="" alt="Preview" draggable="false">

                    </div>
                </div>
            </div>

            <!-- Save Button -->
            <div class="flex justify-center items-center mt-5 mb-5 gap-2">
                <button id="addRectButton" class="bg-[#d7c7a5] text-papyrus border border-yellow-300 rounded-lg p-2"
                    style="display: none;" onclick="addNewRect()">Add Segmentation Zone</button>
                <button id="loadItemButton" class="bg-[#d7c7a5] text-papyrus border border-yellow-300 rounded-lg p-2"
                    style="display: none;" onclick="saveAnalysisData()">Save Analysis</button>
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

    <script>
        const maxSelectItemSize = 10;
        let documentsData = [];
        let selectedDocumentId = null;
        let itemsData = [];
        let selectedItemId = null;
        let selectedItemImagePath = null;

        function getUrlParams() {
            const params = {};
            const queryString = window.location.search.substring(1);
            const vars = queryString.split("&");

            vars.forEach(function (v) {
                const pair = v.split("=");
                params[decodeURIComponent(pair[0])] = decodeURIComponent(pair[1]);
            });

            return params;
        }

        function fetchDocuments() {
            showLoading();

            let data = {
                token: '<?php echo $_SESSION["token"]; ?>',
                user_id: userData.id,
                status: 'SEGMENTED'
            };

            fetch('https://python.tptimovyprojekt.software/get_documents_by_user_and_status', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json'
                },
                body: JSON.stringify(data)
            })
                .then(response => response.json())
                .then(docs => {
                    hideLoading();

                    documentsData = docs;

                    const params = getUrlParams();
                    if (params.document_id) {
                        const selectedDoc = documentsData.find(doc => doc.id == params.document_id);
                        if (selectedDoc) {
                            selectedDocumentId = selectedDoc.id;
                            $("#documentSearch").val(selectedDoc.title);
                            $("#itemSelector").prop("disabled", false);
                            $("#itemSelector").empty();
                            $("#itemSelector").append('<option value="" disabled selected>Select an item</option>');
                            fetchItems(selectedDocumentId, params.item_id);
                        }
                    }
                })
                .catch(error => {
                    toastr.error('Failed to load documents.');
                    console.error('Error fetching documents:', error);
                });
            hideLoading();
        }

        function filterDocuments() {
            const searchTerm = document.getElementById('documentSearch').value.toLowerCase();
            const filteredDocuments = documentsData.filter(doc => doc.title.toLowerCase().includes(searchTerm));
            return filteredDocuments;
        }

        function showFilteredDocuments() {
            const filteredDocuments = filterDocuments();
            const documentList = document.getElementById('documentList');
            documentList.innerHTML = '';

            filteredDocuments.forEach(doc => {
                const option = document.createElement('option');
                option.value = doc.id;
                option.textContent = doc.title;
                documentList.appendChild(option);
            });
        }

        $(function () {
            fetchDocuments();
            $("#documentSearch").autocomplete({
                source: function (request, response) {
                    const term = request.term.toLowerCase();
                    const filtered = documentsData.filter(doc => doc.title.toLowerCase().includes(term));
                    response(filtered.map(doc => ({
                        label: doc.title + ' (' + doc.doc_type.toLowerCase() + ')',
                        value: doc.title,
                        id: doc.id
                    })));
                },
                select: function (event, ui) {
                    showLoading();
                    selectedDocumentId = ui.item.id;
                    $("#itemSelector").prop("disabled", false);
                    $("#itemSelector").empty();
                    $("#itemSelector").append('<option value="" disabled selected>Select an item</option>');
                    fetchItems(selectedDocumentId);
                },
                minLength: 1
            });
        });

        function fetchItems(documentId, preselectItemId = null) {
            disableDocumentSearch();
            showItemSelector();

            let data = {
                token: '<?php echo $_SESSION["token"]; ?>',
                user_id: userData.id,
                document_id: documentId,
                status: 'SEGMENTED'
            };

            console.log('Requesting items with:', data);

            fetch('https://python.tptimovyprojekt.software/get_items_by_doc_and_status', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json'
                },
                body: JSON.stringify(data)
            })
                .then(response => response.json())
                .then(items => {
                    console.log('Fetched items:', items);

                    $("#itemSelector").empty();
                    $("#itemSelector").append('<option value="" disabled selected>Select an item</option>');

                    items.forEach(item => {
                        const option = document.createElement('option');
                        option.value = item.id;
                        option.textContent = item.title;
                        document.getElementById('itemSelector').appendChild(option);
                    });

                    itemsData = items;

                    if (preselectItemId) {
                        $("#itemSelector").val(preselectItemId).trigger('change');
                    }
                })
                .catch(error => {
                    toastr.error('Failed to load items.');
                    console.error('Error fetching items:', error);
                });
        }


        function disableDocumentSearch() {
            document.getElementById('documentSearch').disabled = true;
            document.getElementById('documentSearch').style.pointerEvents = 'none';
        }

        function showItemSelector() {
            document.getElementById('itemSelector').style.display = 'block';
            hideLoading();
        }

        $(function () {
            $("#itemSelector").change(function () {
                showLoading();
                selectedItemId = $(this).val();
                console.log(selectedItemId);
                console.log(itemsData);
                selectedItemImagePath = itemsData.find(item => item.id == selectedItemId).image_path;
                showAnalyzer();
                updateImagePreview();
                deletePolygons();
                CalculateAnalysis();
                hideLoading();
            });
        });

        function deletePolygons() {
            const parent = document.getElementById('previewContainerAnalyze');
            const polygons = parent.querySelectorAll('segment-rect');
            polygons.forEach(polygon => {
                parent.removeChild(polygon);
            });
        }

        function showAnalyzer() {
            document.getElementById('imageAnalyzer').style.display = 'block';
            document.getElementById('loadItemButton').style.display = 'block';
            document.getElementById('addRectButton').style.display = 'block';
        }

        function updateImagePreview() {
            const previewImage = document.querySelector('.imagePreview');
            previewImage.src = '../..' + selectedItemImagePath;
            previewImage.style.display = 'block';
        }

        function hideLoading() {
            loadings = document.getElementsByClassName('loading-cont');
            for (let loading of loadings) {
                loading.style.display = 'none';
            }
        }

        function showLoading() {
            loadings = document.getElementsByClassName('loading-cont');
            for (let loading of loadings) {
                loading.style.display = 'flex';
            }
        }

        function CalculateAnalysis() {
            showLoading();

            const imagePath = 'path/to/your/image.jpg';

            fetch('https://python.tptimovyprojekt.software/segmentate_sections', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json'
                },
                body: JSON.stringify({ path: imagePath })
            })
                .then(response => response.json())
                .then(data => {
                    hideLoading();

                    if (data && Array.isArray(data.polygons)) {
                        appendAnalyzedRects(data.polygons); // Access the polygons array
                    } else {
                        console.error('Invalid response from server:', data);
                    }
                })
                .catch(error => {
                    hideLoading();
                    console.error('Error detecting page edges:', error);
                });
        }

        function appendAnalyzedRects(segments) {
            const parent = document.getElementById('previewContainerAnalyze');

            for (let segment of segments) {
                const polygon = segment.polygon;
                const type = segment.type;

                if (!polygon || polygon.length < 4) {
                    console.error('Invalid polygon:', segment);
                    continue;
                }

                const x1 = polygon[0], y1 = polygon[1];
                const x3 = polygon[2], y3 = polygon[3];
                const x2 = x1, y2 = y3;
                const x4 = x3, y4 = y1;

                let newRect = document.createElement('segment-rect');
                newRect.setAttribute('x1', x1);
                newRect.setAttribute('y1', y1);
                newRect.setAttribute('x2', x2);
                newRect.setAttribute('y2', y2);
                newRect.setAttribute('x3', x3);
                newRect.setAttribute('y3', y3);
                newRect.setAttribute('x4', x4);
                newRect.setAttribute('y4', y4);
                newRect.setAttribute('type', type || 'unknown');

                parent.appendChild(newRect);
            }
        }

        function saveAnalysisData() {
            showLoading();

            let rects = document.querySelectorAll('segment-rect');
            let polygons = [];

            rects.forEach(rect => {
                let polygon = [
                    { x: rect.getAttribute('x1'), y: rect.getAttribute('y1') },
                    { x: rect.getAttribute('x2'), y: rect.getAttribute('y2') },
                    { x: rect.getAttribute('x3'), y: rect.getAttribute('y3') },
                    { x: rect.getAttribute('x4'), y: rect.getAttribute('y4') },
                    { type: rect.getAttribute('type') }
                ];
                polygons.push(polygon);
            });

            let data = {
                document_id: selectedDocumentId,
                item_id: selectedItemId,
                user_id: userData.id,
                status: 'CLASSIFIED',
                polygons: polygons,
                token: '<?php echo $_SESSION["token"]; ?>'
            };

            console.log('Data to be sent:', data);

            fetch('https://python.tptimovyprojekt.software/save_processing_result', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json'
                },
                body: JSON.stringify(data)
            })
                .then(response => response.json())
                .then(data => {
                    hideLoading();
                    if (data.success) {
                        toastr.success('Analysis data saved successfully.');
                        goToLetterSegmentation(selectedDocumentId, selectedItemId);
                    } else {
                        toastr.error('Failed to save segmentation data.');
                    }
                })
                .catch(error => {
                    hideLoading();
                    toastr.error('Error saving segmentation data.');
                    console.error('Error:', error);
                });
        }

        function addNewRect() {
            const parent = document.getElementById('previewContainerAnalyze');
            const newRect = document.createElement('segment-rect');
            newRect.setAttribute('x1', 100);
            newRect.setAttribute('y1', 100);
            newRect.setAttribute('x2', 200);
            newRect.setAttribute('y2', 100);
            newRect.setAttribute('x3', 200);
            newRect.setAttribute('y3', 200);
            newRect.setAttribute('x4', 100);
            newRect.setAttribute('y4', 200);
            newRect.setAttribute('type', 'default');

            parent.appendChild(newRect);
        }
    </script>
    <script type="module" src="../js/main.js?v=<?= time() ?>"></script>
</body>

</html>