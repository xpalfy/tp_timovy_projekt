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
    <title>Letters</title>

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
                    <div class="line" style="background-color: #bfa97a;"></div>
                    <div class="step-group">
                        <div class="step active">4</div>
                        <h3 class="step-info text-papyrus">Letters</h3>
                    </div>
                    <div class="line" style="background-color: #bfa97a;"></div>
                    <div class="step-group">
                        <div class="step active">5</div>
                        <h3 class="step-info text-papyrus">Save</h3>
                    </div>
                </div>
            </div>
            <!-- Process Info -->
            <h3 id="ProcessInfo" class="not-copyable text-2xl mt-4 font-bold text-center text-papyrus mb-6">
                Edit Json on owned document
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

            <!-- Image JSON STEP 4 -->
            <div class="col-md mt-5 animate-fade-in-slow" id="imageJson" style="display: none;">
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
                        <textarea id="jsonEditor" rows="14"
                            class="w-full border border-yellow-300 rounded-lg p-3 font-mono bg-white text-gray-800 resize-none"
                            placeholder="{ ... }">{}</textarea>
                    </div>
                </div>
            </div>

            <!-- Save Btns -->
            <div id="DownloadJSONBtn" class="flex justify-center space-x-4 mt-6" style="display: none;">
                <button class="btn-papyrus px-4 py-2 rounded-lg shadow" onclick="saveJson()">Save Work</button>
                <button class="btn-papyrus px-4 py-2 rounded-lg shadow" onclick="downloadJSON()">Download JSON</button>
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
    <script src="../js/modules-helper.js"></script>

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
                status: 'PROCESSED'
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

        /*function filterDocuments() {
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
        }*/

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
                status: 'PROCESSED'
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

        /*function disableDocumentSearch() {
            document.getElementById('documentSearch').disabled = true;
            document.getElementById('documentSearch').style.pointerEvents = 'none';
        }

        function showItemSelector() {
            document.getElementById('itemSelector').style.display = 'block';
            hideLoading();
        }*/

        $(function () {
            $("#itemSelector").change(function () {
                showLoading();
                selectedItemId = $(this).val();
                console.log(selectedItemId);
                console.log(itemsData);
                selectedItemImagePath = itemsData.find(item => item.id == selectedItemId).image_path;
                showJsonEditor();
                fetchJson(itemsData.find(item => item.id == selectedItemId).type);
                hideLoading();
            });
        });

        /*function deletePolygons() {
            const parent = document.getElementById('previewContainerLetter');
            const polygons = parent.querySelectorAll('segment-rect');
            polygons.forEach(polygon => {
                parent.removeChild(polygon);
            });
        }*/

        function showJsonEditor() {
            document.getElementById('imageJson').style.display = 'block';
            document.getElementById('DownloadJSONBtn').style.display = 'flex';
        }

        /*function updateImagePreview() {
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
        }*/

        function fetchJson(type) {
            if (type === 'KEY') {
                fetchKeyJson();
            } else if (type === 'CIPHER') {
                fetchCipherJson();
            } else {
                toastr.error('Invalid document type');
            }
        }

        function fetchKeyJson() {
            const formData = {
                document_id: selectedDocumentId,
                user_id: userData.id,
                token: '<?php echo $_SESSION['token']; ?>'
            };

            $.ajax({
                url: 'https://python.tptimovyprojekt.software/get_json',
                type: 'POST',
                data: JSON.stringify(formData),
                contentType: 'application/json',
                dataType: 'json',
                success: function (res) {
                    if (res.error) {
                        toastr.error(res.error || 'Failed to load key JSON');
                    } else {
                        $('#jsonEditor').val(JSON.stringify(res, null, 2));
                    }
                },
                error: function () {
                    toastr.error('Server error while fetching key JSON');
                }
            });
        }

        function fetchCipherJson() {
            const formData = {
                document_id: selectedDocumentId,
                user_id: userData.id,
                token: '<?php echo $_SESSION['token']; ?>'
            };

            $.ajax({
                url: 'https://python.tptimovyprojekt.software/get_cipher_json',
                type: 'POST',
                data: JSON.stringify(formData),
                contentType: 'application/json',
                dataType: 'json',
                success: function (res) {
                    if (res.error) {
                        toastr.error(res.error || 'Failed to load cipher JSON');
                    } else {
                        $('#jsonEditor').val(JSON.stringify(res, null, 2));
                    }
                },
                error: function () {
                    toastr.error('Server error while fetching cipher JSON');
                }
            });
        }

        function saveJson() {
            showLoading();

            let fixedJson = {
                "alphabet": {
                    "a": { "codes": [0, 1, 2] },
                    "b": { "codes": [3, 4] },
                    "c": { "codes": [5, 6] },
                    "d": { "codes": [7, 8] },
                    "e": { "codes": [9, 10, 11] },
                    "f": { "codes": [12, 13] },
                    "g": { "codes": [14, 15] },
                    "h": { "codes": [16, 17] },
                    "i": { "codes": [18, 19, 20] },
                    "k": { "codes": [21, 22] },
                    "l": { "codes": [23, 24] },
                    "m": { "codes": [25, 26] },
                    "n": { "codes": [27, 28] },
                    "o": { "codes": [29, 30, 31] },
                    "p": { "codes": [32, 33] },
                    "q": { "codes": [34, 35] },
                    "r": { "codes": [36, 37] },
                    "s": { "codes": [38, 39] },
                    "t": { "codes": [40, 41] },
                    "u": { "codes": [42, 43, 44] },
                    "x": { "codes": [45, 46] },
                    "y": { "codes": [47, 48] },
                    "zeros": { "codes": [49, 50, 51, 52] }
                },
                "doubles": {
                    "bb": { "code": 53 },
                    "ff": { "code": 54 },
                    "ll": { "code": 55 },
                    "pp": { "code": 56 },
                    "mm": { "code": 57 },
                    "nn": { "code": 58 },
                    "rr": { "code": 59 },
                    "ss": { "code": 60 },
                    "tt": { "code": 61 }
                },
                "words": {
                    "Papa": { "code": 62 },
                    "Rex Ferdinandus": { "code": 63 },
                    "Veneti": { "code": 64 },
                    "Florentini": { "code": 65 },
                    "Dux uh": { "code": 66 },
                    "Dux ferrarie": { "code": 67 },
                    "Dux urbini": { "code": 68 },
                    "Comes bier": { "code": 69 },
                    "Cardinales": { "code": 70 },
                    "Concilium": { "code": 71 },
                    "Genuinfes": { "code": 72 },
                    "Maschio mantue": { "code": 73 },
                    "Impator": { "code": 74 },
                    "Rex hungarie": { "code": 75 },
                    "Rex boemie": { "code": 76 },
                    "Rex Pollane": { "code": 77 },
                    "Dux Saxonie": { "code": 78 },
                    "Maschio brandinburgi": { "code": 79 },
                    "Dux Sygimundus": { "code": 80 },
                    "Dux Burgundie": { "code": 81 },
                    "Comes pallatimus": { "code": 82 },
                    "Dux baurtie": { "code": 83 },
                    "Suyati": { "code": 84 },
                    "Soldai": { "code": 85 },
                    "La. Na. Os.": { "code": 86 }
                }
            };

            let data = {
                document_id: selectedDocumentId,
                item_id: selectedItemId,
                user_id: userData.id,
                status: 'SAVED',
                json_data: fixedJson,
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
                        toastr.success('Letter segmentation data saved successfully.');
                        window.location.href = `../edit_key/editOwnKeyDocument.php?id=${selectedDocumentId}&user=${userData.id}`;
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
    </script>
    <script type="module" src="../js/main.js?v=<?= time() ?>"></script>
</body>

</html>