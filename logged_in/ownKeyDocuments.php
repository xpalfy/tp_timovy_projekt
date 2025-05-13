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
    exit();
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>HandScript - Documents</title>

    <link rel="stylesheet" href="../css/documents.css?v=<?php echo time(); ?>">
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://unpkg.com/aos@2.3.1/dist/aos.css" rel="stylesheet">
    <script src="https://unpkg.com/aos@2.3.1/dist/aos.js"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.css">
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.6.0/jquery.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.js"></script>
    <link rel="stylesheet" href="https://cdn.datatables.net/1.13.6/css/jquery.dataTables.min.css">
    <script src="https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    <style>
        .card-pic {
            @apply bg-white rounded-xl shadow-md p-4 transition-transform transform hover:scale-105;
            width: 300px;
        }
    </style>

</head>

<body class="min-h-screen select-none flex flex-col">
<script>
    function checkToasts() {
        let toast = <?php echo json_encode($_SESSION['toast'] ?? null); ?>;
        if (toast) {
            toastr[toast.type](toast.message);
            <?php unset($_SESSION['toast']); ?>
        }
    }

    checkToasts();
</script>

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
                <path d="M4 6h16M4 12h16M4 18h16"/>
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
                                      stroke-width="2" d="M1 1l4 4 4-4"/>
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
                                      stroke-width="2" d="M1 1l4 4 4-4"/>
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
                                <li>
                                    <a href="./modules/decipherModule.php"
                                       class="block px-4 py-2 hover:bg-[#cbbd99]">Decipher</a>
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

<main class="flex-grow">
    <section class="container mx-auto px-6 py-10 min-h-[100vh]">
        <div class="flex flex-col md:flex-row gap-10 mt-4">
            <!-- Sidebar Filters -->
            <aside class="w-full md:w-1/4 h-screen bg-[#f7f1dd] p-6 rounded-xl shadow-md overflow-y-auto md:-ml-10 md:mr-10">
                <h3 class="text-2xl font-semibold text-[#3b2f1d] mb-8">🧰 Filters</h3>
                <div class="space-y-6">
                    <!-- Search -->
                    <div>
                        <label for="search-input" class="block mb-2 text-lg font-medium text-[#3b2f1d]">🔎 Search</label>
                        <input type="text" id="search-input" placeholder="Type to search..."
                               class="w-full p-3 rounded-md border border-[#3b2f1d] bg-[#ede1c3] text-[#3b2f1d] placeholder-[#6b5b3e] focus:ring-2 focus:ring-[#cdbf9b] focus:outline-none transition duration-300">
                    </div>

                    <!-- Document Type Filter -->
                    <div>
                        <label for="filter-select" class="block mb-2 mt-8 text-lg font-medium text-[#3b2f1d]">🗂️ Document
                            Access Type</label>
                        <select id="filter-select"
                                class="w-full px-4 py-3 pr-10 rounded-md border border-[#3b2f1d] bg-[#ede1c3] text-[#3b2f1d] cursor-pointer appearance-none bg-[url('data:image/svg+xml;utf8,<svg fill=\'%233b2f1d\' height=\'20\' viewBox=\'0 0 24 24\' width=\'20\' xmlns=\'http://www.w3.org/2000/svg\'><path d=\'M7 10l5 5 5-5z\'/></svg>')] bg-no-repeat bg-[right_0.75rem_center] focus:ring-2 focus:ring-[#cdbf9b] focus:outline-none transition duration-300">
                            <option value="OWN" selected>Own Documents</option>
                            <option value="SHARED">Shared Documents</option>
                            <option value="PUBLIC">Public Documents</option>
                        </select>
                    </div>
                </div>
            </aside>

            <!-- Documents Grid + Pagination -->
            <div class="flex-1 flex flex-col justify-between mt-4">
                <div>
                    <h2 class="text-4xl font-bold text-center text-papyrus mb-10">🔑 My Key Documents</h2>
                    <div id="my-documents-grid"
                         class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6">
                        <!-- Document cards will be inserted here -->
                    </div>
                </div>

                <div class="flex flex-col md:flex-row items-center justify-between mt-20 mb-4 gap-4">
                    <!-- Pagination Buttons Centered Under Grid -->
                    <div id="pagination" class="flex flex-wrap justify-center w-full md:justify-center gap-2">
                        <!-- Pagination buttons will be injected here -->
                    </div>

                    <!-- Per Page Selector Aligned Right -->
                    <div class="flex items-center space-x-2 md:justify-end w-full md:w-auto">
                        <label for="page-size-select" class="text-sm font-medium text-[#3b2f1d]">Results per
                            page:</label>
                        <select id="page-size-select"
                                class="px-3 py-2 rounded-md border border-[#3b2f1d] bg-[#ede1c3] text-[#3b2f1d] focus:ring-2 focus:ring-[#cdbf9b] focus:outline-none transition duration-300">
                            <option value="6" selected>6</option>
                            <option value="12">12</option>
                            <option value="18">18</option>
                        </select>
                    </div>
                </div>
            </div>
        </div>
    </section>
</main>


<!-- Footer -->
<footer class="bg-[#d7c7a5] border-t border-yellow-300 text-[#3b2f1d] py-6">
    <div class="max-w-7xl mx-auto px-4 flex flex-col md:flex-row justify-between items-center space-y-4 md:space-y-0">
        <p class="text-center md:text-left">&copy; 2025 HandScript</p>
        <div class="flex space-x-4 text-sm">
            <a href="https://tptimovyprojekt.ddns.net/" class="underline hover:text-[#5a452e] transition">Visit Project
                Page</a>
            <a href="../faq.html" target="_blank" rel="noopener noreferrer"
               class="underline hover:text-[#5a452e] transition">FAQ</a>
        </div>
    </div>
</footer>
<script src="https://unpkg.com/flowbite@1.6.5/dist/flowbite.min.js"></script>
<script>
    let documentsData = [], imagesData = {};
    let currentPageSize = 6;

    function renderDocuments(documents, images, pageSize = 6, type = 'OWN', currentPage = 1) {
        const totalPages = Math.max(1, Math.ceil(documents.length / pageSize));
        const start = (currentPage - 1) * pageSize;
        const end = start + pageSize;
        const paginatedDocs = documents.slice(start, end);

        const grid = document.getElementById('my-documents-grid');
        const pagination = document.getElementById('pagination');
        grid.innerHTML = '';
        pagination.innerHTML = '';

        if (paginatedDocs.length === 0) {
            grid.innerHTML = `<div class="text-center text-papyrus text-lg mt-10">No documents found.</div>`;
        } else {
            paginatedDocs.forEach(doc => {
                const imgPath = images[doc.id] ? '..' + images[doc.id] : '../img/default.png';

                let cardButtons = '';
                let editPage = '';

                if (type === 'OWN') {
                    editPage = 'edit_key/editOwnKeyDocument.php';
                    cardButtons = `
                    <a href="${editPage}?id=${doc.id}&user=<?php echo $userData['id']; ?>" class="btn btn-primary">Edit</a>
                    <button onclick="deleteDocument(${doc.id})" class="btn btn-danger">Delete</button>`;
                } else if (type === 'SHARED') {
                    editPage = 'edit_key/editSharedKeyDocument.php';
                    cardButtons = `
                    <a href="${editPage}?id=${doc.id}&user=<?php echo $userData['id']; ?>" class="btn btn-primary">Edit</a>
                    <button onclick="unshareWithMe('<?php echo $userData['username']; ?>', ${doc.id})" class="btn btn-danger">Unshare</button>`;
                } else if (type === 'PUBLIC') {
                    editPage = 'edit_key/viewPublicKeyDocument.php';
                    cardButtons = `<a href="${editPage}?id=${doc.id}&user=<?php echo $userData['id']; ?>" class="btn btn-primary">Show</a>`;
                }

                const card = document.createElement('div');
                card.className = 'card-pic';
                card.innerHTML = `
                <img src="${imgPath}" class="card-img" alt="..." loading="lazy">
                <div class="card-body">
                    <h5 class="card-title">${doc.title}</h5>
                    <div class="card-buttons">${cardButtons}</div>
                </div>`;
                grid.appendChild(card);
            });
        }

        // Always render pagination (even if there's only 1 page or no docs)
        // Previous Button
        const prev = document.createElement('button');
        prev.textContent = '«';
        prev.disabled = currentPage === 1;
        prev.className = `px-3 py-1 rounded ${currentPage === 1 ? 'bg-gray-100 text-gray-400 cursor-not-allowed' : 'bg-gray-200 text-black hover:bg-[#5a452e]'} transition`;
        prev.onclick = () => renderDocuments(documents, images, pageSize, type, currentPage - 1);
        pagination.appendChild(prev);

        // Page Numbers
        for (let i = 1; i <= totalPages; i++) {
            const btn = document.createElement('button');
            btn.textContent = i;
            btn.className = `px-3 py-1 rounded ${i === currentPage ? 'bg-[#3b2f1d] text-white' : 'bg-gray-200 text-black'} hover:bg-[#5a452e] transition`;
            btn.onclick = () => renderDocuments(documents, images, pageSize, type, i);
            pagination.appendChild(btn);
        }

        // Next Button
        const next = document.createElement('button');
        next.textContent = '»';
        next.disabled = currentPage === totalPages;
        next.className = `px-3 py-1 rounded ${currentPage === totalPages ? 'bg-gray-100 text-gray-400 cursor-not-allowed' : 'bg-gray-200 text-black hover:bg-[#5a452e]'} transition`;
        next.onclick = () => renderDocuments(documents, images, pageSize, type, currentPage + 1);
        pagination.appendChild(next);
    }


    function fetchSharedDocumentsAndImages() {
        Promise.all([
            fetch('documents/fetchSharedDocuments.php?key=KEY').then(res => res.json()),
            fetch('items/fetchSharedItems.php?key=KEY').then(res => res.json())
        ])
            .then(([docs, imgs]) => {
                documentsData = docs;
                imagesData = imgs;

                Object.values(imagesData).forEach(imgPath => {
                    if (imgPath) {
                        const fullPath = '..' + imgPath;
                        const preloadImg = new Image();
                        preloadImg.src = fullPath;
                    }
                });

                renderDocuments(documentsData, imagesData, currentPageSize, 'SHARED', 1);
            })
            .catch(error => {
                toastr.error('Failed to load documents.');
                console.error(error);
            });
    }

    function fetchPublicDocumentsAndImages() {
        Promise.all([
            fetch('documents/fetchDocuments.php?key=KEY&public=true').then(res => res.json()),
            fetch('items/fetchItems.php?key=KEY&public=true').then(res => res.json())
        ])
            .then(([docs, imgs]) => {
                documentsData = docs;
                imagesData = imgs;

                Object.values(imagesData).forEach(imgPath => {
                    if (imgPath) {
                        const fullPath = '..' + imgPath;
                        const preloadImg = new Image();
                        preloadImg.src = fullPath;
                    }
                });

                renderDocuments(documentsData, imagesData, currentPageSize, 'PUBLIC', 1);
            })
            .catch(error => {
                toastr.error('Failed to load documents.');
                console.error(error);
            });
    }

    function fetchDocumentsAndImages() {
        Promise.all([
            fetch('documents/fetchDocuments.php?key=KEY&public=false').then(res => res.json()),
            fetch('items/fetchItems.php?key=KEY&public=false').then(res => res.json())
        ])
            .then(([docs, imgs]) => {
                documentsData = docs;
                imagesData = imgs;

                Object.values(imagesData).forEach(imgPath => {
                    if (imgPath) {
                        const fullPath = '..' + imgPath;
                        const preloadImg = new Image();
                        preloadImg.src = fullPath;
                    }
                });

                renderDocuments(documentsData, imagesData, currentPageSize, 'OWN', 1);
            })
            .catch(error => {
                toastr.error('Failed to load documents.');
                console.error(error);
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
                fetch('documents/deleteDocument.php', {
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
                            toastr.success("Document deleted successfully");
                            setTimeout(() => location.reload(), 1000);
                        }
                    }).catch(error => {
                    console.error('Error:', error);
                    toastr.error("An error occurred while deleting the document");
                });
            }
        });
    }

    function unshareWithMe(username, documentId) {
        Swal.fire({
            icon: 'warning',
            title: 'Are you sure?',
            text: `You will no longer have access to this document.`,
            showCancelButton: true,
            confirmButtonColor: '#3085d6',
            cancelButtonColor: '#d33',
            confirmButtonText: 'Yes, unshare it!'
        }).then((result) => {
            if (result.isConfirmed) {
                const formData = {
                    document_id: documentId,
                    username: username,
                    token: '<?php echo $_SESSION['token']; ?>'
                };

                $.ajax({
                    url: 'https://python.tptimovyprojekt.software/remove_shared_user',
                    type: 'POST',
                    data: JSON.stringify(formData),
                    contentType: 'application/json',
                    dataType: 'json',
                    success: function (res) {
                        if (res.success) {
                            toastr.success('Access removed successfully');
                            fetchSharedDocumentsAndImages();
                        } else {
                            toastr.error(res.error || 'Failed to remove access');
                        }
                    },
                    error: function () {
                        toastr.error('Server error while unsharing');
                    }
                });
            }
        });
    }


    $(document).ready(function () {
        fetchDocumentsAndImages();
    });

    $('#search-input').on('input', function () {
        const searchTerm = $(this).val().toLowerCase();
        const filteredDocs = documentsData.filter(doc => doc.title.toLowerCase().includes(searchTerm));
        const filterValue = $('#filter-select').val();
        renderDocuments(filteredDocs, imagesData, currentPageSize, filterValue, 1);
    });

    $('#filter-select').on('change', function () {
        const selected = $(this).val();
        if (selected === 'OWN') {
            fetchDocumentsAndImages();
        } else if (selected === 'SHARED') {
            fetchSharedDocumentsAndImages();
        } else {
            fetchPublicDocumentsAndImages();
        }
    });

    $('#page-size-select').on('change', function () {
        currentPageSize = parseInt($(this).val());
        const filterValue = $('#filter-select').val();
        renderDocuments(documentsData, imagesData, currentPageSize, filterValue, 1);
    });


</script>

</body>

</html>