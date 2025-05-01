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
    exit;
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>HandScript - Documents</title>

    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://unpkg.com/aos@2.3.1/dist/aos.css" rel="stylesheet">
    <script src="https://unpkg.com/aos@2.3.1/dist/aos.js"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.css">
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.6.0/jquery.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.js"></script>
    <link rel="stylesheet" href="https://cdn.datatables.net/1.13.6/css/jquery.dataTables.min.css">
    <script src="https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/paginationjs/2.1.5/pagination.css" />
    <script src="https://cdnjs.cloudflare.com/ajax/libs/paginationjs/2.1.5/pagination.min.js"></script>
    <link rel="stylesheet" href="../css/documents.css?v=<?php echo time(); ?>">

    <style>
        .card-pic {
            @apply bg-white rounded-xl shadow-md p-4 transition-transform transform hover:scale-105;
        }
    </style>

</head>

<body class="min-h-screen select-none flex flex-col">

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
        <section class="container mx-auto px-6 py-10">
            <h2 class="text-4xl font-bold text-center text-papyrus mb-8">🔑 My Key Documents</h2>
            <div class="flex flex-col md:flex-row items-center justify-center gap-6 mb-8">
                <div class="w-full max-w-md">
                    <label for="search-input" class="block mb-2 text-lg font-medium text-[#3b2f1d]">🔎 Search
                        documents</label>
                    <input type="text" id="search-input" placeholder="Type to search..."
                        class="w-full p-3 rounded-md border border-[#3b2f1d] bg-[#ede1c3] text-[#3b2f1d] placeholder-[#6b5b3e] focus:ring-2 focus:ring-[#cdbf9b] focus:outline-none transition duration-300">
                </div>
                <div class="w-full max-w-md">
                    <label for="filter-select" class="block mb-2 text-lg font-medium text-[#3b2f1d]">📂 Filter by
                        type</label>
                    <select id="filter-select"
                        class="w-full p-3 rounded-md border border-[#3b2f1d] bg-[#ede1c3] text-[#3b2f1d] focus:ring-2 focus:ring-[#cdbf9b] focus:outline-none transition duration-300">
                        <option value="OWN">Own Documents</option>
                        <option value="SHARED">Shared Documents</option>
                        <option value="GLOBAL">Global Documents</option>
                    </select>
                </div>
            </div>

            <div id="my-documents-grid" class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 gap-6"
                style="display: flex; justify-content: center; align-items: flex-start; flex-wrap: wrap;">
            </div>
            <div id="pagination" class="flex justify-center mt-8"></div>
        </section>
    </main>

    <footer class="bg-[#d7c7a5] text-center text-papyrus py-4 mt-10 border-t border-yellow-300">
        &copy; 2025 HandScript - <a href="https://tptimovyprojekt.ddns.net/" class="underline">Visit Project Page</a>
    </footer>
    <script src="https://unpkg.com/flowbite@1.6.5/dist/flowbite.min.js"></script>
    <script>
        let documentsData = [], imagesData = {};

        function renderDocuments(documents, images) {
            $('#pagination').pagination({
                dataSource: documents,
                pageSize: 3,
                showPrevious: true,
                showNext: true,
                callback: function (data, pagination) {
                    const grid = document.getElementById('my-documents-grid');
                    grid.innerHTML = '';

                    if (data.length === 0) {
                        const noDataMessage = document.createElement('div');
                        noDataMessage.className = 'text-center text-papyrus text-lg';
                        noDataMessage.innerHTML = '<p>No documents found.</p>';
                        grid.appendChild(noDataMessage);
                        return;
                    }

                    data.forEach(doc => {
                        const imgPath = images[doc.id] ? '..' + images[doc.id] : '../img/default.png';
                        const card = document.createElement('div');
                        card.className = 'card-pic';
                        card.innerHTML = `
                    <img src="${imgPath}" class="card-img" alt="..." loading="lazy">
                    <div class="card-body">
                        <h5 class="card-title">${doc.title}</h5>
                        <div class="card-buttons">
                            <a href="editDocument.php?id=${doc.id}&user=<?php echo $userData['id']; ?>" class="btn btn-primary">Edit</a>
                            <button onclick="deleteDocument(${doc.id})" class="btn btn-danger">Delete</button>
                        </div>
                    </div>`;
                        grid.appendChild(card);
                    });
                }
            });
        }

        function fetchSharedDocumentsAndImages() {
            Promise.all([
                fetch('fetchSharedDocuments.php?key=CIPHER').then(res => res.json()),
                fetch('fetchSharedImages.php?key=CIPHER').then(res => res.json())
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

                    renderDocuments(documentsData, imagesData);
                })
                .catch(error => {
                    toastr.error('Failed to load documents.');
                    console.error(error);
                });
        }

        function fetchPublicDocumentsAndImages() {
            Promise.all([
                fetch('fetchDocuments.php?key=CIPHER&public=true').then(res => res.json()),
                fetch('fetchImages.php?key=CIPHER&public=true').then(res => res.json())
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

                    renderDocuments(documentsData, imagesData);
                })
                .catch(error => {
                    toastr.error('Failed to load documents.');
                    console.error(error);
                });
        }

        function fetchDocumentsAndImages() {
            Promise.all([
                fetch('fetchDocuments.php?key=CIPHER&public=false').then(res => res.json()),
                fetch('fetchImages.php?key=CIPHER&public=false').then(res => res.json())
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

                    renderDocuments(documentsData, imagesData);
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
                        method: 'POST',
                        headers: { 'Content-Type': 'application/json' },
                        body: JSON.stringify({ doc_id: documentId, id: <?php echo $userData['id']; ?>, user_name: "<?php echo $userData['username']; ?>" })
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

        function checkToasts() {
            let toast = <?php echo json_encode($_SESSION['toast'] ?? null); ?>;
            if (toast) {
                toastr[toast.type](toast.message);
                <?php unset($_SESSION['toast']); ?>
            }
        }

        $(document).ready(function () {
            fetchDocumentsAndImages();
            checkToasts();
        });

        $('#search-input').on('input', function () {
            const searchTerm = $(this).val().toLowerCase();
            const filteredDocs = documentsData.filter(doc => doc.title.toLowerCase().includes(searchTerm));
            renderDocuments(filteredDocs, imagesData);
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


    </script>

</body>

</html>