<?php
require '../checkType.php';

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

check();
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

    function pathButtonPressed(){
        // get directory path from system
        // TODO: implement



    }
</script>
<nav class="navbar navbar-expand-lg navbar-dark bg-dark">
    <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarNav"
            aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
        <span class="navbar-toggler-icon"></span>
    </button>
    <div class="collapse navbar-collapse" id="navbarNav">
        <ul class="navbar-nav ml-auto">
            <li class="nav-item">
                <a class="nav-link" href="https://tptimovyprojekt.ddns.net/">Project</a>
            </li>
            <li>
                <a class="nav-link" href="main.php">Home</a>
            </li>
            <li class="nav-item">
                <a class="nav-link" href="../logout.php">Logout</a>
            </li>
        </ul>
    </div>
</nav>

<div class="background-image"></div>



<div class="cont mb-5 pt-5">
    <div class="container">
        <!-- Main title -->
        <div class="row">
            <div class="col-md-12 text-center">
                <h1 class="display-4 font-weight-bold mb-4">Dashboard</h1>
            </div>
        </div>
        <div class="row">
            <div class="col-md-12 text-center">
                <h2 class="">Welcome back, <span class="text-primary"><?php echo $_SESSION['user']['username']; ?></span></h2>
            </div>
        </div>
        <!-- Welcome message -->
        <div class="container">
            <div class="input-group mb-3 col-md-8">
                <div class="input-group-prepend">
                    <span class="input-group-text" id="basic-addon1">@</span>
                </div>
                <input type="text" class="form-control" placeholder="Username" aria-label="Username" aria-describedby="basic-addon1">
            </div>
            <div class="input-group col-md-8 mb-3">
                <div class="input-group-prepend">
                    <span class="input-group-text" id="">New password twice</span>
                </div>
                <input type="text" class="form-control" id="password">
                <input type="text" class="form-control" id="password2">
            </div>
                <div class="input-group mb-3 col-md-8">
                    <input disabled type="text" class="form-control" placeholder="Directory path to stored files" aria-label="Recipient's username" aria-describedby="basic-addon2">
                    <div class="input-group-append">
                        <button class="btn btn-secondary" type="button" onclick="pathButtonPressed()">...</button>
                    </div>
                </div>
        </div>
    </div>
</div>


<!-- Footer remains the same -->
<footer class="footer bg-dark text-center text-white py-3">
    © Project Site <a href="https://tptimovyprojekt.ddns.net/" class="text-white">tptimovyprojekt.ddns.net</a>
</footer>

</body>
</html>