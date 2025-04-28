<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

require_once '../checkType.php';
require_once '../config.php';

try {
    $userData = validateToken();
} catch (Exception $e) {
    http_response_code(500);
    $_SESSION['toast'] = ['type' => 'error', 'message' => 'Token validation failed'];
    header('Location: ../login.php');
}

$userId = $_GET['user'];
$documentId = $_GET['id'];

if ($userId == null || $documentId == null) {
    $_SESSION['toast'] = [
        'message' => 'Invalid URL',
        'type' => 'error'
    ];
    header('Location: documents.php');
    exit();
}

if ($userId != $userData['id']) {
    $_SESSION['toast'] = [
        'message' => 'You can only edit your own documents',
        'type' => 'error'
    ];
    header('Location: documents.php');
    exit();
}

$conn = getDatabaseConnection();

$sql = "SELECT * FROM documents WHERE ID = $documentId AND author_id = $userId";

$stmt = $conn->prepare($sql);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows == 0) {
    // check if document exists in shared documents
    $sql = "SELECT * FROM users_pictures WHERE picture_id = $documentId AND user_id = $userId";
    $stmt = $conn->prepare($sql);
    $stmt->execute();
    $result = $stmt->get_result();
    if ($result->num_rows == 0) {
        $_SESSION['toast'] = [
            'message' => 'Document not found',
            'type' => 'error'
        ];
        header('Location: documents.php');
        exit();
    }

    $sql = "SELECT * FROM pictures WHERE ID = $documentId";
    $stmt = $conn->prepare($sql);
    $stmt->execute();
    $result = $stmt->get_result();
    if ($result->num_rows == 0) {
        $_SESSION['toast'] = [
            'message' => 'Document not found',
            'type' => 'error'
        ];
        header('Location: documents.php');
        exit();
    }
}

$picture = $result->fetch_assoc();

$stmt->close();
$conn->close();
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

    <!-- DataTables -->
    <link rel="stylesheet" href="https://cdn.datatables.net/1.13.6/css/jquery.dataTables.min.css">
    <script src="https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script>
</head>

<style>
    body {
        font-family: 'Inter', sans-serif;
        background: linear-gradient(to bottom right, #ede1c3, #cdbf9b);
    }

    body {
        height: 100%;
    }

    .text-papyrus {
        color: #3b2f1d;
    }

    /* Reduce spacing and button size */
    .dataTables_filter input {
        padding: 4px 6px;
        font-size: 0.875rem;
        border: 1px solid #ccc;
        border-radius: 4px;
    }

    .dataTables_wrapper .dataTables_paginate .paginate_button {
        padding: 2px 8px;
        margin: 0 2px;
        font-size: 0.8rem;
        border-radius: 4px;
    }

    .dataTables_wrapper .dataTables_paginate {
        margin-top: 0.5rem;
    }

    table.dataTable td {
        padding: 6px 8px;
    }

    table.dataTable thead th {
        padding: 6px 8px;
    }

    /* Style the autocomplete dropdown to match Tailwind aesthetics */
    /* Dropdown container */
    .ui-autocomplete {
        z-index: 9999;
        max-height: 12rem;
        max-width: 10rem;
        overflow-y: auto;
        overflow-x: hidden;
        background-color: white;
        border: 2px solid #facc15;
        /* Tailwind yellow-400 */
        border-radius: 0.5rem;
        box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
        font-size: 0.875rem;
        padding: 0.5rem 0;
        /* Inner padding to avoid touching the border */
    }

    /* Dropdown item */
    .ui-menu-item {
        padding: 5px 5px;
        /* space between items */
        cursor: pointer;
        /* rounded-md */
    }

    .ui-menu-item-wrapper {
        padding: 5px 20px;
        /* space between items */
        cursor: pointer;
        border-radius: 0.375rem;
        /* rounded-md */
    }

    /* Active (hovered or selected) item */
    .ui-menu-item-wrapper.ui-state-active {
        background-color: #fef08a;
        /* Tailwind yellow-200 */
        color: #3b2f1d;
    }
</style>




<body class="bg-gradient-to-br from-[#ede1c3] to-[#cdbf9b] text-papyrus min-h-screen flex flex-col select-none">
    <!-- Navbar -->
    <nav class="sticky top-0 z-50 w-full transition-all duration-300 bg-[#d7c7a5] border-b border-yellow-300 shadow-md not-copyable not-draggable"
        id="navbar">
        <div class="container mx-auto flex flex-wrap items-center justify-between py-3 px-4">
            <!-- Logo and brand -->
            <a href="main.php"
                class="flex items-center text-papyrus text-2xl font-bold hover:underline animate-slide-left"
                data-aos="fade-right" data-aos-delay="150">
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
            <div class="w-full lg:flex lg:items-center lg:w-auto hidden mt-4 lg:mt-0" id="navbarNav"
                data-aos="fade-left" data-aos-delay="150">
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

    <main class="flex-grow container mx-auto px-4 py-10">
        <h1 class="text-4xl font-bold text-center mb-2"><?php echo $picture['name'] ?></h1>
        <p class="text-center text-lg mb-8">Edit your document here</p>
        <div class="flex flex-col lg:flex-row gap-10">
            <div class="w-full lg:w-1/2">
                <img src="<?php echo '/' . explode('/', explode('htdocs/', __DIR__)[1])[0] . $picture['path']; ?>"
                    alt="Document" class="w-full rounded-lg border shadow-lg" />
            </div>
            <div class="w-full lg:w-1/2 bg-white bg-opacity-50 rounded-xl p-6 shadow-lg">
                <form action="editDocumentSave.php" method="post" enctype="multipart/form-data"
                    class="space-y-6 relative min-h-[500px]">
                    <input type="hidden" name="id" value="<?php echo $picture['id'] ?>">
                    <input type="hidden" name="user" value="<?php echo $picture['author_id'] ?>">

                    <!-- Name Input -->
                    <div>
                        <label for="name" class="block font-semibold mb-1">Document Name</label>
                        <input type="text" name="name" id="name" value="<?php echo $picture['title'] ?>"
                            class="w-full border border-yellow-400 rounded px-4 py-2" />
                    </div>

                    <!-- Share With Input -->
                    <div>
                        <label for="share" class="block font-semibold mb-1">Share with</label>
                        <div class="flex items-center gap-2 mb-2">
                            <input type="text" id="share" placeholder="Enter username"
                                class="flex-grow border border-yellow-400 rounded px-4 py-2" />
                            <input type="hidden" name="sharedUsers" id="sharedUsers">
                            <button type="button" onclick="addUser()"
                                class="px-4 py-2 bg-yellow-300 text-[#3b2f1d] rounded shadow hover:bg-yellow-400 transition">
                                Add
                            </button>
                        </div>
                    </div>

                    <!-- Shared Users Table -->
                    <div>
                        <table id="sharedUsersTable" class="display w-full text-sm compact">
                            <thead>
                                <tr>
                                    <th>Username</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody id="sharedUsersTableBody">
                                <?php
                                $conn = getDatabaseConnection();
                                $sql = "SELECT u.username FROM users u
                                        JOIN document_user_association d_u_a ON u.id = d_u_a.user_id
                                        WHERE d_u_a.document_id = $documentId";
                                $stmt = $conn->prepare($sql);
                                $stmt->execute();
                                $result = $stmt->get_result();
                                while ($row = $result->fetch_assoc()) {
                                    echo '<tr>';
                                    echo '<td>' . htmlspecialchars($row['username']) . '</td>';
                                    echo '<td><button type="button" onclick="removeUser(\'' . htmlspecialchars($row['username']) . '\')" class="text-red-500">Remove</button></td>';
                                    echo '</tr>';
                                }
                                $stmt->close();
                                $conn->close();

                                ?>
                            </tbody>
                        </table>
                    </div>

                    <!-- Save Button -->
                    <!-- Save Button -->
                    <div class="absolute bottom-6 right-6">
                        <button type="submit"
                            class="bg-green-500 text-white px-6 py-2 rounded hover:bg-green-600 transition">
                            Save
                        </button>
                    </div>

                </form>
            </div>

        </div>
    </main>

    <footer class="bg-[#d7c7a5] text-center text-papyrus py-4 mt-10 border-t border-yellow-300">
        &copy; 2025 HandScript – <a href="https://tptimovyprojekt.ddns.net/" class="underline">Visit Project Page</a>
    </footer>

    <script>
        AOS.init(
            {
                duration: 800,
                once: true
            }
        );
        let sharedUsers = [];

        function addUser() {
            const username = document.getElementById('share').value.trim();
            if (username && !sharedUsers.includes(username)) {
                // add user to the list and table
                sharedUsers.push(username);
                document.getElementById('share').value = '';
                sharedTable.row.add([username, '<button type="button" onclick="removeUser(\'' + username + '\')" class="text-red-500">Remove</button>']).draw();
                document.getElementById('sharedUsers').value = sharedUsers.join(',');


            }
            else if (sharedUsers.includes(username)) {
                toastr.warning('User already added');
            } else {
                toastr.error('Please enter a valid username');
            }
        }

        function removeUser(username) {
            // remove user from the list and table
            sharedUsers = sharedUsers.filter(user => user !== username);
            sharedTable.rows().every(function (rowIdx, tableLoop, rowLoop) {
                const data = this.data();
                if (data[0] === username) {
                    this.remove();
                    return false; // break the loop
                }
            });
            sharedTable.draw();
            document.getElementById('sharedUsers').value = sharedUsers.join(',');
            toastr.success('User removed');
        }

        $(function () {
            $("#share").autocomplete({
                source: function (request, response) {
                    $.ajax({
                        url: "fetchUsernames.php",
                        type: "GET",
                        data: { query: request.term, picture_id: <?php echo $documentId; ?> },
                        dataType: "json",
                        success: function (data) {
                            response(data.map(user => user.username));
                        }
                    });
                },
                minLength: 1
            });
        });

        toastr.options = { positionClass: 'toast-top-right', timeOut: 3000 };
        let toast = <?php echo json_encode($_SESSION['toast'] ?? null); ?>;
        if (toast) {
            toastr[toast.type](toast.message);
            <?php unset($_SESSION['toast']); ?>
        }
        // Initialize DataTable
        let sharedTable;

        $(document).ready(function () {
            sharedTable = $('#sharedUsersTable').DataTable({
                pagingType: "simple", // smaller pagination controls (Previous / Next only)
                lengthChange: false,  // hides "Show X entries"
                pageLength: 5,        // default number of rows per page
                searching: true,      // enables search box
                info: false,          // hides "Showing X to Y of Z entries"
                autoWidth: false,
                language: {
                    search: "", // clears the default "Search:"
                    searchPlaceholder: "Filter users..."
                },
                columnDefs: [
                    { targets: [1], orderable: false } // disable ordering on Actions column
                ],
                dom: '<"top"f>rt<"bottom"p><"clear">', // filters (f), table (t), pagination (p)
            });

            // get elements form table and add to sharedUsers
            tableBody = document.getElementById('sharedUsersTableBody');
            for (let i = 0; i < tableBody.rows.length; i++) {
                const row = tableBody.rows[i];
                const username = row.cells[0].innerText;
                sharedUsers.push(username);
            }

        });
    </script>
</body>

</html>