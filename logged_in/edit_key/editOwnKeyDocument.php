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

$fullCallerUrl = (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? 'https' : 'http') .
    '://' . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI'];

?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8"/>
    <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
    <title>HandScript - Edit Document</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://unpkg.com/aos@2.3.1/dist/aos.css">
    <script src="https://unpkg.com/aos@2.3.1/dist/aos.js"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.css">
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.6.0/jquery.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.js"></script>
    <script src="https://code.jquery.com/ui/1.13.2/jquery-ui.min.js"></script>
    <link rel="stylesheet" href="https://cdn.datatables.net/1.13.6/css/jquery.dataTables.min.css">
    <script src="https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/flowbite@1.6.5/dist/flowbite.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <link rel="stylesheet" href="../../css/editDocument.css?v=<?php echo time(); ?>"/>
</head>

<body class="bg-gradient-to-br from-[#ede1c3] to-[#cdbf9b] text-papyrus min-h-screen flex flex-col select-none">
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
                <path d="M4 6h16M4 12h16M4 18h16"/>
            </svg>
        </button>

        <div class="w-full lg:flex lg:items-center lg:w-auto hidden lg:mt-1" id="navbarNav">
            <ul class="flex flex-col lg:flex-row w-full text-lg font-medium text-papyrus animate-slide-right gap-x-6">
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
                                      stroke-width="2" d="M1 1l4 4 4-4"/>
                            </svg>
                        </button>
                        <div id="dropdownDocuments"
                             class="z-10 hidden font-normal bg-[#d7c7a5] divide-y divide-gray-100 rounded-lg shadow w-44 absolute top-full mt-2">
                            <ul class="py-2 text-sm text-[#3b2f1d]">
                                <li><a href="../ownKeyDocuments.php" class="block px-4 py-2 hover:bg-[#cbbd99]">Cipher
                                        Keys</a></li>
                                <li><a href="../ownCipherDocuments.php" class="block px-4 py-2 hover:bg-[#cbbd99]">Encrypted
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
                                      stroke-width="2" d="M1 1l4 4 4-4"/>
                            </svg>
                        </button>
                        <div id="dropdownTools"
                             class="z-10 hidden font-normal bg-[#d7c7a5] divide-y divide-gray-100 rounded-lg shadow w-44 absolute top-full mt-2">
                            <ul class="py-2 text-sm text-[#3b2f1d]">
                                <li><a href="../modules/segmentModule.php" class="block px-4 py-2 hover:bg-[#cbbd99]">Segment</a>
                                </li>
                                <li><a href="../modules/analyzeModule.php" class="block px-4 py-2 hover:bg-[#cbbd99]">Analyze</a>
                                </li>
                                <li><a href="../modules/lettersModule.php" class="block px-4 py-2 hover:bg-[#cbbd99]">Letters</a>
                                </li>
                                <li><a href="../modules/editJsonModule.php" class="block px-4 py-2 hover:bg-[#cbbd99]">Edit
                                        Json</a></li>
                                <li><a href="../modules/decipherModule.php" class="block px-4 py-2 hover:bg-[#cbbd99]">Decipher</a>
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
<main class="flex-grow container mx-auto px-4 py-10">
    <h1 id="docTitle" class="text-4xl font-bold text-center mb-10">Edit Your Own Key Document Here</h1>


    <!-- Top Row: Image + JSON Textarea -->
    <div class="flex flex-col lg:flex-row gap-10 items-stretch">
        <!-- Left: Image -->
        <div class="w-full lg:w-1/2 flex">
            <div class="w-full">
                <img id="docImage" src="" alt="Document"
                     class="w-full h-full object-cover rounded-lg border shadow-lg cursor-pointer"
                     onclick="openImageModal()"/>
            </div>
        </div>


        <!-- Left: Form section (under image) -->
        <div class="w-full lg:w-1/2 flex">
            <div class="w-full bg-white bg-opacity-50 rounded-xl p-6 shadow-lg min-h-[450px] flex flex-col">
                <input type="hidden" name="id" id="docId">
                    <input type="hidden" name="item_id" id="itemId">
                <input type="hidden" name="user" id="userId">

                <div>
                    <label for="date" class="block font-semibold mb-1">Publish Date</label>
                    <div class="flex items-center gap-2 mb-2">
                        <input type="text" name="date" id="date"
                               class="flex-grow border border-yellow-400 rounded px-4 py-2"/>
                    </div>
                </div>

                
                <!-- Row 3: Historical Author + Historical Date -->
                <div class="flex flex-col md:flex-row gap-4 mt-4">
                    <div class="w-full md:w-1/2">
                        <label for="historical_author" class="block font-semibold mb-1">Historical Author</label>
                        <input type="text" name="historical_author" id="historical_author"
                            class="w-full border border-yellow-400 rounded px-4 py-2 mb-2"/>
                    </div>
                    <div class="w-full md:w-1/2">
                        <label for="historical_date" class="block font-semibold mb-1">Historical Date</label>
                        <input type="text" name="historical_date" id="historical_date"
                            class="w-full border border-yellow-400 rounded px-4 py-2 mb-2"/>
                    </div>
                </div>

                <!-- Row 4: Country + Language -->
                <div class="flex flex-col md:flex-row gap-4 mt-4">
                    <div class="w-full md:w-1/2">
                        <label for="country" class="block font-semibold mb-1">Country</label>
                        <input type="text" name="country" id="country"
                            class="w-full border border-yellow-400 rounded px-4 py-2 mb-2"/>
                    </div>
                    <div class="w-full md:w-1/2">
                        <label for="language" class="block font-semibold mb-1">Language</label>
                        <input type="text" name="language" id="language"
                            class="w-full border border-yellow-400 rounded px-4 py-2 mb-2"/>
                    </div>
                </div>

                <!-- Document Name -->
                <div>
                    <label for="name" class="block font-semibold mb-1 mt-4">Document Name</label>
                    <div class="flex items-center gap-2">
                        <input type="text" name="name" id="name" placeholder="Enter document name"
                               class="flex-grow border border-yellow-400 rounded px-4 py-2"/>
                        <button type="button" onclick="changeName()"
                                class="px-4 py-2 bg-yellow-300 text-[#3b2f1d] rounded shadow hover:bg-yellow-400 transition">
                            Change
                        </button>
                    </div>
                </div>

                <!-- Share with section -->
                <label for="share" class="block font-semibold mb-1 mt-4">Share with</label>
                <div class="flex flex-col lg:flex-row gap-4 mb-2">
                    <div class="lg:w-5/6 w-full">
                        <div class="flex flex-col sm:flex-row gap-2">
                            <input type="text" id="share" placeholder="Enter username"
                                   class="flex-grow border border-yellow-400 rounded px-4 py-2"/>
                            <button type="button" onclick="addUser()"
                                    class="px-4 py-2 bg-yellow-300 text-[#3b2f1d] rounded shadow hover:bg-yellow-400 transition">
                                Add
                            </button>
                        </div>
                        <input type="hidden" name="sharedUsers" id="sharedUsers">
                    </div>
                    <div class="lg:w-1/6 w-full flex items-center justify-center">
                        <label for="isPublic" class="flex items-center cursor-pointer select-none">
                            <div class="relative">
                                <input type="checkbox" id="isPublic" class="sr-only peer">
                                <div class="w-12 h-6 bg-gray-300 rounded-full peer-checked:bg-yellow-400 transition-colors duration-300"></div>
                                <div class="absolute top-0.5 left-0.5 w-5 h-5 bg-white rounded-full transition-transform duration-300 peer-checked:translate-x-6"></div>
                            </div>
                            <span class="ml-3 text-sm font-medium text-gray-700">Public</span>
                        </label>
                    </div>
                </div>

                <!-- Shared users table -->
                <div class="flex-grow overflow-y-auto mt-8">
                    <table id="sharedUsersTable" class="display w-full text-sm compact">
                        <thead>
                        <tr>
                            <th>Username</th>
                            <th>Actions</th>
                        </tr>
                        </thead>
                        <tbody id="sharedUsersTableBody"></tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <!-- Bottom Row: Form (under image) + JSON Helper (under textarea) -->
    <div class="flex flex-col lg:flex-row gap-10 items-stretch mt-10">

        <!-- Right: JSON Textarea -->
        <div class="w-full lg:w-1/2 flex">
            <div class="bg-white bg-opacity-50 rounded-xl p-6 shadow-lg w-full">
                <label for="jsonData" class="block font-semibold mb-2 text-[#3b2f1d]">Key JSON</label>
                <textarea id="jsonData" rows="15"
                          class="w-full border border-yellow-400 rounded px-4 py-2 text-sm font-mono bg-white bg-opacity-70 mb-4 resize-none"
                          placeholder="{ }"></textarea>
                <div class="text-right mt-4">
                    <button onclick="continueProcessing()" id="continueProcessing"
                        class="px-4 py-2 bg-green-500 hover:bg-green-600 text-white rounded shadow transition hidden">
                        Continue Processing
                    </button>
                    <button onclick="saveKeyJson()"
                            class="px-4 py-2 bg-blue-500 hover:bg-blue-600 text-white rounded shadow transition ml-2">
                        Save JSON
                    </button>
                    <button onclick="deleteDocument(document.getElementById('docId').value)"
                            class="px-4 py-2 bg-red-500 hover:bg-red-600 text-white rounded shadow transition ml-2">
                        Delete Document
                    </button>
                </div>
            </div>
        </div>

        <!-- Right: Help panel (under textarea) -->
        <div class="w-full lg:w-1/2 flex">
            <div class="bg-yellow-100 bg-opacity-70 rounded-xl p-6 shadow-lg w-full min-h-[450px] flex flex-col justify-between">
                <div>
                    <h2 class="text-xl font-semibold mb-4 text-[#3b2f1d]">How to Write a JSON Document</h2>
                    <ul class="list-disc list-inside text-sm text-gray-800 mb-10">
                        <li>Use double quotes <code class="text-yellow-900">"</code> for keys and string values</li>
                        <li>Keys and values are separated by a colon <code class="text-yellow-900">:</code></li>
                        <li>Separate entries with commas <code class="text-yellow-900">,</code></li>
                        <li>Wrap your structure in <code class="text-yellow-900">{ }</code></li>
                        <li>Use arrays <code class="text-yellow-900">[ ]</code> for lists</li>
                    </ul>

                    <h3 class="font-semibold mb-2 text-[#3b2f1d]">Example 1: Single Object</h3>
                    <pre class="bg-white bg-opacity-90 rounded px-4 py-2 text-sm font-mono text-gray-800 overflow-x-auto mb-4">
[
  {
    "key": "Batman",
    "code": "BAT-001"
  },
  {
    "key": "Wonder Woman",
    "code": "WW-002"
  }
]
                </pre>
                </div>
            </div>
        </div>
    </div>

    <!-- Fullscreen Image Modal -->
    <div id="imageModal"
         class="fixed inset-0 z-50 hidden items-center justify-center bg-black bg-opacity-80 backdrop-blur-sm p-6 overflow-auto">
        <button onclick="closeImageModal()"
                class="absolute top-5 right-5 text-white text-3xl font-bold z-50">&times;
        </button>
        <img id="modalImage" src=""
             alt="Full Image"
             class="max-w-full max-h-[90vh] rounded-lg shadow-lg border-4 border-white"/>
    </div>


</main>

<!-- Footer -->
<footer class="bg-[#d7c7a5] border-t border-yellow-300 text-[#3b2f1d] py-6">
    <div class="max-w-7xl mx-auto px-4 flex flex-col md:flex-row justify-between items-center space-y-4 md:space-y-0">
        <p class="text-center md:text-left">&copy; 2025 HandScript</p>
        <div class="flex space-x-4 text-sm">
            <a href="https://tptimovyprojekt.ddns.net/" class="underline hover:text-[#5a452e] transition">Visit Project
                Page</a>
            <a href="../../faq.php" class="underline hover:text-[#5a452e] transition">FAQ</a>
        </div>
    </div>
</footer>

<script>
    let sharedTable;
    let documentId = null;
    let continueLink = null;

    function openImageModal() {
        const image = document.getElementById('docImage');
        const modal = document.getElementById('imageModal');
        const modalImage = document.getElementById('modalImage');
        modalImage.src = image.src;
        modal.classList.remove('hidden');
        modal.classList.add('flex');
        document.body.style.overflow = 'hidden';
    }

    function closeImageModal() {
        const modal = document.getElementById('imageModal');
        modal.classList.remove('flex');
        modal.classList.add('hidden');
        document.body.style.overflow = '';
    }

    function fetchProcessingStatus() {
        const formData = {
            document_id: documentId,
            user_id: $('#userId').val(),
            token: '<?php echo htmlspecialchars($_SESSION['token']); ?>'
        };

        $.ajax({
            url: 'https://python.tptimovyprojekt.software/documents/get_processing_result_status',
            type: 'POST',
            data: JSON.stringify(formData),
            contentType: 'application/json',
            dataType: 'json',
            success: function (res) {
                if (res.error) {
                    toastr.error(res.error || 'Failed to load processing status');
                } else {
                    if (res.need_continue) {
                        $('#continueProcessing').removeClass('hidden');
                        const itemId = $('#itemId').val();
                        switch (res.status) {
                            case 'UPLOADED':
                                continueLink = '../modules/segmentModule.php?document_id=' + documentId + '&item_id=' + itemId;
                                break;
                            case 'SEGMENTED':
                                continueLink =  '../modules/analyzeModule.php?document_id=' + documentId + '&item_id=' + itemId;
                                break;
                            case 'CLASSIFIED':
                                continueLink = '../modules/lettersModule.php?document_id=' + documentId + '&item_id=' + itemId;
                                break;
                            case 'PROCESSED':
                                continueLink =  '../modules/editJsonModule.php?document_id=' + documentId + '&item_id=' + itemId;
                                break;
                            default:
                                toastr.error('Unknown processing status');
                                break;
                        }
                    } else {
                        $('#continueProcessing').addClass('hidden');
                    }
                }
            },
            error: function () {
                toastr.error('Server error while fetching processing status');
            }
        });
    }

    function continueProcessing() {
        if (continueLink) {
            window.location.href = continueLink;
        } else {
            toastr.error('No processing link available');
        }
    }

    function fetchKeyJson() {
        const formData = {
            document_id: documentId,
            user_id: $('#userId').val(),
            token: '<?php echo $_SESSION['token']; ?>'
        };

        $.ajax({
            url: 'https://python.tptimovyprojekt.software/documents/get_json',
            type: 'POST',
            data: JSON.stringify(formData),
            contentType: 'application/json',
            dataType: 'json',
            success: function (res) {
                if (res.error) {
                    toastr.error(res.error || 'Failed to load key JSON');
                } else {
                    $('#jsonData').val(JSON.stringify(res, null, 2));
                }
            },
            error: function () {
                toastr.error('Server error while fetching key JSON');
            }
        });
    }

    function fetchSharedUsers() {
        sharedTable.clear();
        $.get(`../users/getSharedUsers.php?id=${documentId}`, function (data) {
            if (Array.isArray(data)) {
                data.forEach(username => {
                    sharedTable.row.add([
                        username,
                        `<button type="button" onclick="removeUser('${username}')" class="text-red-500">Remove</button>`
                    ]);
                });
                sharedTable.draw();
            } else {
                toastr.error('Failed to load shared users');
            }
        });
    }

    function changeName() {
        const newTitle = $('#name').val().trim();
        const documentId = $('#docId').val();
        const authorId = $('#userId').val();
        const callerUrl = "<?php echo $fullCallerUrl; ?>";

        if (!newTitle) {
            toastr.error('Please enter a valid document name');
            return;
        }

        const formData = {
            document_id: documentId,
            author_id: authorId,
            new_title: newTitle,
            token: '<?php echo $_SESSION['token']; ?>'
        };

        $.ajax({
            url: 'https://python.tptimovyprojekt.software/documents/update_document_title',
            type: 'POST',
            data: JSON.stringify(formData),
            contentType: 'application/json',
            dataType: 'json',
            headers: {
                'X-Caller-Url': callerUrl
            },
            success: function (res) {
                if (res.success) {
                    toastr.success('Document name updated');
                } else {
                    toastr.error(res.error || 'Failed to update name');
                }
            },
            error: function () {
                toastr.error('Server error while updating document name');
            }
        });
    }


    function addUser() {
        const username = $('#share').val().trim();
        if (!username) {
            toastr.error('Please enter a valid username');
            return;
        }

        const formData = {
            document_id: documentId,
            username: username,
            token: '<?php echo $_SESSION['token']; ?>'
        };

        $.ajax({
            url: 'https://python.tptimovyprojekt.software/documents/add_shared_user',
            type: 'POST',
            data: JSON.stringify(formData),
            contentType: 'application/json',
            dataType: 'json',
            success: function (res) {
                if (res.success) {
                    toastr.success('User added');
                    $('#share').val('');
                    fetchSharedUsers();
                } else {
                    toastr.error(res.error || 'Failed to add user');
                }
            },
            error: function (xhr) {
                toastr.error('Failed to add user');
            }
        });
    }


    function removeUser(username) {
        const formData = {
            document_id: documentId,
            username: username,
            token: '<?php echo $_SESSION['token']; ?>'
        };

        $.ajax({
            url: 'https://python.tptimovyprojekt.software/documents/remove_shared_user',
            type: 'POST',
            data: JSON.stringify(formData),
            contentType: 'application/json',
            dataType: 'json',
            success: function (res) {
                if (res.success) {
                    toastr.success('User removed');
                    fetchSharedUsers();
                } else {
                    toastr.error(res.error || 'Failed to remove user');
                }
            },
            error: function (xhr) {
                toastr.error('Failed to remove user');
            }
        });
    }

    function deleteDocument(documentId) {
        Swal.fire({
            icon: 'warning',
            title: 'Are you sure?',
            text: "You won't be able to revert this!",
            showCancelButton: true,
            confirmButtonColor: '#3085d6',
            cancelButtonColor: '#d33',
            confirmButtonText: 'Yes, delete it!'
        }).then((result) => {
            if (result.isConfirmed) {
                fetch('../documents/deleteDocument.php', {
                    method: 'DELETE',
                    headers: {'Content-Type': 'application/json'},
                    body: JSON.stringify({
                        doc_id: documentId,
                        id: <?php echo $userData['id']; ?>,
                        user_name: "<?php echo $userData['username']; ?>"
                    })
                }).then(response => response.json())
                    .then(data => {
                        if (data.error) {
                            toastr.error(data.error);
                        } else {
                            window.location.href = "../ownKeyDocuments.php";
                        }
                    }).catch(error => {
                    console.error('Error:', error);
                    toastr.error("An error occurred while deleting the document");
                });
            }
        });
    }

    function saveKeyJson() {
        let parsedJson;
        try {
            parsedJson = JSON.parse($('#jsonData').val());
        } catch (e) {
            toastr.error('Invalid JSON format');
            return;
        }

        const formData = {
            document_id: documentId,
            user_id: $('#userId').val(),
            token: '<?php echo $_SESSION['token']; ?>',
            json_data: parsedJson
        };

        $.ajax({
            url: 'https://python.tptimovyprojekt.software/save_key_json',
            type: 'POST',
            data: JSON.stringify(formData),
            contentType: 'application/json',
            dataType: 'json',
            success: function (res) {
                if (res.success) {
                    toastr.success('Key JSON saved successfully');
                } else {
                    toastr.error(res.error || 'Failed to save key JSON');
                }
            },
            error: function () {
                toastr.error('Server error while saving key JSON');
            }
        });
    }


    $(document).ready(function () {
        const urlParams = new URLSearchParams(window.location.search);
        const userId = urlParams.get('user');
        documentId = urlParams.get('id');

        sharedTable = $('#sharedUsersTable').DataTable({
            pagingType: "simple",
            lengthChange: false,
            pageLength: 4,
            searching: true,
            info: false,
            autoWidth: false,
            columnDefs: [{targets: [1], orderable: false}],
            dom: '<"top"f>rt<"bottom"p><"clear">',
            language: {
                search: "",
                searchPlaceholder: "Filter users..."
            }
        });

        $.get(`../documents/getDocument.php?user=${userId}&id=${documentId}`, function (data) {
            if (data.error) {
                toastr.error(data.error);
            } else {
                $('#docId').val(data.document.id);
                $('#itemId').val(data.itemId);
                $('#userId').val(userId);
                $('#name').val(data.document.title);
                $('#date').val(data.publishDate);
                $('#historical_author').val(data.document.historical_author);
                $('#historical_date').val(data.document.historical_date);
                $('#country').val(data.document.country);
                $('#language').val(data.document.language);
                $('#isPublic').prop('checked', data.document.is_public);
                $('#date').prop('disabled', true);
                $('#historical_author').prop('disabled', true);
                $('#historical_date').prop('disabled', true);
                $('#country').prop('disabled', true);
                $('#language').prop('disabled', true);

                if (data.imagePaths && data.imagePaths.length > 0) {
                    $('#docImage').attr('src', '../../' + data.imagePaths[0]);
                } else {
                    $('#docImage').attr('alt', 'No image available');
                }

                fetchSharedUsers();
                fetchKeyJson();
                fetchProcessingStatus();
            }
        });

        $("#share").autocomplete({
            source: function (request, response) {
                $.get("../users/fetchUsernames.php", {
                    query: request.term,
                    picture_id: documentId
                }, function (data) {
                    if (Array.isArray(data)) {
                        response(data.map(user => user.username));
                    } else if (data.error) {
                        toastr.error(data.error);
                        response([]);
                    } else {
                        response([]);
                    }
                });
            },
            minLength: 1
        });
        $('.ui-helper-hidden-accessible').remove();

        $('#isPublic').on('change', function () {
            const isPublic = $(this).is(':checked');

            const formData = {
                document_id: documentId,
                author_id: $('#userId').val(),
                is_public: isPublic,
                token: '<?php echo $_SESSION['token']; ?>'
            };

            $.ajax({
                url: 'https://python.tptimovyprojekt.software/documents/update_doc_public',
                type: 'POST',
                data: JSON.stringify(formData),
                contentType: 'application/json',
                dataType: 'json',
                success: function (res) {
                    if (res.success) {
                        toastr.success(`Document is now ${isPublic ? 'Public' : 'Private'}`);
                    } else {
                        toastr.error(res.error || 'Update failed');
                    }
                },
                error: function () {
                    toastr.error('Server error while updating visibility');
                }
            });
        });


    });
</script>

</body>
</html>