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

$fullCallerUrl = (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? 'https' : 'http') .
    '://' . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI'];

?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
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
    <link rel="stylesheet" href="../css/editDocument.css?v=<?php echo time(); ?>" />
</head>

<body class="bg-gradient-to-br from-[#ede1c3] to-[#cdbf9b] text-papyrus min-h-screen flex flex-col select-none">
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
                                    <li>
                                        <a href="./modules/editJsonModule.php"
                                            class="block px-4 py-2 hover:bg-[#cbbd99]">Edit Json</a>
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
    <main class="flex-grow container mx-auto px-4 py-10">
        <h1 id="docTitle" class="text-4xl font-bold text-center mb-10">Edit Your Document Here</h1>

        <div class="flex flex-col lg:flex-row gap-10 items-stretch">

            <!-- Left side: Image container -->
            <div class="w-full lg:w-1/2 flex">
                <div class="w-full">
                    <img id="docImage" src="" alt="Document"
                        class="w-full h-full object-cover rounded-lg border shadow-lg" />
                </div>
            </div>

            <!-- Right side: Form container -->
            <div class="w-full lg:w-1/2 flex">
                <div class="w-full bg-white bg-opacity-50 rounded-xl p-6 shadow-lg mt-4 lg:mt-0">
                    <input type="hidden" name="id" id="docId">
                    <input type="hidden" name="user" id="userId">

                    <!-- Document Name -->
                    <div>
                        <label for="name" class="block font-semibold mb-1">Document Name</label>
                        <div class="flex items-center gap-2 mb-2">
                            <input type="text" name="name" id="name"
                                class="flex-grow border border-yellow-400 rounded px-4 py-2" />
                        </div>
                    </div>

                    <div>
                        <label for="owner" class="block font-semibold mb-1 mt-6">Owner</label>
                        <div class="flex items-center gap-2 mb-2">
                            <input type="text" name="owner" id="owner"
                                class="flex-grow border border-yellow-400 rounded px-4 py-2" />
                        </div>
                    </div>

                    <div>
                        <label for="date" class="block font-semibold mb-1 mt-6">Publish Date</label>
                        <div class="flex items-center gap-2 mb-2">
                            <input type="text" name="date" id="date"
                                class="flex-grow border border-yellow-400 rounded px-4 py-2" />
                        </div>
                    </div>
                </div>
            </div>

        </div>
        <div class="mt-10 w-full">
            <div class="bg-white bg-opacity-50 rounded-xl p-6 shadow-lg">
                <label for="jsonData" class="block font-semibold mb-2 text-[#3b2f1d]">Key JSON</label>
                <textarea id="jsonData" rows="12"
                    class="w-full border border-yellow-400 rounded px-4 py-2 text-sm font-mono bg-white bg-opacity-70 resize-y mb-4"
                    placeholder="{ }"></textarea>
            </div>
        </div>

    </main>
    <!-- Footer -->
    <footer class="bg-[#d7c7a5] text-center text-papyrus py-4 mt-10 border-t border-yellow-300">
        &copy; 2025 HandScript – <a href="https://tptimovyprojekt.ddns.net/" class="underline">Visit Project Page</a>
    </footer>

    <script>
        let sharedTable;
        let documentId = null;

        function fetchKeyJson() {
            const formData = {
                document_id: documentId,
                user_id: $('#userId').val(),
                token: '<?php echo $_SESSION['token']; ?>'
            };

            $.ajax({
                url: 'https://python.tptimovyprojekt.software/get_key_json',
                type: 'POST',
                data: JSON.stringify(formData),
                contentType: 'application/json',
                dataType: 'json',
                success: function (res) {
                    if (res.error) {
                        toastr.error(res.error || 'Failed to load key JSON');
                    } else {
                        $('#jsonData').val(JSON.stringify(res, null, 2));
                        $('#jsonData').prop('disabled', true);
                    }
                },
                error: function () {
                    toastr.error('Server error while fetching key JSON');
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
                pageLength: 5,
                searching: true,
                info: false,
                autoWidth: false,
                columnDefs: [{ targets: [1], orderable: false }],
                dom: '<"top"f>rt<"bottom"p><"clear">',
                language: {
                    search: "",
                    searchPlaceholder: "Filter users..."
                }
            });

            $.get(`documents/getDocument.php?user=${userId}&id=${documentId}`, function (data) {
                if (data.error) {
                    toastr.error(data.error);
                } else {
                    $('#docId').val(data.document.id);
                    $('#userId').val(data.document.author_id);
                    $('#name').val(data.document.title);
                    $('#date').val(data.publishDate);
                    $('#owner').val(data.document.author_name);
                    $('#docTitle').text(data.document.name);
                    $('#isPublic').prop('checked', data.document.is_public);
                    $('#name').prop('disabled', true);
                    $('#owner').prop('disabled', true);
                    $('#date').prop('disabled', true);

                    if (data.imagePaths && data.imagePaths.length > 0) {
                        $('#docImage').attr('src', '../' + data.imagePaths[0]);
                    } else {
                        $('#docImage').attr('alt', 'No image available');
                    }

                    fetchKeyJson();
                }
            });

        });
    </script>

</body>

</html>