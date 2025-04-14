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
            background-color:rgb(189, 175, 145);
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
    <nav class="bg-[#d7c7a5] p-4 text-papyrus shadow-md sticky top-0 z-50 border-b border-yellow-300">
        <div class="container mx-auto flex justify-between items-center">
            <div class="flex items-center space-x-3">
                <img src="../img/logo.png" alt="Logo" class="w-10 h-10">
                <h1 class="text-2xl font-bold">HandScript</h1>
            </div>
            <ul class="flex space-x-6 font-semibold">
                <li><a href="profile.php" class="hover:underline">Profile</a></li>
                <li><a href="documents.php" class="hover:underline">Documents</a></li>
                <li><a href="https://tptimovyprojekt.ddns.net/" class="hover:underline">Project</a></li>
                <li><a href="../logout.php" class="hover:underline">Logout</a></li>
            </ul>
        </div>
    </nav>

    <main class="flex-grow">
        <!-- My Documents Section -->
        <section class="container mx-auto px-6 py-10">
            <h2 class="text-4xl font-bold text-center text-papyrus mb-2" data-aos="fade-up">📁 My Documents</h2>
            <p class="text-center text-lg mb-8 text-papyrus" data-aos="fade-up" data-aos-delay="150">View and edit your
                documents here</p>
            
            <div class="display-btns">
                <button class="item-display-btn" id="item-display-btn-my" onclick="changeToItemDispMy()"></button>
                <button class="list-display-btn" id="list-display-btn-my" onclick="changeToListDispMy()"></button>
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 gap-6"
                style="display: flex; justify-content: center; align-items: flex-start; flex-wrap: wrap;">
                <?php
                require_once '../config.php';
                $conn = getDatabaseConnection();

                $sql = "SELECT ID ,path FROM pictures WHERE creator = ?";
                $stmt = $conn->prepare($sql);
                $stmt->bind_param("i", $userData['id']);
                $stmt->execute();
                $result = $stmt->get_result();

                if ($result->num_rows > 0) {
                    while ($row = $result->fetch_assoc()) {
                        echo '<div class="card-pic">';
                        echo '<img src="..' . $row['path'] . '" class="" alt="..." >';
                        echo '<div class="card-body">';
                        echo '<h5 class="card-title">' . pathinfo($row['path'], PATHINFO_FILENAME) . '</h5>';
                        echo '<div class="card-buttons">';
                        echo '<a href="editDocument.php?id=' . $row['ID'] . '&user=' . $userData['id'] . '" class="btn btn-primary">Edit</a>';
                        echo '<a href="deleteDocument.php?id=' . $row['ID'] . '&user=' . $userData['id'] . '" class="btn btn-danger">Delete</a>';
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
        </section>

        <!-- Shared Documents Section -->
        <section class="container mx-auto px-6 py-10">
            <h2 class="text-4xl font-bold text-center text-papyrus mb-2" data-aos="fade-up">🔗 Shared Documents</h2>
            <p class="text-center text-lg mb-8 text-papyrus" data-aos="fade-up" data-aos-delay="150">View and edit
                shared
                documents</p>

            <div class="display-btns">
                <button class="item-display-btn" id="item-display-btn-shared" onclick="changeToItemDispShared()"></button>
                <button class="list-display-btn" id="list-display-btn-shared" onclick="changeToListDispShared()"></button>
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 gap-6"
                style="display: flex; justify-content: center; align-items: flex-start; flex-wrap: wrap;">
                <?php
                $sql = "SELECT picture_id FROM users_pictures WHERE user_id = ?";
                $stmt = $conn->prepare($sql);
                $stmt->bind_param("i", $userData['id']);
                $stmt->execute();
                $result = $stmt->get_result();

                if ($result->num_rows > 0) {
                    while ($row = $result->fetch_assoc()) {
                        $sql2 = "SELECT ID, path FROM pictures WHERE ID = ?";
                        $stmt2 = $conn->prepare($sql2);
                        $stmt2->bind_param("i", $row['picture_id']);
                        $stmt2->execute();
                        $result2 = $stmt2->get_result();

                        if ($result2->num_rows > 0) {
                            while ($row2 = $result2->fetch_assoc()) {
                                echo '<div class="card-pic">';
                                echo '<img src="..' . $row2['path'] . '" class="" alt="..." >';
                                echo '<div class="card-body">';
                                echo '<h5 class="card-title">' . pathinfo($row2['path'], PATHINFO_FILENAME) . '</h5>';
                                echo '<div class="card-buttons">';
                                echo '<a href="editDocument.php?id=' . $row2['ID'] . '&user=' . $userData['id'] . '" class="btn btn-primary">Edit</a>';
                                echo '</div>';
                                echo '</div>';
                                echo '</div>';
                            }
                        }
                    }
                } else {
                    echo '<div class="col-span-3 text-center text-gray-600" data-aos="fade-up">No shared documents found</div>';
                }

                $stmt->close();
                $conn->close();
                ?>
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
            // TODO: display the grid as default
        }
        function changeToListDispMy() {
            document.getElementById("item-display-btn-my").classList.remove("active");
            document.getElementById("list-display-btn-my").classList.add("active");
            // TODO: display the list
        }
        function changeToItemDispShared() {
            document.getElementById("list-display-btn-shared").classList.remove("active");
            document.getElementById("item-display-btn-shared").classList.add("active");
            // TODO: display the grid as default
        }
        function changeToListDispShared() {
            document.getElementById("item-display-btn-shared").classList.remove("active");
            document.getElementById("list-display-btn-shared").classList.add("active");
            // TODO: display the list
        }

        function checkToasts() {
            let toast = <?php echo json_encode($_SESSION['toast'] ?? null); ?>;
            if (toast) {
                toastr[toast.type](toast.message);
                <?php unset($_SESSION['toast']); ?>
            }
        }

        checkToasts();
        changeToItemDispMy();
        changeToItemDispShared();

        // scroll to top of window
        window.scrollTo(0, 0);
    
        AOS.init({
            duration: 800,
            once: true
        });
    </script>
</body>

</html>