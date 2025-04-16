<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);
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
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>HandScript - Documents</title>

    <link rel="stylesheet" href="../css/documents.css?v=2.0">

    <!-- Tailwind CSS -->
    <script src="https://cdn.tailwindcss.com"></script>

    <!-- AOS for animations -->
    <link href="https://unpkg.com/aos@2.3.1/dist/aos.css" rel="stylesheet">
    <script src="https://unpkg.com/aos@2.3.1/dist/aos.js"></script>

    <!-- Toastr -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.css">
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.6.0/jquery.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.js"></script>

    <!-- jQuery (MUST come first) -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.6.0/jquery.min.js"></script>

    <!-- DataTables -->
    <link rel="stylesheet" href="https://cdn.datatables.net/1.13.6/css/jquery.dataTables.min.css">
    <script src="https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script>



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

        .card-img {
            max-height: 250px;
            object-fit: cover;
        }

        .card-pic {
            @apply bg-white rounded-xl shadow-md p-4 transition-transform transform hover:scale-105;
        }

        .display-btns {
            display: flex;
            justify-content: center;
            gap: 10px;
            margin-bottom: 20px;
        }

        .item-display-btn,
        .list-display-btn {
            background-color: rgb(189, 175, 145);
            color: #3b2f1d;
            border: none;
            padding: 20px;
            border-radius: 5px;
            cursor: pointer;
            font-size: 16px;
            transition: background-color 0.3s, transform 0.3s;
            border: #3b2f1d 1px solid;
        }

        .item-display-btn:hover,
        .list-display-btn:hover {
            background-color: #cdbf9b;
            transform: scale(1.10);
        }

        .item-display-btn.active,
        .list-display-btn.active {
            background-color: #cdbf9b;
            color: #3b2f1d;
            font-weight: bold;
            transform: scale(1.10);
        }

        .item-display-btn {
            background-image: url('../img/item.png?');
            background-size: 20px 20px;
            background-repeat: no-repeat;
            background-position: center left 10px;
        }

        .list-display-btn {
            background-image: url('../img/list.png?');
            background-size: 20px 20px;
            background-repeat: no-repeat;
            background-position: center left 10px;
        }
    </style>

</head>

<body class="min-h-screen select-none flex flex-col">



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

    <main class="flex-grow">
        <!-- My Documents Section -->
        <section class="container mx-auto px-6 py-10">
            <h2 class="text-4xl font-bold text-center text-papyrus mb-2" data-aos="fade-up">📁 My Documents</h2>
            <p class="text-center text-lg mb-8 text-papyrus" data-aos="fade-up" data-aos-delay="150">View and edit your
                documents here</p>

            <div class="display-btns" data-aos="flip-up" data-aos-delay="1000">
                <button class="item-display-btn" id="item-display-btn-my" onclick="changeToItemDispMy()"></button>
                <button class="list-display-btn" id="list-display-btn-my" onclick="changeToListDispMy()"></button>
            </div>

            <div id="my-documents-grid" class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 gap-6"
                style="display: flex; justify-content: center; align-items: flex-start; flex-wrap: wrap;">
                <?php
                require_once '../config.php';
                $conn = getDatabaseConnection();
                $sql = "SELECT * FROM documents WHERE author_id = ?";
                $stmt = $conn->prepare($sql);
                $stmt->bind_param("i", $userData['id']);
                $stmt->execute();
                $result = $stmt->get_result();
                if ($result->num_rows > 0) {
                    while ($row = $result->fetch_assoc()) {
                        $sql2 = "SELECT image_path FROM items WHERE document_id = ?";
                        $stmt2 = $conn->prepare($sql2);
                        $stmt2->bind_param("i", $row['id']);
                        $stmt2->execute();
                        $result2 = $stmt2->get_result();
                        $row2 = $result2->fetch_assoc();
                        echo '<div class="card-pic">';
                        echo '<img src="..' . $row2['image_path'] . '" class="" alt="..." >';
                        echo '<div class="card-body">';
                        echo '<h5 class="card-title">' . $row['title'] . '</h5>';
                        echo '<div class="card-buttons">';
                        echo '<a href="editDocument.php?id=' . $row['id'] . '&user=' . $userData['id'] . '" class="btn btn-primary">Edit</a>';
                        echo '<button onclick="deleteDocument(' . $row['id'] . ')" class="btn btn-danger">Delete</button>';
                        echo '</div>';
                        echo '</div>';
                        echo '</div>';
                    }
                } else {
                    echo '<div class="col-span-3 text-center text-gray-600" data-aos="fade-up">No documents found</div>';
                }
                $stmt->close();
                ?>
            </div>

            <!-- My Documents table -->
            <div id="my-documents-table-wrapper" class="w-full overflow-x-auto mt-4" style="display: none;">
                <table id="my-documents-table" class="display">
                    <thead>
                        <tr>
                            <th>Name</th>
                            <th>Number of Items</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                        $sql = "SELECT * FROM documents WHERE author_id = ?";
                        $stmt = $conn->prepare($sql);
                        $stmt->bind_param("i", $userData['id']);
                        $stmt->execute();
                        $result = $stmt->get_result();
                        while ($row = $result->fetch_assoc()) {
                            $sql2 = "SELECT id FROM items WHERE document_id = ?";
                            $stmt2 = $conn->prepare($sql2);
                            $stmt2->bind_param("i", $row['id']);
                            $stmt2->execute();
                            $result2 = $stmt2->get_result();
                            $item_count = $result2->num_rows;
                            echo '<tr>';
                            echo '<td>' . $row['title'] . '</td>';
                            echo '<td>' . $item_count . '</td>';
                            echo '<td><a href="editDocument.php?id=' . $row['id'] . '&user=' . $userData['id'] . '" class="btn btn-sm btn-primary mr-2">Edit</a>
                          <button onclick="deleteDocument(' . $row['id'] . ')" class="btn btn-sm btn-danger">Delete</button></td>';
                            echo '</tr>';
                        }
                        $stmt->close();
                        ?>
                    </tbody>
                </table>
            </div>
        </section>

        <!-- Shared Documents Section -->
        <section class="container mx-auto px-6 py-10">
            <h2 class="text-4xl font-bold text-center text-papyrus mb-2" data-aos="fade-up">🔗 Shared Documents</h2>
            <p class="text-center text-lg mb-8 text-papyrus" data-aos="fade-up" data-aos-delay="150">View and edit
                shared
                documents</p>

            <div class="display-btns" data-aos="flip-up" data-aos-delay="1000">
                <button class="item-display-btn" id="item-display-btn-shared"
                    onclick="changeToItemDispShared()"></button>
                <button class=" list-display-btn" id="list-display-btn-shared"
                    onclick="changeToListDispShared()"></button>
            </div>

            <div id="shared-documents-grid" class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 gap-6"
                style="display: flex; justify-content: center; align-items: flex-start; flex-wrap: wrap;">
                <?php
                $sql = "SELECT document_id FROM document_user_association WHERE user_id = ?";
                $stmt = $conn->prepare($sql);
                $stmt->bind_param("i", $userData['id']);
                $stmt->execute();
                $result = $stmt->get_result();
                $stmt->close();
                if ($result->num_rows > 0) {
                    while ($row = $result->fetch_assoc()) {
                        $sql2 = "SELECT * FROM documents WHERE id = ?";
                        $stmt2 = $conn->prepare($sql2);
                        $stmt2->bind_param("i", $row['document_id']);
                        $stmt2->execute();
                        $result2 = $stmt2->get_result();
                        $stmt2->close();
                        if ($result2->num_rows > 0) {
                            while ($row2 = $result2->fetch_assoc()) {
                                $sql3 = "SELECT image_path FROM items WHERE document_id = ?";
                                $stmt3 = $conn->prepare($sql3);
                                $stmt3->bind_param("i", $row2['id']);
                                $stmt3->execute();
                                $result3 = $stmt3->get_result();
                                $row3 = $result3->fetch_assoc();
                                echo '<div class="card-pic">';
                                echo '<img src="..' . $row3['image_path'] . '" class="" alt="..." >';
                                echo '<div class="card-body">';
                                echo '<h5 class="card-title">' . $row2['title'] . '</h5>';
                                echo '<div class="card-buttons">';
                                echo '<a href="editDocument.php?id=' . $row2['id'] . '&user=' . $userData['id'] . '" class="btn btn-primary">Edit</a>';
                                echo '</div>';
                                echo '</div>';
                                echo '</div>';
                                $stmt3->close();
                            }
                        }
                    }
                } else {
                    echo '<div class="col-span-3 text-center text-gray-600" data-aos="fade-up">No shared documents found</div>';
                }
                ?>
            </div>

            <!-- Shared Documents table -->
            <div id="shared-documents-table-wrapper" class="w-full overflow-x-auto mt-4" style="display: none;">
                <table id="shared-documents-table" class="display">
                    <thead>
                        <tr>
                            <th>Name</th>
                            <th>Number of Items</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                        $sql = "SELECT document_id FROM document_user_association WHERE user_id = ?";
                        $stmt = $conn->prepare($sql);
                        $stmt->bind_param("i", $userData['id']);
                        $stmt->execute();
                        $result = $stmt->get_result();
                        $stmt->close();
                        if ($result->num_rows > 0) {
                            while ($row = $result->fetch_assoc()) {
                                $sql2 = "SELECT * FROM documents WHERE id = ?";
                                $stmt2 = $conn->prepare($sql2);
                                $stmt2->bind_param("i", $row['document_id']);
                                $stmt2->execute();
                                $result2 = $stmt2->get_result();
                                $stmt2->close();
                                if ($result2->num_rows > 0) {
                                    while ($row2 = $result2->fetch_assoc()) {
                                        $sql3 = "SELECT image_path FROM items WHERE document_id = ?";
                                        $stmt3 = $conn->prepare($sql3);
                                        $stmt3->bind_param("i", $row2['id']);
                                        $stmt3->execute();
                                        $result3 = $stmt3->get_result();
                                        $item_count = $result3->num_rows;
                                        echo '<tr>';
                                        echo '<td>' . $row2['title'] . '</td>';
                                        echo '<td>' . $item_count . '</td>';
                                        echo '<td><a href="editDocument.php?id=' . $row2['id'] . '&user=' . $userData['id'] . '" class="btn btn-sm btn-primary mr-2">Edit</a></td>';
                                        echo '</tr>';
                                        $stmt3->close();
                                    }
                                }
                            }
                        } else {
                            echo '<div class="col-span-3 text-center text-gray-600" data-aos="fade-up">No shared documents found</div>';
                        }
                        $conn->close();
                        ?>
                    </tbody>
                </table>
            </div>
        </section>
    </main>



    <footer class="bg-[#d7c7a5] text-center text-papyrus py-4 mt-10 border-t border-yellow-300">
        &copy; 2025 HandScript – <a href="https://tptimovyprojekt.ddns.net/" class="underline">Visit Project Page</a>
    </footer>

    <script>
        function changeToItemDispMy() {
            document.getElementById("list-display-btn-my").classList.remove("active");
            document.getElementById("item-display-btn-my").classList.add("active");
            document.getElementById("my-documents-grid").style.display = "flex";
            document.getElementById("my-documents-table-wrapper").style.display = "none";
        }

        function changeToListDispMy() {
            document.getElementById("item-display-btn-my").classList.remove("active");
            document.getElementById("list-display-btn-my").classList.add("active");
            document.getElementById("my-documents-grid").style.display = "none";
            document.getElementById("my-documents-table-wrapper").style.display = "block";

            if ($.fn.DataTable.isDataTable('#my-documents-table')) {
                $('#my-documents-table').DataTable().destroy();
            }
            $('#my-documents-table').DataTable();
        }

        function changeToItemDispShared() {
            document.getElementById("list-display-btn-shared").classList.remove("active");
            document.getElementById("item-display-btn-shared").classList.add("active");
            document.getElementById("shared-documents-grid").style.display = "flex";
            document.getElementById("shared-documents-table-wrapper").style.display = "none";
        }

        function changeToListDispShared() {
            document.getElementById("item-display-btn-shared").classList.remove("active");
            document.getElementById("list-display-btn-shared").classList.add("active");
            document.getElementById("shared-documents-grid").style.display = "none";
            document.getElementById("shared-documents-table-wrapper").style.display = "block";

            if ($.fn.DataTable.isDataTable('#shared-documents-table')) {
                $('#shared-documents-table').DataTable().destroy();
            }
            $('#shared-documents-table').DataTable();
        }

        function deleteDocument(documentId) {
            if (confirm("Are you sure you want to delete this document?")) {
                fetch('documents/deleteDocument.php', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json'
                    },
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
                            setTimeout(() => {
                                location.reload();
                            }, 1000);
                        }
                    })
                    .catch(error => {
                        console.error('Error:', error);
                        toastr.error("An error occurred while deleting the document");
                    });
            }
        }


        function checkToasts() {
            let toast = <?php echo json_encode($_SESSION['toast'] ?? null); ?>;
            if (toast) {
                toastr[toast.type](toast.message);
                <?php unset($_SESSION['toast']); ?>
            }
        }

        checkToasts();
        setTimeout(function () {
            changeToItemDispMy();
            changeToItemDispShared();
        }, 800);

        // scroll to top of window
        window.scrollTo(0, 0);

        AOS.init({
            duration: 800,
            once: true
        });
    </script>
</body>

</html>