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
  <link rel="stylesheet" href="../css/editDocument.css?v=<?php echo time(); ?>" />
</head>

<body class="bg-gradient-to-br from-[#ede1c3] to-[#cdbf9b] text-papyrus min-h-screen flex flex-col select-none">
  <main class="flex-grow container mx-auto px-4 py-10">
    <h1 id="docTitle" class="text-4xl font-bold text-center mb-2"></h1>
    <p class="text-center text-lg mb-8">Edit your document here</p>
    <div class="flex flex-col lg:flex-row gap-10">
      <div class="w-full lg:w-1/2">
        <img id="docImage" src="" alt="Document" class="w-full rounded-lg border shadow-lg" />
      </div>
      <div class="w-full lg:w-1/2 bg-white bg-opacity-50 rounded-xl p-6 shadow-lg">
        <form id="editForm" class="space-y-6 relative min-h-[500px]">
          <input type="hidden" name="id" id="docId">
          <input type="hidden" name="user" id="userId">
          <div>
            <label for="name" class="block font-semibold mb-1">Document Name</label>
            <input type="text" name="name" id="name" class="w-full border border-yellow-400 rounded px-4 py-2" />
          </div>
          <div>
            <label for="share" class="block font-semibold mb-1">Share with</label>
            <div class="flex items-center gap-2 mb-2">
              <input type="text" id="share" placeholder="Enter username" class="flex-grow border border-yellow-400 rounded px-4 py-2" />
              <input type="hidden" name="sharedUsers" id="sharedUsers">
              <button type="button" onclick="addUser()" class="px-4 py-2 bg-yellow-300 text-[#3b2f1d] rounded shadow hover:bg-yellow-400 transition">
                Add
              </button>
            </div>
          </div>
          <table id="sharedUsersTable" class="display w-full text-sm compact">
            <thead>
              <tr>
                <th>Username</th>
                <th>Actions</th>
              </tr>
            </thead>
            <tbody id="sharedUsersTableBody"></tbody>
          </table>
          <div class="absolute bottom-6 right-6">
            <button type="submit" class="bg-green-500 text-white px-6 py-2 rounded hover:bg-green-600 transition">
              Save
            </button>
          </div>
        </form>
      </div>
    </div>
  </main>
  <script>
    let sharedTable;
    let documentId = null;

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
        url: 'https://python.tptimovyprojekt.software/add_shared_users',
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
          console.error(xhr.responseText);
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
        url: 'https://python.tptimovyprojekt.software/remove_shared_users',
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
          console.error(xhr.responseText);
          toastr.error('Failed to remove user');
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
          window.location.href = 'ownKeyDocuments.php';
        } else {
          $('#docId').val(data.document.id);
          $('#userId').val(data.document.author_id);
          $('#name').val(data.document.title);
          $('#docTitle').text(data.document.name);

          if (data.imagePaths && data.imagePaths.length > 0) {
            $('#docImage').attr('src', '../' + data.imagePaths[0]);
          } else {
            $('#docImage').attr('alt', 'No image available');
          }

          fetchSharedUsers(); 
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
              console.error("Unexpected response format:", data);
              response([]);
            }
          });
        },
        minLength: 1
      });

      $('#editForm').on('submit', function (e) {
        e.preventDefault();
        const formData = {
          id: $('#docId').val(),
          user: $('#userId').val(),
          name: $('#name').val()
        };

        $.ajax({
          url: 'https://python.tptimovyprojekt.software/update_document',
          type: 'POST',
          data: formData,
          headers: {
            'Referer': window.location.href
          },
          success: function () {
            toastr.success('Document updated successfully!');
            setTimeout(() => window.location.href = 'ownCipherDipherDocuments.php', 1500);
          },
          error: function (xhr) {
            console.error(xhr.responseText);
            toastr.error('Failed to update document');
          }
        });
      });
      
    });
  </script>

</body>
</html>