<?php

require '../checkType.php';

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

check();

// get user id and picture id from url
$userId = $_GET['user'];
$pictureId = $_GET['id'];

if ($userId == null || $pictureId == null) {
    $_SESSION['toast'] = [
        'message' => 'Invalid URL',
        'type' => 'error'
    ];
    header('Location: documents.php');
    exit();
}

if ($userId != $_SESSION['user']['id']) {
    $_SESSION['toast'] = [
        'message' => 'You can only edit your own documents',
        'type' => 'error'
    ];
    header('Location: documents.php');
    exit();
}

require '../config.php';
$conn = getDatabaseConnection();

// get picture from database

$sql = "SELECT * FROM pictures WHERE ID = $pictureId AND creator = $userId";

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

$picture = $result->fetch_assoc();

$stmt->close();
$conn->close();



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
    <link rel="stylesheet" href="https://code.jquery.com/ui/1.13.2/themes/base/jquery-ui.css">
    <link rel="stylesheet" href="../css/editDocument.css">
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/idb/build/iife/index-min.js"></script>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://code.jquery.com/ui/1.13.2/jquery-ui.min.js"></script>

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



        let sharedUsers = [];

        // Add a user to the share list
        function addUser() {
            const inputField = document.getElementById('share');
            const userCapsulesContainer = document.getElementById('userCapsules');
            const username = inputField.value.trim();
            const sharedUsersInput = document.getElementById('sharedUsers');

            if (username && !sharedUsers.includes(username)) {
                sharedUsers.push(username);
                sharedUsersInput.value = sharedUsers.join(','); // Update the hidden input

                // Create a capsule
                const capsule = document.createElement('div');
                capsule.className = 'user-capsule';
                capsule.innerHTML = `${username} <button type="button" class="remove-btn" onclick="removeUser('${username}')">&times;</button>`;

                userCapsulesContainer.appendChild(capsule);
                inputField.value = ''; // Clear the input field
            }
        }

        // Remove a user from the share list
        function removeUser(username) {
            const userCapsulesContainer = document.getElementById('userCapsules');
            const sharedUsersInput = document.getElementById('sharedUsers');
            sharedUsers = sharedUsers.filter(user => user !== username);
            sharedUsersInput.value = sharedUsers.join(','); // Update the hidden input

            // Remove the corresponding capsule
            Array.from(userCapsulesContainer.children).forEach(capsule => {
                if (capsule.textContent == username) {
                    capsule.remove();
                }
            });

            userCapsulesContainer.innerHTML = '';
            sharedUsers.forEach(user => {
                const capsule = document.createElement('div');
                capsule.className = 'user-capsule';
                capsule.innerHTML = `${user} <button type="button" class="remove-btn" onclick="removeUser('${user}')">&times;</button>`;
                userCapsulesContainer.appendChild(capsule);
            });

        }

        // Reset the user list
        function resetUsers() {
            const userCapsulesContainer = document.getElementById('userCapsules');
            const sharedUsersInput = document.getElementById('sharedUsers');
            sharedUsers = [];
            sharedUsersInput.value = '';
            userCapsulesContainer.innerHTML = '';
        }

        

        $(document).ready(function () {
            $("#share").autocomplete({
                source: function (request, response) {
                    $.ajax({
                        url: "fetchUsernames.php",
                        type: "GET",
                        data: {
                            query: request.term, // Term typed in the input
                            picture_id: <?php echo $pictureId; ?> // Picture ID
                        },
                        dataType: "json",
                        success: function (data) {
                            response(data.map(user => user.username)); // Extract usernames
                        },
                        error: function (xhr) {
                            console.error("Error fetching suggestions:", xhr);
                        }
                    });
                },
                minLength: 1, // Start suggesting after typing 1 character
                select: function (event, ui) {
                    $("#share").val(ui.item.value); // Fill the input with the selected value
    
                    return false; // Prevent default behavior
                }
            });
        });




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
        <div class="container mb-5 pt-5">
            <!-- Main title -->
            <div class="row">
                <div class="col-md-12 text-center">
                    <h1 class="display-4 font-weight-bold mb-4"><?php echo $picture['name'] ?></h1>
                </div>
            </div>

            <!-- Welcome message -->
            <div class="row">
                <div class="col-md-12 text-center">
                    <h2>Edit your document here</h2>
                </div>
            </div>

            <!-- Document list -->
            <div class="edit-cont">
                <div class="left">
                    <img src="<?php echo '/' . explode("/", explode("htdocs/", __DIR__)[1])[0] . $picture['path']; ?>"
                        alt="Document">
                </div>
                <div class="form-container">
                    <form action="editDocumentSave.php" method="post" enctype="multipart/form-data">
                        <input type="hidden" name="id" value="<?php echo $picture['ID'] ?>">
                        <input type="hidden" name="user" value="<?php echo $picture['creator'] ?>">

                        <div class="form-group">
                            <label for="name" class="form-label">Document Name</label>
                            <input type="text" class="form-input" id="name" name="name"
                                value="<?php echo $picture['name'] ?>" placeholder="Name of the file">
                        </div>

                        <div class="form-group">
                            <label for="share" class="form-label">Share with</label>
                            <div class="share-zone">
                                <input type="text" id="share" class="form-input" placeholder="Enter username">
                                <input type="hidden" name="sharedUsers" id="sharedUsers">
                                <button type="button" class="btn btn-add" onclick="addUser()">Add</button>
                            </div>
                            <div class="user-capsules" id="userCapsules"></div>
                        </div>

                        <div class="form-actions">
                            <button type="submit" class="btn btn-save">Save</button>
                            <button type="reset" class="btn btn-reset" onclick="resetUsers()">Reset</button>
                        </div>
                    </form>
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