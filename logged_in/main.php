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
    <link rel="stylesheet" href="../css/main.css">
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/idb/build/iife/index-min.js"></script>

</head>
<body>
<script>
    function handleDrop(event) {
        event.preventDefault();
        let file = event.dataTransfer.files[0];
        if (file.type.match('image.*')) {
            let reader = new FileReader();
            reader.onload = function (e) {
                let image = document.getElementById('imagePreview');
                document.getElementById('image_name').innerHTML = file.name.split('.')[0].split(' ').join('_').toLowerCase();
                image.src = e.target.result;
                image.style.display = 'block';
                document.getElementById('SaveBtns').style.display = 'flex';
                document.getElementById('SaveBtnsInfo').style.display = 'none';
            };
            reader.readAsDataURL(file);
        } else {
            toastr.error('Please upload an image file.');
        }
    }

    function handleDragOver(event) {
        event.preventDefault();
        document.getElementById('imageUploader').style.border = '2px dashed #007bff';
    }

    function handleDragLeave() {
        document.getElementById('imageUploader').style.border = 'none';
    }

    function previewImageButton(event) {
        let file = event.target.files[0];
        if (file.type.match('image.*')) {
            let reader = new FileReader();
            reader.onload = function (e) {
                let image = document.getElementById('imagePreview');
                document.getElementById('image_name').innerHTML = file.name.split('.')[0].split(' ').join('_').toLowerCase();
                image.src = e.target.result;
                image.style.display = 'block';
                document.getElementById('SaveBtns').style.display = 'flex';
                document.getElementById('SaveBtnsInfo').style.display = 'none';
            };
            reader.readAsDataURL(file);
        } else {
            toastr.error('Please upload an image file.');
        }
    }

    function saveKey() {
        let image = document.getElementById('imagePreview');
        let data = image.src;
        fetch('savePicture.php', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json'
            },
            body: JSON.stringify({
                data: data,
                data_name: document.getElementById('image_name').innerHTML,
                user_name: <?php echo json_encode($userData['username']); ?>,
                type: 'KEYS',
                id: <?php echo json_encode($userData['id']); ?>
            })
        }).then(response => response.json())
            .then(data => {
                console.log(data);
                if (data.success) {
                    toastr.success(data.message);
                } else {
                    toastr.error(data.error);
                }
            });

    }

    function saveCipher() {
        let image = document.getElementById('imagePreview');
        let data = image.src;
        fetch('savePicture.php', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json'
            },
            body: JSON.stringify({
                data: data,
                data_name: document.getElementById('image_name').innerHTML,
                user_name: <?php echo json_encode($userData['username']); ?>,
                type: 'CIPHER',
                id: <?php echo json_encode($userData['id']); ?>
            })
        }).then(response => response.json())
            .then(data => {
                console.log(data);
                if (data.success) {
                    toastr.success(data.message);
                } else {
                    toastr.error(data.error);
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
        <div class="row">
            <div class="col-md-12 text-center">
                <h1 class="display-4 font-weight-bold mb-4">Dashboard</h1>
            </div>
        </div>
        <div class="row">
            <div class="col-md-12 text-center">
                <h2 class="">Welcome back, <span class="text-primary"
                                                 id="username"><?php echo $userData['username']; ?></span>!</h2>
            </div>
        </div>
        <div class="row justify-content-center mt-5">
            <div class="col-md">
                <div class="card shadow-sm h-100 view-history">
                    <div class="card-body text-center">
                        <h5 class="card-title">View Documents</h5>
                        <p class="card-text text-muted">Review your previous scans and manage your documents.</p>
                        <a href="documents.php" class="btn btn-outline-primary">View Documents</a>
                    </div>
                </div>
            </div>
            <div class="col-md text-right">
                <div class="card shadow-sm h-100 account-settings">
                    <div class="card-body">
                        <h5 class="card-title text-center">Account Settings</h5>
                        <p class="card-text text-muted text-center">Manage your account settings and preferences.</p>
                        <a href="profile.php" class="btn btn-outline-success btn-block">Account Settings</a>
                    </div>
                </div>
            </div>
        </div>
        <div class="row justify-content-center mt-5">
            <div class="col-md mt-5">
                <div class="card shadow-lg h-100 p-4 text-center scan-document"
                     id="imageUploader"
                     ondrop="handleDrop(event)"
                     ondragover="handleDragOver(event)"
                     ondragleave="handleDragLeave()">
                    <h4 class="card-title font-weight-bold mb-3">Upload Image</h4>
                    <p class="card-text">Drag & Drop an image here or click the button below to upload.</p>

                    <div id="previewContainer"
                         style="min-height: 150px; margin-top: 15px; display: flex; justify-content: center">
                        <img id="imagePreview" src="" alt=""
                             style="max-width: 100%; display: none; border: 2px white solid; border-radius: 10px; padding: 10px">
                    </div>

                    <input type="file" id="fileInput" accept="image/*" style="display: none;"
                           onchange="previewImageButton(event)">
                    <div class="row justify-content-center mt-3">
                        <div class="col-md-4">
                            <button class="btn btn-secondary btn-block"
                                    onclick="document.getElementById('fileInput').click()">Select Image
                            </button>
                        </div>
                    </div>
                    <div class="row justify-content-center mt-3" style="display: none" id="SaveBtns">
                        <div class="col-md-4">
                            <button class="btn btn-info btn-block" onclick="saveKey()">Save as Key</button>
                        </div>
                        <div class="col-md-4">
                            <button class="btn btn-info btn-block" onclick="saveCipher()">Save as Cipher Text</button>
                        </div>
                    </div>
                    <div class="row justify-content-center mt-3" style="display: none" id="SaveBtnsInfo">
                        <div class="col-md-8">
                            <div class="p-3 mb-2 bg-danger text-white" style="border-radius: 10px">Please select a
                                directory to store the image in the Account Settings page.
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <p id="image_name" style="display: none"></p>

        <div class="row justify-content-between mt-4">

        </div>
    </div>
</div>

<footer class="footer bg-dark text-center text-white py-3">
    © Project Site <a href="https://tptimovyprojekt.ddns.net/" class="text-white">tptimovyprojekt.ddns.net</a>
</footer>

</body>
</html>