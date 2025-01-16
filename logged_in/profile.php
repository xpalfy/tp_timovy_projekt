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
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/idb/build/iife/index-min.js"></script>
    <script src="../js/regex.js"></script>

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
    <nav class="navbar navbar-expand-lg navbar-dark bg-dark">
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

    <div class="background-image"></div>


    <div class="cont mb-5 pt-5">
        <div class="container mb-5 pt-5">
            <div class="row">
                <div class="col-md-12 text-center">
                    <h1 class="display-4 font-weight-bold mb-4">Profile</h1>
                </div>
            </div>

            <div class="row">
                <div class="col-md-12 text-center">
                    <h2>Welcome back, <span class="text-primary"><?php echo $userData['username']; ?></span>
                    </h2>
                </div>
            </div>

            <form class="container mt-4"
                style="display: flex; flex-direction: column; align-items: center; flex-wrap: wrap;"
                action="profileUpdate.php" method="post">
                <div class="input-group mb-3 col-md-8">
                    <div class="input-group-prepend">
                        <span class="input-group-text" id="basic-addon1">🇮🇩</span>
                    </div>
                    <input id="username" name="username" type="text" class="form-control" placeholder="Username"
                        aria-label="Username" aria-describedby="basic-addon1">
                </div>

                <div class="input-group mb-3 col-md-8">
                    <div class="input-group-prepend">
                        <span class="input-group-text" id="basic-addon1">@</span>
                    </div>
                    <input type="email" class="form-control" id="email" name="email" oninput="isValidEmail(this)"
                        placeholder="Email">
                </div>

                <div class="input-group col-md-8 mb-3">
                    <div class="input-group-prepend">
                        <span class="input-group-text"><img src="../img/lock.png" width="20" draggable="false"></span>
                    </div>
                    <input type="password" class="form-control" id="password" name="password"
                        placeholder="New Password">
                    <input type="password" class="form-control" id="password_confirm" name="password_confirm"
                        placeholder="New Password Again">
                </div>

                <div class="input-group col-md-3 justify-content-center mb-3">
                    <button type="submit" class="btn btn-secondary mb-3">Submit</button>
                </div>
            </form>
        </div>
    </div>

    <footer class="footer bg-dark text-center text-white py-3">
        © Project Site <a href="https://tptimovyprojekt.ddns.net/" class="text-white">tptimovyprojekt.ddns.net</a>
    </footer>


</body>

</html>