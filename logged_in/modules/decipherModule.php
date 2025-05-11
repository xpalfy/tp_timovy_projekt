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
                                    <li>
                                        <a href="./decipherModule.php"
                                            class="block px-4 py-2 hover:bg-[#cbbd99]">Decipher</a>
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
            <div class="flex flex-col items-center justify-center gap-4">
                <div id="leftSide" class="flex flex-col items-center justify-center p-6 max-w-4xl mx-auto">
                    <p>Please choose the Document you want to decrypt</p>
                    <!-- Document Selector -->
                    <div class="flex justify-center items-center mt-5">
                        <input type="text" placeholder="Search for a document"
                            class="bg-[#d7c7a5] text-papyrus border border-yellow-300 rounded-lg p-2 w-1/2"
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
                <div id="rightSide" class="flex flex-col items-center justify-center p-6 max-w-4xl mx-auto"
                    style="display: none;">
                    <p>Please choose the Key you want to use for dechiper</p>
                    <!-- Document Selector -->
                    <div class="flex justify-center items-center mt-5">
                        <input type="text" placeholder="Search for a document"
                            class="bg-[#d7c7a5] text-papyrus border border-yellow-300 rounded-lg p-2 w-1/2"
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

                    <div id="KeySelector" class="flex justify-center items-center mt-5">
                        <!-- Here comes the Keys from the fetch -->
                    </div>
                </div>
                <div class="flex justify-center items-center mt-5 mb-5">
                    <button id="startDecipherBtn"
                        class="bg-[#d7c7a5] text-papyrus border border-yellow-300 rounded-lg p-2 mt-2 transition duration-300 hover:bg-yellow-300 hover:text-[#d7c7a5]"
                        style="display: none;" onclick="startDecipher()">Start Dechiper</button>
                </div>

                <!-- Result Area -->
                <div id="resultArea" class="flex justify-center items-center mb-5 mt-5 w-full relative" style="display: none;">
                    <h3 class="text-2xl font-bold text-center text-papyrus mb-6">Decrypted Result</h3>
                    <div class="bg-[#d7c7a5] text-papyrus border border-yellow-300 rounded-lg p-4 mr-5 ml-5">
                        <textarea id="resultText" class="w-full h-auto"
                            style="padding: 5px; padding-right: 25px; min-height: 60px;">Decrypted text will be shown here.</textarea>
                        <button id="copyToClipboardBtn"
                            class="rounded-lg p-1 transition duration-300 hover:bg-gray-100 absolute" style="top: 78px; right: 40px;"
                            onclick="copyToClipboard()"><img src="../../img/copy.png" width="20px" height="20px"></button>
                    </div>
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
    <script type="module" src="../js/main.js?v=<?= time() ?>"></script>

    <script>
        const maxSelectItemSize = 10;
        let DocumentsData = [];
        let selectedKeyDocumentId = null;
        let selectedCipherDocumentId = null;
        let itemsDataKey = [];
        let itemsDataCipher = [];
        let selectedKeyItemId = null;
        let selectedCipherItemId = null;
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

            let data = {
                token: '<?php echo $_SESSION["token"]; ?>',
                user_id: userData.id,
                status: 'SAVED'
            };

            showLoading();
            fetch('https://python.tptimovyprojekt.software/get_documents_by_user_and_status', { // Itt kéne a public is 
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json'
                },
                body: JSON.stringify(data)
            })
                .then(response => response.json())
                .then(docs => {

                    hideLoading();
                    DocumentsData = docs;
                    const params = getUrlParams();
                    if (params.cipher_doc_id) {
                        const selectedDoc = DocumentsData.find(doc => doc.id == params.cipher_doc_id);
                        if (selectedDoc) {
                            selectedCipherDocumentId = selectedDoc.id;
                            $("#documentSearchCipher").val(selectedDoc.title);
                            $("#itemSelectorCipher").prop("disabled", false);
                            $("#itemSelectorCipher").empty();
                            $("#itemSelectorCipher").append('<option value="" disabled selected>Select an item</option>');
                            fetchItems(selectedCipherDocumentId, params.cipher_item_id, "CIPHER");
                        }
                    }
                })
                .catch(error => {
                    hideLoading();
                    toastr.error('Failed to load documents.');
                    console.error('Error fetching documents:', error);
                });
        }

        $(function () {
            fetchDocuments();
            $("#documentSearchKey").autocomplete({
                source: function (request, response) {
                    const term = request.term.toLowerCase();
                    const filtered = DocumentsData.filter(doc => doc.title.toLowerCase().includes(term) && doc.doc_type === 'KEY');
                    response(filtered.map(doc => ({
                        label: doc.title,
                        value: doc.title,
                        id: doc.id
                    })));
                },
                select: function (event, ui) {
                    selectedKeyDocumentId = ui.item.id;
                    $("#itemSelectorKey").prop("disabled", false);
                    $("#itemSelectorKey").empty();
                    $("#itemSelectorKey").append('<option value="" disabled selected>Select an item</option>');
                    fetchItems(selectedKeyDocumentId, null, "KEY");
                },
                minLength: 1
            });
            $("#documentSearchCipher").autocomplete({
                source: function (request, response) {
                    const term = request.term.toLowerCase();
                    const filtered = DocumentsData.filter(doc => doc.title.toLowerCase().includes(term) && doc.doc_type === 'CIPHER');
                    response(filtered.map(doc => ({
                        label: doc.title,
                        value: doc.title,
                        id: doc.id
                    })));
                },
                select: function (event, ui) {
                    selectedCipherDocumentId = ui.item.id;
                    $("#itemSelectorCipher").prop("disabled", false);
                    $("#itemSelectorCipher").empty();
                    $("#itemSelectorCipher").append('<option value="" disabled selected>Select an item</option>');
                    fetchItems(selectedCipherDocumentId, null, "CIPHER");
                },
                minLength: 1
            });
        });

        function fetchItems(documentId, preselectItemId = null, type) {
            disableDocumentSearch(type);
            showItemSelector(type);

            let data = {
                token: '<?php echo $_SESSION["token"]; ?>',
                user_id: userData.id,
                document_id: documentId,
                status: 'SAVED'
            };

            console.log('Requesting items with:', data);
            showLoading();

            fetch('https://python.tptimovyprojekt.software/get_items_by_doc_and_status', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json'
                },
                body: JSON.stringify(data)
            })
                .then(response => response.json())
                .then(items => {
                    hideLoading();
                    console.log('Fetched items:', items);

                    $("#itemSelectorKey").empty();
                    $("#itemSelectorKey").append('<option value="" disabled selected>Select an item</option>');

                    items.forEach(item => {
                        const option = document.createElement('option');
                        option.value = item.id;
                        option.textContent = item.title;
                        if (item.type == type) {
                            if (type == 'KEY') {
                                document.getElementById('itemSelectorKey').appendChild(option);
                            } else {
                                document.getElementById('itemSelectorCipher').appendChild(option);
                            }
                        }
                    });

                    itemsData = items;

                    if (preselectItemId) {
                        if (type == 'KEY') {
                            $("#itemSelectorKey").val(preselectItemId).trigger('change');
                        } else {
                            $("#itemSelectorCipher").val(preselectItemId).trigger('change');
                        }
                    }
                })
                .catch(error => {
                    toastr.error('Failed to load items.');
                    console.error('Error fetching items:', error);
                });
        }

        function disableDocumentSearch(type) {
            if (type === 'KEY') {
                document.getElementById('documentSearchKey').disabled = true;
                document.getElementById('documentSearchKey').style.pointerEvents = 'none';
            } else {
                document.getElementById('documentSearchCipher').disabled = true;
                document.getElementById('documentSearchCipher').style.pointerEvents = 'none';
            }
        }

        function showItemSelector(type) {
            if (type === 'KEY') {
                document.getElementById('itemSelectorKey').style.display = 'block';
            } else {
                document.getElementById('itemSelectorCipher').style.display = 'block';
            }
        }

        $(function () {
            $("#itemSelectorKey").change(function () {
                showLoading();
                selectedKeyItemId = $(this).val();
                showSelectedItem("KEY");
                document.getElementById('KeySelector').style.display = 'none';
                document.getElementById('recommendMessage').style.display = 'none';
                document.getElementById('startDecipherBtn').style.display = 'block';
                hideLoading();
            });
        });

        $(function () {
            $("#itemSelectorCipher").change(function () {
                showLoading();
                document.getElementById('rightSide').style.display = 'flex';
                fetchKeys();
                selectedCipherItemId = $(this).val();
                showSelectedItem("CIPHER");
                hideLoading();
            });
        });

        function fetchKeys() {
            let data = {
                token: '<?php echo $_SESSION["token"]; ?>',
                user_id: userData.id,
                document_id: selectedCipherDocumentId
            };

            showLoading();
            fetch('https://python.tptimovyprojekt.software/get_keys_for_cipher', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json'
                },
                body: JSON.stringify(data)
            })
                .then(response => response.json())
                .then(items => {
                    hideLoading();
                    itemsDataKey = items;
                    const keySelector = document.getElementById('KeySelector');
                    keySelector.innerHTML = ''; // Clear previous options
                    items.forEach(item => {
                        const card = document.createElement('div');
                        card.className = 'bg-[#d7c7a5] text-papyrus border border-yellow-300 rounded-lg p-4 m-2 flex flex-col items-center w-48 h-64';

                        card.innerHTML = `
        <div class="w-full h-36 flex justify-center items-center overflow-hidden bg-[#f0e7d5] rounded-lg">
            <img src="../..${item.image_path}" alt="${item.title}" class="w-full h-full object-cover">
        </div>
        <p class="mt-2 text-center font-semibold">${item.title}</p>
        <button class="bg-[#d7c7a5] text-[#4b4b4b] border border-[#4b4b4b] rounded-lg p-2 mt-2 transition duration-300 hover:bg-[#c4b59d] hover:text-[#2d2d2d]" onclick="selectKey(${item.document_id})">Select</button>
    `;

                        keySelector.appendChild(card);
                    });


                    if (items.length > maxSelectItemSize) {
                        keySelector.style.overflowY = 'scroll';
                        keySelector.style.maxHeight = '300px';
                    } else {
                        keySelector.style.overflowY = 'hidden';
                        keySelector.style.maxHeight = 'none';
                    }
                })
                .catch(error => {
                    toastr.error('Failed to load keys.');
                    console.error('Error fetching keys:', error);
                });
        }

        function showSelectedItem(type) {
            const selectedItem = itemsData.find(item => item.id == (type === "KEY" ? selectedKeyItemId : selectedCipherItemId));
            if (selectedItem) {
                selectedItemImagePath = '../..' + selectedItem.image_path;
                if (type === "KEY") {
                    document.getElementById('imagePreviewKey').src = selectedItemImagePath;
                    document.getElementById('imagePreviewKey').style.display = 'block';
                    document.getElementById('documentSearchKey').style.display = 'none';
                    document.getElementById('itemSelectorKey').style.display = 'none';
                } else {
                    document.getElementById('imagePreviewCipher').src = selectedItemImagePath;
                    document.getElementById('imagePreviewCipher').style.display = 'block';
                    document.getElementById('documentSearchCipher').style.display = 'none';
                    document.getElementById('itemSelectorCipher').style.display = 'none';
                }
            }
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

        function selectKey(keyId) {
            selectedKeyDocumentId = keyId;
            const selectedKey = itemsDataKey.find(item => item.document_id == selectedKeyDocumentId);
            if (selectedKey) {
                selectedItemImagePath = '../..' + selectedKey.image_path;
                document.getElementById('imagePreviewKey').src = selectedItemImagePath;
                document.getElementById('imagePreviewKey').style.display = 'block';
                document.getElementById('documentSearchKey').style.display = 'none';
                document.getElementById('itemSelectorKey').style.display = 'none';
                document.getElementById('KeySelector').style.display = 'none';
                document.getElementById('recommendMessage').style.display = 'none';
                document.getElementById('startDecipherBtn').style.display = 'block';
            }
        }

        function startDecipher() {
            let data = {
                token: '<?php echo $_SESSION["token"]; ?>',
                user_id: userData.id,
                cipher_document_id: selectedCipherDocumentId,
                key_document_id: selectedKeyDocumentId,
            };

            console.log('Requesting items with:', data);
            showLoading();

            fetch('https://python.tptimovyprojekt.software/decrypt_cipher_with_key', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json'
                },
                body: JSON.stringify(data)
            })
                .then(response => response.json())
                .then(item => {
                    hideLoading();
                    console.log('Fetched items:', item);
                    document.getElementById('startDecipherBtn').style.display = 'none';
                    document.getElementById('resultArea').style.display = 'block';
                    setDecryptResult(item.decrypted);
                })
                .catch(error => {
                    hideLoading();
                    toastr.error('Failed to load items.');
                    console.error('Error fetching items:', error);
                });
        }

        function setDecryptResult(decryptResult) {
            const resultText = document.getElementById('resultText');
            resultText.innerHTML = decryptResult;
        }

        function copyToClipboard() {
            const resultText = document.getElementById('resultText');
            resultText.select();
            document.execCommand("copy");
            toastr.success('Copied to clipboard!');
        }

    </script>
</body>

</html>