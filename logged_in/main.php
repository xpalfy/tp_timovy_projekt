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
    <link rel="stylesheet" href="../css/main.css">
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
        <!-- Welcome message -->
        <div class="row">
            <div class="col-md-12 text-center">
                <h2 class="">Welcome back, <span class="text-primary"><?php echo $_SESSION['user']['username']; ?></span></h2>
            </div>
        </div>
        <!-- Main Content: Scan Document section centered and bigger -->
        <div class="row justify-content-center mt-5">
            <!-- Card 1: View History -->
            <div class="col-md">
                <div class="card shadow-sm h-100 view-history">
                    <div class="card-body text-center">
                        <h5 class="card-title">View History</h5>
                        <p class="card-text text-muted">Review your previous scans and manage your documents.</p>
                        <a href="#" class="btn btn-outline-primary">View History</a>
                    </div>
                </div>
            </div>
            <!-- Centered, larger Scan Document card -->
            <div class="col-md-8">
                <div class="card shadow-lg h-100 scan-document">
                    <div class="card-body text-center">
                        <h4 class="card-title font-weight-bold">Scan Document</h4>
                        <p class="card-text">Start scanning your handwritten documents now with just one click.</p>
                        <a href="#" class="btn btn-dark btn-block btn-lg">Upload file</a>
                    </div>
                </div>
            </div>

            <!-- Card 2: Account Settings -->
            <div class="col-md text-right">
                <div class="card shadow-sm h-100 account-settings">
                    <div class="card-body">
                        <h5 class="card-title text-center">Account Settings</h5>
                        <p class="card-text text-muted text-center">Manage your account settings and preferences.</p>
                        <a href="#" class="btn btn-outline-success btn-block">Account Settings</a>
                    </div>
                </div>
            </div>
        </div>
        <!-- Additional options (View History and Account Settings) -->
        <div class="row justify-content-between mt-4">

        </div>
    </div>
</div>


<!-- Footer remains the same -->
<footer class="footer bg-dark text-center text-white py-3">
    © Project Site <a href="https://tptimovyprojekt.ddns.net/" class="text-white">tptimovyprojekt.ddns.net</a>
</footer>

</body>
</html>