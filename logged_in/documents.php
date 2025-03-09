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
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Main</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.css">
    <link rel="stylesheet" href="../css/profile.css">
    <link rel="stylesheet" href="../css/documents.css">
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/idb/build/iife/index-min.js"></script>

</head>

<body>
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
    <nav class="navbar navbar-expand-lg navbar-dark bg-dark sticky-top" style="transition: top 0.3s;" id="navbar">
        <a class="navbar-brand" href="main.php" style="font-size: xx-large; display: flex; align-items: center;">
            <img src="../img/logo.png" alt="Logo" style="width: 40px; height: 40px; margin-right: 15px;">
            HandScript
        </a>
        <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarNav"
            aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
            <span class="navbar-toggler-icon"></span>
        </button>
        <div class="collapse navbar-collapse" id="navbarNav">
            <ul class="navbar-nav ml-auto">

                <li class="nav-item">
                    <a class="nav-link " href="profile.php">
                        Profile
                        <img src="../img/account.png" alt="profile"
                            style="width: 25px; height: 25px; margin-right: 8px; margin-left: 4px;">
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link " href="documents.php">
                        Documents
                        <img src="../img/document.png" alt="document"
                            style="width: 25px; height: 25px; margin-right: 8px; margin-left: 4px;">
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="https://tptimovyprojekt.ddns.net/">
                        Project
                        <img src="../img/web.png" alt="Project"
                            style="width: 25px; height: 25px; margin-right: 8px; margin-left: 4px;">
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link  " href="../logout.php">
                        Logout
                        <img src="../img/logout.png" alt="logout"
                            style="width: 25px; height: 25px; margin-right: 8px; margin-left: 4px;">
                    </a>
                </li>
            </ul>
        </div>
    </nav>

    <script>
        let lastScrollTop = 0;
        const navbar = document.getElementById('navbar');

        window.addEventListener('scroll', function () {
            const currentScroll = window.pageYOffset || document.documentElement.scrollTop;
            if (currentScroll > lastScrollTop) {    
                // Scrolling down
                navbar.style.top = '-80px';
            } else {
                // Scrolling up
                navbar.style.top = '0';
            }
            lastScrollTop = currentScroll <= 0 ? 0 : currentScroll; // Avoid negative values
        });
    </script>

    <div class="background-image"></div>

    <div class="cont mb-5 pt-5">
        <div class="container mb-5 pt-5">
            <div class="row">
                <div class="col-md-12 text-center">
                    <h1 class="display-4 font-weight-bold mb-4">My Documents</h1>
                </div>
            </div>
            <div class="row">
                <div class="col-md-12 text-center">
                    <h2>View and edit your documents here</h2>
                </div>
            </div>
            <div class="cont">
                <?php
                require '../config.php';

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
                    echo '<div class="alert alert-info" role="alert">No documents found</div>';
                }

                $stmt->close();
                $conn->close();

                ?>
            </div>
        </div>
    </div>
    <footer class="footer bg-dark text-center text-white py-3">
        © Project Site <a href="https://tptimovyprojekt.ddns.net/" class="text-white">tptimovyprojekt.ddns.net</a>
    </footer>
</body>

</html>