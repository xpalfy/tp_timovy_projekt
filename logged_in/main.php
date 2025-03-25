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
    <script src="https://unpkg.com/@dotlottie/player-component@2.7.12/dist/dotlottie-player.mjs" type="module"></script>
    <script src="../js/segment-rect.js?" type="module"></script>

</head>

<body>
    <div id="navbar-container" style="background: black; position: absolute; width: 100%; height: 100px;"></div>

    <nav class="navbar navbar-expand-lg navbar-dark sticky-top" style="transition: top 0.3s;" id="navbar">
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
    <div class="gradient-effect"></div>

    <!-- Welcome Section with Tutorial -->
    <div id="welcome-section" class="text-center container">
        <h1 class="display-4">Welcome, <?php echo $userData['username']; ?>!</h1>
        <p class="lead">Explore the key features of HandScript.</p>

        <div id="tutorialCarousel" class="carousel slide mt-4" data-ride="carousel">
            <ol class="carousel-indicators">
                <li data-target="#tutorialCarousel" data-slide-to="0" class="active"></li>
                <li data-target="#tutorialCarousel" data-slide-to="1"></li>
                <li data-target="#tutorialCarousel" data-slide-to="2"></li>
                <li data-target="#tutorialCarousel" data-slide-to="3"></li>
                <li data-target="#tutorialCarousel" data-slide-to="4"></li>
            </ol>
            <div class="carousel-inner">
                <div class="carousel-item active">
                    <div class="carousel-content">
                        <h3>🚀 What is HandScript?</h3>
                        <p>Lorem ipsum dolor sit amet, consectetur adipiscing elit. Nunc in suscipit magna. Pellentesque
                            tincidunt cursus neque, a molestie dui vestibulum sit amet. Duis ut ante nibh. Nulla a nisl
                            quis augue elementum rhoncus quis quis libero. Mauris nec eros at lacus semper viverra in et
                            felis. Praesent ac massa turpis. Nam augue magna, semper sit amet erat vel, sollicitudin
                            sodales nisi. Sed rutrum nibh eu dapibus suscipit. Phasellus sed suscipit est. Pellentesque
                            elit felis, facilisis in lacus in, interdum tempor elit.</p>

                        <p>Suspendisse tincidunt tincidunt leo a mattis. Aenean convallis, justo a mollis fermentum,
                            sapien ante vestibulum turpis, at malesuada ante quam eu dolor. Integer ullamcorper pharetra
                            turpis. Vestibulum at scelerisque nulla. Cras rutrum porta tortor, eu accumsan augue auctor
                            eget. Fusce nec felis hendrerit, fringilla mi a, suscipit justo. Fusce egestas eros sit amet
                            justo dignissim fringilla.</p>

                        <p>Sed aliquam lacus ut erat efficitur, eu pharetra justo scelerisque. Vestibulum ante ipsum
                            primis in faucibus orci luctus et ultrices posuere cubilia curae; Etiam efficitur id libero
                            fringilla gravida. Nunc ultrices ligula lacus. Nunc imperdiet neque ac lorem vestibulum
                            pharetra. In tincidunt dolor nisi, nec accumsan sem consequat eget. Mauris lacinia ante
                            gravida, mattis leo vel, fringilla diam. Ut ut blandit sem. Curabitur maximus faucibus
                            lobortis. Nulla dolor odio, mattis eu nunc non, iaculis venenatis nunc. Duis faucibus
                            aliquet mi ac congue. Vestibulum tristique nisi et vehicula elementum. Morbi ut arcu neque.
                            Donec semper rutrum orci quis posuere. Quisque in ornare quam. Nullam et justo libero.</p>
                    </div>
                </div>
                <div class="carousel-item">
                    <div class="carousel-content">
                        <h3>📂 Uploading Documents</h3>
                        <p>You can **drag and drop** or select a file to upload encrypted documents.</p>
                        <p>Documents containing encryption keys can be stored separately for later use.</p>
                    </div>
                </div>
                <div class="carousel-item">
                    <div class="carousel-content">
                        <h3>🔑 Multi-stage Decryption</h3>
                        <p>After uploading, the system allows for **multi-stage decryption of encrypted files**.</p>
                        <p>Users can **pause and resume** decryption anytime.</p>
                    </div>
                </div>
                <div class="carousel-item">
                    <div class="carousel-content">
                        <h3>💾 Secure Storage</h3>
                        <p>All user data is securely stored in a **MySQL database**.</p>
                        <p>Passwords are encrypted and data is protected.</p>
                    </div>
                </div>
                <div class="carousel-item">
                    <div class="carousel-content">
                        <h3>🛠️ Technology Used</h3>
                        <p>HandScript is built using **PHP, Bootstrap, and JavaScript**.</p>
                        <p>Back-end: **PHP & MySQL** | Front-end: **Bootstrap & jQuery**.</p>
                    </div>
                </div>
            </div>
            <a class="carousel-control-prev" href="#tutorialCarousel" role="button" data-slide="prev">
                <span class="carousel-control-prev-icon" aria-hidden="true"></span>
                <span class="sr-only">Previous</span>
            </a>
            <a class="carousel-control-next" href="#tutorialCarousel" role="button" data-slide="next">
                <span class="carousel-control-next-icon" aria-hidden="true"></span>
                <span class="sr-only">Next</span>
            </a>
        </div>
    </div>

    <a href="#Dashboard" style="text-align: center;"><img src="../img/arrow_down.png" alt="Try now" style="width: 50px;"
            id="arrow_down"></a>

    <!-- Styling for better visibility -->
    <style>
        #tutorialCarousel {
            margin: 30px auto 0px auto;
            background: rgba(0, 0, 0, 0.6);
            /* Transparent effect */
            border-radius: 10px;
        }

        #arrow_down {
            animation: zoom_and_point_down 9s infinite;
        }



        @keyframes zoom_and_point_down {
            0% {
                transform: scale(1);
            }

            5.55% {
                transform: scale(1.2);
            }

            11.1% {
                transform: scale(1);
            }

            16.65% {
                transform: translateY(+10px);
            }

            22.2% {
                transform: translateY(0);
            }

            27.75% {
                transform: translateY(+10px);
            }

            33.3% {
                transform: translateY(0);
            }
        }

        .carousel-content {
            padding: 20px;
            background: rgba(0, 0, 0, 0.25);
            border: 2px solid white;
            border-radius: 10px;
            color: #fff;
            text-align: center;
            min-height: 66vh;
        }



        /* Adjust the welcome section */
        #welcome-section {

            color: white;
            padding: 10px 0 0 0;
        }

        #welcome-section h1 {
            font-size: 2.5rem;
            font-weight: bold;
        }

        #welcome-section p {
            font-size: 1.2rem;
        }

        #navbar {
            background: rgba(52, 58, 64, 0.6);
            /* Transparent effect */
            border-radius: 10px;
            padding: 10px;
            margin: 10px;
            /* Glassmorphism effect */
            backdrop-filter: blur(10px);
        }

        .step-progress-container {
            display: flex;
            align-items: center;
            justify-content: center;
            width: 70%;
        }

        .step {
            width: 40px;
            height: 40px;
            background-color: #212529;
            color: #fff;
            border-radius: 50%;
            display: flex;
            justify-content: center;
            align-items: center;
            font-weight: bold;
            position: relative;
            z-index: 1;
            transition: background-color 1s;
        }

        .step.active {
            background-color: #007bff;
        }

        .line {
            flex: 1;
            height: 4px;
            background-color: #ccc;
            margin: 0 10px;
            z-index: 0;
            transition: background-color 1s;
        }

        .step.active+.line {
            background-color: #007bff;
        }

        .gradient-effect {
            position: absolute;
            max-height: 100vh;
            top: 100px;
            left: 0;
            width: 100%;
            height: 100%;
            background: linear-gradient(rgba(0, 0, 0, 1), rgba(255, 255, 255, 0));
            z-index: -1;
        }
    </style>

    <div id="Dashboard" class="cont mb-5 pt-5">
        <div class="container">
            <div class="row">
                <div class="col-md-12 text-center">
                    <h1 class="display-4 font-weight-bold mb-4">Dashboard</h1>
                </div>
            </div>
            <div class="row">
                <div class="col-md-12 text-center">
                    <h2 id="instruction_message">Welcome back, <span class="text-primary"
                            id="username"><?php echo $userData['username']; ?></span>!<br>Please Upload your images.
                    </h2>
                </div>
            </div>
            <div class="row mt-3 justify-content-center">
                <div class="step-progress-container">
                    <div class="step active">1</div>
                    <div class="line"></div>
                    <div class="step">2</div>
                    <div class="line"></div>
                    <div class="step">3</div>
                    <div class="line"></div>
                    <div class="step">4</div>
                </div>
            </div>

            <div class="row justify-content-center">
                <div class="col-md mt-5" id="UPLOAD-DOCUMENT-STEP">
                    <div class="card shadow-lg h-100 p-4 text-center scan-document" id="imageUploader"
                        ondrop="handleDrop(event)" ondragover="handleDragOver(event)" ondragleave="handleDragLeave()">
                        <div class="loading-cont"
                            style="overflow: hidden; position: absolute; left: 0; right: 0; bottom: 0; top: 0; display: none; justify-content: center; align-items: center; background-color:rgba(115, 124, 133, 0.52); z-index: 2;">
                            <dotlottie-player
                                src="https://lottie.host/4f6b3ace-c7fc-45e9-85a2-c1fe04047ae3/QLPJzOha5m.lottie"
                                background="transparent" speed="1" style="width: 150px; height: 150px;" loop
                                autoplay></dotlottie-player>
                        </div>
                        <h4 class="card-title font-weight-bold mb-3">Upload Images</h4>
                        <p class="card-text">Drag & Drop an images here or click the button below to upload.</p>

                        <div id="previewContainer" class="position-relative"
                            style="min-height: 200px; margin-top: 15px; display: flex; justify-content: center; align-items: center;">
                            <button class="prevBtn btn btn-light position-absolute"
                                style="left: 10px; z-index: 10; visibility: hidden;">❮</button>
                            <img class="imagePreview" src="" alt="Preview"
                                style="max-width: 100%; display: none; border: 2px white solid; border-radius: 10px; padding: 10px;">
                            <button class="nextBtn btn btn-light position-absolute"
                                style="right: 10px; z-index: 10; visibility: hidden;">❯</button>
                        </div>

                        <input type="file" id="fileInput" accept="image/*" style="display: none;"
                            onchange="previewImageButton(event)" multiple>
                        <div class="row justify-content-center mt-3">
                            <div class="col-md-4">
                                <button class="btn btn-secondary btn-block"
                                    onclick="document.getElementById('fileInput').click()">Select Images
                                </button>
                            </div>
                        </div>
                        <div class="row justify-content-center mt-3" style="display: none" id="SaveBtns">
                            <div class="col-md-4">
                                <button class="btn btn-block" onclick="segmentKey()" id="saveKey"
                                    style="background-color: #007bff; color: white;">Process Images as Key</button>
                            </div>
                            <div class="col-md-4">
                                <button class="btn btn-block" onclick="segmentCipher()" id="saveCipher"
                                    style="background-color: #007bff; color: white;">Process Images as Cipher
                                    Text</button>
                            </div>
                        </div>
                        <div class="row justify-content-center mt-3" id="SaveBtnsInfo">
                            <p class="fs-3 " id="classificationMessage" style="display: none;"></p>
                        </div>
                    </div>
                </div>
                <div class="col-md mt-5" id="SEGMENT-DOCUMENT-STEP" style="display: none;">
                    <div class="card shadow-lg h-100 text-center segment-document"
                        style="background-color:  rgba(52, 58, 64, 0.29);">
                        <div class="loading-cont"
                            style="overflow: hidden; position: absolute; left: 0; right: 0; bottom: 0; top: 0; display: none; justify-content: center; align-items: center; background-color:rgba(115, 124, 133, 0.52); z-index: 2; border-radius: 10px;">
                            <dotlottie-player
                                src="https://lottie.host/4f6b3ace-c7fc-45e9-85a2-c1fe04047ae3/QLPJzOha5m.lottie"
                                background="transparent" speed="1" style="width: 150px; height: 150px;" loop
                                autoplay></dotlottie-player>
                        </div>
                        <div id="previewContainerSegment" class="position-relative"
                            style="min-height: 200px; margin-bottom: 20px; display: flex; justify-content: center; align-items: center;">
                            <button class="prevBtn btn btn-light position-absolute"
                                style="left: 10px; z-index: 10; visibility: hidden;">❮</button>
                            <img class="imagePreview" src="" alt="Preview"
                                style="max-width: 100%; display: none; border: 2px white solid; border-radius: 10px; padding: 10px;">
                            <button class="nextBtn btn btn-light position-absolute"
                                style="right: 10px; z-index: 10; visibility: hidden;">❯</button>

                            <!-- ONLY TEST -->
                            <segment-rect x1="100" y1="10" x2="150" y2="10" x3="150" y3="70" x4="100"
                                y4="70" style="width: 100%; height: 100%; position: absolute; padding: 10px"></segment-rect>
                        </div>
                    </div>
                </div>
            </div>

            <p id="image_name" style="display: none"></p>

            <div class="row justify-content-between mt-4">

            </div>
        </div>
    </div>

    <button class="btn btn-primary" onclick="segmentCipher()">TEST</button>
    <button class="btn btn-warning" onclick="saveCipher()">Save</button>

    <footer class="footer bg-dark text-center text-white py-3">
        © Project Site <a href="https://tptimovyprojekt.ddns.net/" class="text-white">tptimovyprojekt.ddns.net</a>
    </footer>


    <script>
        let currentImageId = []; // Store the image IDs to track unsaved images
        let previewImages = [];
        let classificationScores = [];
        let currentPreviewIndex = 0;
        let numOfFiles = 0;
        function handleFile(file, shouldShow, first) {
            if (file.type.match('image.*')) {
                let reader = new FileReader();
                reader.onload = function (e) {
                    if (currentImageId.length > 0 && first) {
                        console.log("Deleting previous unsaved images...");
                        deleteUnsavedImage(currentImageId);
                    }
                    image_name = file.name.split('.')[0].split(' ').join('_').toLowerCase();
                    previewImages.push([e.target.result, image_name]);
                    console.log("Image uploaded:", previewImages);
                    if (shouldShow) {
                        currentPreviewIndex = previewImages.length - 1;
                        updatePreview();
                    }

                    saveImage(e.target.result, image_name);
                };
                reader.readAsDataURL(file);
            } else {
                handleError('Please upload an image file.');
            }
        }

        function handleDrop(event) {
            event.preventDefault();
            const files = event.dataTransfer.files;
            showLoading();
            numOfFiles = files.length;
            if (files.length > 1) {
                showScrollBtns();
            } else {
                hideScrollBtns();
            }
            for (let i = 0; i < files.length; i++) {
                handleFile(files[i], i === files.length - 1, i === 0);
            }

        }

        function previewImageButton(event) {
            const files = event.target.files;
            showLoading();
            numOfFiles = files.length;
            if (files.length > 1) {
                showScrollBtns();
            } else {
                hideScrollBtns();
            }
            for (let i = 0; i < files.length; i++) {
                handleFile(files[i], i === files.length - 1, i === 0);
            }
        }

        function handleDragOver(event) {
            event.preventDefault();
            document.getElementById('imageUploader').style.border = '2px dashed #007bff';
        }

        function handleDragLeave() {
            document.getElementById('imageUploader').style.border = '#000000 dashed 4px';
        }

        function updatePreview() {
            let imageElements = document.getElementsByClassName('imagePreview');
            for (imageElement of imageElements) {
                if (previewImages.length > 0) {
                    imageElement.src = previewImages[currentPreviewIndex][0];
                    imageElement.style.display = 'block';
                } else {
                    imageElement.style.display = 'none';
                }
            }

        }

        buttons = document.getElementsByClassName('prevBtn');
        for (let button of buttons) {
            button.addEventListener('click', function () {
                if (previewImages.length === 0) return;
                currentPreviewIndex = (currentPreviewIndex - 1 + previewImages.length) % previewImages.length;
                updatePreview();
            });
        }

        buttons = document.getElementsByClassName('nextBtn');
        for (let button of buttons) {
            button.addEventListener('click', function () {
                if (previewImages.length === 0) return;
                currentPreviewIndex = (currentPreviewIndex + 1) % previewImages.length;
                updatePreview();
            });
        }

        function saveData(type) {
            console.log(previewImages);
            if (previewImages.length === 0) {
                handleError('Please upload an image first.');
                return;
            }
            for (let [data, image_name] of previewImages) {
                fetch('movePicture.php', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json'
                    },
                    body: JSON.stringify({
                        data: data,
                        data_name: image_name,
                        user_name: <?php echo json_encode($userData['username']); ?>,
                        type: type, // Use the passed 'type' parameter
                        id: <?php echo json_encode($userData['id']); ?>
                    })
                })
                    .then(response => response.json())
                    .then(data => {
                        if (data.success) {
                            toastr.success(data.message);
                            console.log("Image saved successfully.");
                            currentImageId = null; // Image is saved, no need to delete it
                        } else {
                            handleError(data.error);
                        }
                    });
            }
        }

        // To save keys:
        function saveKey() {
            saveData('KEYS');
        }

        // To save cipher:
        function saveCipher() {
            saveData('CIPHER');
        }

        function saveImage(data, image_name) {
            fetch('savePicture.php', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json'
                },
                body: JSON.stringify({
                    data: data,
                    data_name: image_name,
                    user_name: <?php echo json_encode($userData['username']); ?>,
                    type: 'temp',
                    id: <?php echo json_encode($userData['id']); ?>
                })
            }).then(response => response.json())
                .then(data => {
                    if (data.success) {
                        toastr.success(data.message);

                        currentImageId.push(data.picture_id); // Store the temporary image ID
                        console.log("Image uploaded successfully. ID:", currentImageId);
                        classificationScores.push(classifyPicture(data.path));
                        if (classificationScores.length === numOfFiles && classificationScores.length > 0) {
                            showBtns();
                            applyClassificationStyle(classificationScores);
                        }
                    } else {
                        handleError(data.error);
                    }
                });
        }

        function showBtns() {
            document.getElementById('SaveBtns').style.display = 'flex';
        }

        function deleteUnsavedImage(imageId) {
            console.log("Deleting unsaved images...");
            for (let id of imageId) {
                fetch(`deleteDocument.php?id=${id}&user=${<?php echo json_encode($userData['id']); ?>}`, {
                    method: 'GET'
                }).then(response => {
                    if (response.ok) {
                        console.log("Unsaved image deleted successfully.");
                    } else {
                        console.error("Failed to delete unsaved image.");
                    }
                }).catch(error => {
                    handleError("Error deleting unsaved image:" + error);
                });
            }
            currentImageId = [];
            previewImages = [];
            classificationScores = [];
            currentPreviewIndex = 0;
            updatePreview();
            document.getElementById('SaveBtns').style.display = 'none';
            document.getElementById('classificationMessage').style.display = 'none';
        }

        async function classifyPicture(path) {
            const url = 'https://python.tptimovyprojekt.software/classify';
            console.log("Sending request to Flask server...");

            try {
                const response = await fetch(url, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json'
                    },
                    body: JSON.stringify({ path })  // Sending JSON data
                });

                if (!response.ok) {
                    throw new Error(`HTTP error! Status: ${response.status}`);
                    handleError();
                }

                const data = await response.json();

                if (data.classification) {
                    console.log("Classification:", data.classification);
                    toastr.success(`Classification: ${data.classification}`);
                    return data.classification;
                } else {
                    console.error("Error in classification response.");
                    handleError();
                }
            } catch (error) {
                console.error("Error sending request to Flask server:", error.message);
                handleError();
            }
        }

        function checkToasts() {
            let toast = <?php echo json_encode($_SESSION['toast'] ?? null); ?>;
            if (toast) {
                toastr[toast.type](toast.message);
                <?php unset($_SESSION['toast']); ?>
            }
        }
        function applyClassificationStyle(classification_score) {
            let saveCipherBtn = document.getElementById('saveCipher');
            let saveKeyBtn = document.getElementById('saveKey');
            let messageContainer = document.getElementById('classificationMessage');

            score = 0;
            Promise.all(classificationScores).then(value => {
                for (let i = 0; i < value.length; i++) {
                    score += value[i];
                }
                classification_score = score / classification_score.length;

                // Reset styles
                saveCipherBtn.style.border = "2px solidrgb(0, 0, 0)";
                saveCipherBtn.style.padding = "9px";
                saveKeyBtn.style.border = "2px solidrgb(0, 0, 0)";
                saveKeyBtn.style.padding = "9px";

                if (classification_score > 50) {
                    saveCipherBtn.style.border = "2px solid green";
                    saveCipherBtn.style.padding = "9px";
                    messageContainer.innerHTML = `The classifier thinks the images are ${classification_score}% ciphertexts.`;
                } else {
                    saveKeyBtn.style.border = "2px solid green";
                    saveKeyBtn.style.padding = "9px";
                    messageContainer.innerHTML = `The classifier thinks the images are ${100 - classification_score}% keys.`;
                }
                hideLoading();
                messageContainer.style.display = 'block';
            })
        }

        window.addEventListener("beforeunload", function () {
            console.log("Page is being unloaded..., deleting currentImageId:", currentImageId);

            if (currentImageId) {
                deleteUnsavedImage(currentImageId);
            }
        });

        function setStep(index) {
            const steps = document.querySelectorAll('.step');
            const lines = document.querySelectorAll('.line');

            steps.forEach((step, i) => {
                if (i <= index) {
                    step.classList.add('active');
                } else {
                    step.classList.remove('active');
                }
            });

            lines.forEach((line, i) => {
                if (i < index) {
                    line.style.backgroundColor = '#007bff';
                } else {
                    line.style.backgroundColor = '#212529';
                }
            });
        }

        function showLoading() {
            loadings = document.getElementsByClassName('loading-cont');
            for (let loading of loadings) {
                loading.style.display = 'flex';
            }
        }

        function hideLoading() {
            loadings = document.getElementsByClassName('loading-cont');
            for (let loading of loadings) {
                loading.style.display = 'none';
            }
        }

        function showScrollBtns() {
            buttons = document.getElementsByClassName('prevBtn');
            for (let button of buttons) {
                button.style.visibility = 'visible';
            }
            buttons = document.getElementsByClassName('nextBtn');
            for (let button of buttons) {
                button.style.visibility = 'visible';
            }
        }

        function hideScrollBtns() {
            buttons = document.getElementsByClassName('prevBtn');
            for (let button of buttons) {
                button.style.visibility = 'hidden';
            }
            buttons = document.getElementsByClassName('nextBtn');
            for (let button of buttons) {
                button.style.visibility = 'hidden';
            }
        }

        function handleError(error_message) {
            // If something mega bad happens set everything back to normal
            hideLoading();
            hideScrollBtns();
            document.getElementById('SaveBtns').style.display = 'none';
            document.getElementById('classificationMessage').style.display = 'none';
            previewImages = [];
            currentImageId = [];
            classificationScores = [];
            currentPreviewIndex = 0;
            updatePreview();
            toastr.error(error_message || 'An error occurred. Please try again.');
        }

        function segmentCipher() {
            setStep(1);
            document.getElementById('UPLOAD-DOCUMENT-STEP').style.display = 'none';
            document.getElementById('SEGMENT-DOCUMENT-STEP').style.display = 'block';
            document.getElementById('instruction_message').innerHTML = 'Wait for the system to process the images.<br>If the system made some mistakes, feel free to correct them.';
            // got to #Dashboard
            scrollToDashboard();
            updatePreview();
            CalculateSegmentation();
        }

        function CalculateSegmentation() {
            // TODO: Implement image segmentation
            // For now, just wait 5secs and hide loading, then show the buttons and rect
            showLoading();
            let wait = true;
            setTimeout(() => {
                wait = false;
            }, 5000);
            setInterval(() => {
                if (!wait) {
                    hideLoading();
                }
            }, 1000);
        }

        function scrollEvent() {
            const currentScroll = window.pageYOffset || document.documentElement.scrollTop;
            if (currentScroll > lastScrollTop) {
                // Scrolling down
                navbar.style.top = '-80px';
            } else {
                // Scrolling up
                navbar.style.top = '0';
            }
            lastScrollTop = currentScroll <= 0 ? 0 : currentScroll; // Avoid negative values
        }

        let lastScrollTop = 0;
        const navbar = document.getElementById('navbar');

        window.addEventListener('scroll', scrollEvent);

        function scrollToDashboard() {
            // temporarly turn off scroll event listener
            window.removeEventListener('scroll', scrollEvent);
            document.getElementById('Dashboard').scrollIntoView({ behavior: 'smooth' });
            // turn on scroll event listener
            setTimeout(() => {
                window.addEventListener('scroll', scrollEvent);
            }, 500);
        }

        //segmentCipher();
        hideLoading();

        // index starts at 0
        setStep(0);





        checkToasts();
    </script>


</body>

</html>