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

$fullCallerUrl = (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? 'https' : 'http') .
                 '://' . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI'];

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
  <link rel="stylesheet" href="https://cdn.datatables.net/1.13.6/css/jquery.dataTables.min.css">
  <script src="https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script>
  <script src="https://cdn.jsdelivr.net/npm/flowbite@1.6.5/dist/flowbite.min.js"></script>
  <link rel="stylesheet" href="../css/editDocument.css?v=<?php echo time(); ?>" />
</head>

<body class="bg-gradient-to-br from-[#ede1c3] to-[#cdbf9b] text-papyrus min-h-screen flex flex-col select-none">
  <!-- Navbar -->
  <nav class="sticky top-0 z-50 w-full transition-all duration-300 bg-[#d7c7a5] border-b border-yellow-300 shadow-md not-copyable not-draggable"
    id="navbar">
    <div class="container mx-auto flex flex-wrap items-center justify-between py-3 px-4">
        <a href="main.php"
            class="flex items-center text-papyrus text-2xl font-bold hover:underline animate-slide-left">
            <img src="../img/logo.png" alt="Logo" class="w-10 h-10 mr-3"
                style="filter: filter: brightness(0) saturate(100%) invert(15%) sepia(56%) saturate(366%) hue-rotate(357deg) brightness(98%) contrast(93%);">
            HandScript
        </a>
        <button class="lg:hidden text-papyrus focus:outline-none" id="navbarToggle">
            <svg class="w-6 h-6" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"
                stroke-linecap="round" stroke-linejoin="round">
                <path d="M4 6h16M4 12h16M4 18h16" />
            </svg>
        </button>
        <div class="w-full lg:flex lg:items-center lg:w-auto hidden mt-4 lg:mt-0" id="navbarNav">
            <ul class="flex flex-col lg:flex-row w-full text-lg font-medium text-papyrus animate-slide-right">
                <li class="flex items-center">
                    <a href="profile.php" class="nav-link flex items-center hover:underline">
                        Profile
                        <img src="../img/account.png" alt="profile" class="w-6 h-6 ml-2"
                            style="filter: brightness(0) saturate(100%) invert(15%) sepia(56%) saturate(366%) hue-rotate(357deg) brightness(98%) contrast(93%);">
                    </a>
                </li>
                <li class="flex items-center ml-6">
                    <div class="relative flex items-center">
                        <button id="dropdownDocumentsButton" data-dropdown-toggle="dropdownDocuments"
                            class="hover:underline flex items-center">
                            Documents
                            <img src="../img/document.png" alt="document" class="w-6 h-6 ml-2"
                                style="filter: brightness(0) saturate(100%) invert(15%) sepia(56%) saturate(366%) hue-rotate(357deg) brightness(98%) contrast(93%);">
                            <svg class="w-2.5 h-2.5 ml-2.5" aria-hidden="true" xmlns="http://www.w3.org/2000/svg"
                                fill="none" viewBox="0 0 10 6">
                                <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round"
                                    stroke-width="2" d="M1 1l4 4 4-4" />
                            </svg>
                        </button>
                        <div id="dropdownDocuments"
                            class="z-10 hidden font-normal bg-[#d7c7a5] divide-y divide-gray-100 rounded-lg shadow w-44 absolute top-full mt-2">
                            <ul class="py-2 text-sm text-[#3b2f1d]" aria-labelledby="dropdownDocumentsButton">
                                <li>
                                    <a href="ownKeyDocuments.php" class="block px-4 py-2 hover:bg-[#cbbd99]">Key
                                        Documents</a>
                                </li>
                                <li>
                                    <a href="ownCipherDocuments.php"
                                        class="block px-4 py-2 hover:bg-[#cbbd99]">Cipher Documents</a>
                                </li>
                            </ul>
                        </div>
                    </div>
                </li>
                <li class="flex items-center ml-6">
                    <div class="relative flex items-center">
                        <button id="dropdownToolsButton" data-dropdown-toggle="dropdownTools"
                            class="hover:underline flex items-center">
                            Tools
                            <img src="../img/tools.png" alt="tools" class="w-6 h-6 ml-2"
                                style="filter: brightness(0) saturate(100%) invert(15%) sepia(56%) saturate(366%) hue-rotate(357deg) brightness(98%) contrast(93%);">
                            <svg class="w-2.5 h-2.5 ml-2.5" aria-hidden="true" xmlns="http://www.w3.org/2000/svg"
                                fill="none" viewBox="0 0 10 6">
                                <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round"
                                    stroke-width="2" d="M1 1l4 4 4-4" />
                            </svg>
                        </button>
                        <div id="dropdownTools"
                            class="z-10 hidden font-normal bg-[#d7c7a5] divide-y divide-gray-100 rounded-lg shadow w-44 absolute top-full mt-2">
                            <ul class="py-2 text-sm text-[#3b2f1d]" aria-labelledby="dropdownToolsButton">
                                <li>
                                    <a href="./modules/segmentModule.php" class="block px-4 py-2 hover:bg-[#cbbd99]">Segment</a>
                                </li>
                                <li>
                                    <a href="./modules/analyzeModule.php" class="block px-4 py-2 hover:bg-[#cbbd99]">Analyze</a>
                                </li>
                                <li>
                                    <a href="./modules/lettersModule.php" class="block px-4 py-2 hover:bg-[#cbbd99]">Letters</a>
                                </li>
                            </ul>
                        </div>
                    </div>
                </li>
                <li class="flex items-center ml-6">
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
    <h1 id="docTitle" class="text-4xl font-bold text-center mb-10">Edit Your Document Here</h1>
    
    <div class="flex flex-col lg:flex-row gap-10 items-stretch">
      
      <!-- Left side: Image container -->
      <div class="w-full lg:w-1/2 flex">
        <div class="w-full">
          <img id="docImage" src="" alt="Document" class="w-full h-full object-cover rounded-lg border shadow-lg" />
        </div>
      </div>

      <!-- Right side: Form container -->
      <div class="w-full lg:w-1/2 flex">
        <div class="w-full bg-white bg-opacity-50 rounded-xl p-6 shadow-lg mt-4 lg:mt-0">
          <input type="hidden" name="id" id="docId">
          <input type="hidden" name="user" id="userId">
          
          <!-- Document Name -->
          <div>
            <label for="name" class="block font-semibold mb-1">Document Name</label>
            <div class="flex items-center gap-2 mb-2">
              <input type="text" name="name" id="name" placeholder="Enter document name" class="flex-grow border border-yellow-400 rounded px-4 py-2" />
              <button type="button" onclick="changeName()" class="px-4 py-2 bg-yellow-300 text-[#3b2f1d] rounded shadow hover:bg-yellow-400 transition">
                Change
              </button>
            </div>
          </div>

          <!-- Share with section -->
          <div class="mt-4 mb-8">
            <label for="share" class="block font-semibold mb-1">Share with</label>
            <div class="flex flex-col lg:flex-row gap-4 mb-2">
              <div class="lg:w-5/6 w-full">
                <div class="flex flex-col sm:flex-row gap-2">
                  <input type="text" id="share" placeholder="Enter username"
                    class="flex-grow border border-yellow-400 rounded px-4 py-2" />
                  <button type="button" onclick="addUser()"
                    class="px-4 py-2 bg-yellow-300 text-[#3b2f1d] rounded shadow hover:bg-yellow-400 transition">
                    Add
                  </button>
                </div>
                <input type="hidden" name="sharedUsers" id="sharedUsers">
              </div>
              <div class="lg:w-1/6 w-full flex items-center justify-center">
                <label for="isPublic" class="flex items-center cursor-pointer select-none">
                  <div class="relative">
                    <input type="checkbox" id="isPublic" class="sr-only peer">
                    <div class="w-12 h-6 bg-gray-300 rounded-full peer-checked:bg-yellow-400 transition-colors duration-300"></div>
                    <div class="absolute top-0.5 left-0.5 w-5 h-5 bg-white rounded-full transition-transform duration-300 peer-checked:translate-x-6"></div>
                  </div>
                  <span class="ml-3 text-sm font-medium text-gray-700">Public</span>
                </label>
              </div>
            </div>
          </div>

          <!-- Shared users table -->
          <table id="sharedUsersTable" class="display w-full text-sm compact">
            <thead>
              <tr>
                <th>Username</th>
                <th>Actions</th>
              </tr>
            </thead>
            <tbody id="sharedUsersTableBody"></tbody>
          </table>
        </div>
      </div>

    </div>
    <div class="mt-6">
      <label for="jsonData" class="block font-semibold mb-2">Key JSON</label>
      <textarea id="jsonData" rows="12" class="w-full border border-yellow-400 rounded p-4 text-sm font-mono bg-white bg-opacity-70 resize-y"></textarea>
    </div>
    <button onclick="saveKeyJson()" class="mt-4 px-4 py-2 bg-green-500 hover:bg-green-600 text-white rounded shadow">
      Save JSON
    </button>


  </main>
  <!-- Footer -->
  <footer class="bg-[#d7c7a5] text-center text-papyrus py-4 mt-10 border-t border-yellow-300">
    &copy; 2025 HandScript – <a href="https://tptimovyprojekt.ddns.net/" class="underline">Visit Project Page</a>
  </footer>

  <script>
    let sharedTable;
    let documentId = null;

    function fetchKeyJson() {
      const formData = {
        document_id: documentId,
        user_id: $('#userId').val(),
        token: '<?php echo $_SESSION['token']; ?>'
      };

      $.ajax({
        url: 'https://python.tptimovyprojekt.software/get_key_json',
        type: 'POST',
        data: JSON.stringify(formData),
        contentType: 'application/json',
        dataType: 'json',
        success: function (res) {
          if (res.error) {
            toastr.error(res.error || 'Failed to load key JSON');
          } else {
            $('#jsonData').val(JSON.stringify(res, null, 2));
          }
        },
        error: function () {
          toastr.error('Server error while fetching key JSON');
        }
      });
    }

    function fetchSharedUsers() {
      sharedTable.clear();
      $.get(`users/getSharedUsers.php?id=${documentId}`, function (data) {
        if (Array.isArray(data)) {
          data.forEach(username => {
            sharedTable.row.add([
              username,
              `<button type="button" onclick="removeUser('${username}')" class="text-red-500">Remove</button>`
            ]);
          });
          sharedTable.draw();
        } else {
          toastr.error('Failed to load shared users');
        }
      });
    }

    function changeName() {
      const newTitle = $('#name').val().trim();
      const documentId = $('#docId').val();
      const authorId = $('#userId').val();
      const callerUrl = "<?php echo $fullCallerUrl; ?>";

      if (!newTitle) {
        toastr.error('Please enter a valid document name');
        return;
      }

      const formData = {
        document_id: documentId,
        author_id: authorId,
        new_title: newTitle,
        token: '<?php echo $_SESSION['token']; ?>'
      };

      $.ajax({
        url: 'https://python.tptimovyprojekt.software/update_document_title',
        type: 'POST',
        data: JSON.stringify(formData),
        contentType: 'application/json',
        dataType: 'json',
        headers: {
          'X-Caller-Url': callerUrl
        },
        success: function (res) {
          if (res.success) {
            toastr.success('Document name updated');
          } else {
            toastr.error(res.error || 'Failed to update name');
          }
        },
        error: function () {
          toastr.error('Server error while updating document name');
        }
      });
    }


    function addUser() {
      const username = $('#share').val().trim();
      if (!username) {
        toastr.error('Please enter a valid username');
        return;
      }

      const formData = {
        document_id: documentId,
        username: username,
        token: '<?php echo $_SESSION['token']; ?>'
      };

      $.ajax({
        url: 'https://python.tptimovyprojekt.software/add_shared_user',
        type: 'POST',
        data: JSON.stringify(formData),           
        contentType: 'application/json',         
        dataType: 'json',                        
        success: function (res) {
          if (res.success) {
            toastr.success('User added');
            $('#share').val('');
            fetchSharedUsers();
          } else {
            toastr.error(res.error || 'Failed to add user');
          }
        },
        error: function (xhr) {
          toastr.error('Failed to add user');
        }
      });
    }


    function removeUser(username) {
      const formData = {
        document_id: documentId,
        username: username,
        token: '<?php echo $_SESSION['token']; ?>'
      };

      $.ajax({
        url: 'https://python.tptimovyprojekt.software/remove_shared_user',
        type: 'POST',
        data: JSON.stringify(formData),      
        contentType: 'application/json',     
        dataType: 'json',                   
        success: function (res) {
          if (res.success) {
            toastr.success('User removed');
            fetchSharedUsers();
          } else {
            toastr.error(res.error || 'Failed to remove user');
          }
        },
        error: function (xhr) {
          toastr.error('Failed to remove user');
        }
      });
    }

    function saveKeyJson() {
      let parsedJson;
      try {
        parsedJson = JSON.parse($('#jsonData').val());
      } catch (e) {
        toastr.error('Invalid JSON format');
        return;
      }

      const formData = {
        document_id: documentId,
        user_id: $('#userId').val(),
        token: '<?php echo $_SESSION['token']; ?>',
        json_data: parsedJson  // 🟢 this key matches your Flask backend exactly
      };

      $.ajax({
        url: 'https://python.tptimovyprojekt.software/save_key_json',
        type: 'POST',
        data: JSON.stringify(formData),
        contentType: 'application/json',
        dataType: 'json',
        success: function (res) {
          if (res.success) {
            toastr.success('Key JSON saved successfully');
          } else {
            toastr.error(res.error || 'Failed to save key JSON');
          }
        },
        error: function () {
          toastr.error('Server error while saving key JSON');
        }
      });
    }


    $(document).ready(function () {
      const urlParams = new URLSearchParams(window.location.search);
      const userId = urlParams.get('user');
      documentId = urlParams.get('id');

      sharedTable = $('#sharedUsersTable').DataTable({
        pagingType: "simple",
        lengthChange: false,
        pageLength: 5,
        searching: true,
        info: false,
        autoWidth: false,
        columnDefs: [{ targets: [1], orderable: false }],
        dom: '<"top"f>rt<"bottom"p><"clear">',
        language: {
          search: "",
          searchPlaceholder: "Filter users..."
        }
      });

      $.get(`documents/getDocument.php?user=${userId}&id=${documentId}`, function (data) {
        if (data.error) {
          toastr.error(data.error);
        } else {
          $('#docId').val(data.document.id);
          $('#userId').val(data.document.author_id);
          $('#name').val(data.document.title);
          $('#docTitle').text(data.document.name);
          $('#isPublic').prop('checked', data.document.is_public);

          if (data.imagePaths && data.imagePaths.length > 0) {
            $('#docImage').attr('src', '../' + data.imagePaths[0]);
          } else {
            $('#docImage').attr('alt', 'No image available');
          }

          fetchSharedUsers(); 
          fetchKeyJson();
        }
      });

      $("#share").autocomplete({
        source: function (request, response) {
          $.get("users/fetchUsernames.php", {
            query: request.term,
            picture_id: documentId
          }, function (data) {
            if (Array.isArray(data)) {
              response(data.map(user => user.username));
            } else if (data.error) {
              toastr.error(data.error);
              response([]);
            } else {
              response([]);
            }
          });
        },
        minLength: 1
      });
      $('.ui-helper-hidden-accessible').remove();

      $('#isPublic').on('change', function () {
        const isPublic = $(this).is(':checked');

        const formData = {
          document_id: documentId,
          author_id: $('#userId').val(),
          is_public: isPublic,
          token: '<?php echo $_SESSION['token']; ?>'
        };

        $.ajax({
          url: 'https://python.tptimovyprojekt.software/update_doc_public',
          type: 'POST',
          data: JSON.stringify(formData),
          contentType: 'application/json',
          dataType: 'json',
          success: function (res) {
            if (res.success) {
              toastr.success(`Document is now ${isPublic ? 'Public' : 'Private'}`);
            } else {
              toastr.error(res.error || 'Update failed');
            }
          },
          error: function () {
            toastr.error('Server error while updating visibility');
          }
        });
      });

      
    });
  </script>

</body>
</html>