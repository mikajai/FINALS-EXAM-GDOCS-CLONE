<?php 
require_once 'core/dbconfig.php'; 
require_once 'core/models.php'; 

if (!isset($_SESSION['username'])) {
  header("Location: login.php");
}

if ($_SESSION['is_admin'] == 1) {
  header("Location: ../admin/index.php");
}

// document default 
$document = [
  'title' => 'Untitled document',
  'content' => '',
];

// to view the documents
$document_id = $_GET['id'] ?? null;

if ($document_id) {
    $sql = "SELECT * 
            FROM documents 
            WHERE id = ?";
    $stmt = $pdo->prepare($sql);
    $stmt->execute([$document_id]);
    $document = $stmt->fetch();

    if (!$document) {
        $document = ['title' => 'Untitled document', 'content' => ''];
    }
}

?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title><?php echo htmlspecialchars($document['title'] ?? 'Untitled document'); ?></title>

  <!-- Bootstrap CSS -->
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@4.6.2/dist/css/bootstrap.min.css">

  <!-- jQuery -->
  <script src="https://code.jquery.com/jquery-3.7.0.js"></script>

  <style>
    body {
      background-color: #f5f5f5;
      padding-bottom: 40px;
    }

    #editor-wrapper {
      display: flex;
      justify-content: center;
      padding-top: 30px;
    }

    #document-proper {
      width: 816px;
      height: 1056px;
      background: #ffffff;
      border: 1px solid #ddd;
      padding: 96px;
      outline: none;
      box-shadow: 0 0 10px rgba(0,0,0,0.05);
    }
  </style>
</head>
<body>
  <?php include 'include/header.php'; ?>

  <!-- Editor Container -->
  <div id="editor-wrapper">
    <div id="document-proper" contenteditable="true">
      <?php echo htmlspecialchars_decode($document['content'] ?? '', ENT_QUOTES); ?>
    </div>
  </div>


  <!-- javascript -->

  <script>
  var document_id = <?php echo $document_id ? intval($document_id) : 'null'; ?>;

  $(document).ready(function() {
    var saveTimeout;
    var $titleInput = $('#document-title');
    var $contentDiv = $('#document-proper');

    // saving a document
    function saveDocument() {
      var title = $titleInput.val().trim() || 'Untitled Document';
      var content = $contentDiv.html();

      $('#save-status').text('Saving...');

      if (!document_id) {
        $.post('core/handleForms.php', {
          saveDocument: 1,
          title: title,
          content: content
        }, function(response) {
          if (response) {
            document_id = parseInt(response);
            window.history.pushState({}, '', '?id=' + document_id);
          }
          $('#save-status').text('All changes are saved.');
        });
      } else {
        $.post('core/handleForms.php', {
          updateDocumentContent: 1,
          document_id: document_id,
          title: title,
          content: content
        }, function(response) {
          $('#save-status').text('All changes are saved.');
        });
      }
    }


    // auto-save feature
    $titleInput.on('input', function() {
      clearTimeout(saveTimeout);
      $('#save-status').text('Saving...');
      saveTimeout = setTimeout(saveDocument, 3000);
    });

    $contentDiv.on('input', function() {
      clearTimeout(saveTimeout);
      $('#save-status').text('Saving...');
      saveTimeout = setTimeout(saveDocument, 3000);
    });


    // event listener when share button is clicked
    $('#shareButton').on('click', function() {
      $('#shareModal').modal('show');
      $('#userSearchInput').val('');
      $('#userSearchResults').empty();

      // Load shared users
      $.post('core/handleForms.php', {
        getSharedUsers: 1,
        document_id: documentId
      }, function(data) {
        $('#sharedUsersList').html(data);
      });
    });


    // event listener when an input is set
    $('#userSearchInput').on('input', function() {
      var query = $(this).val().trim();
      if (query.length < 2) return;

      $.post('core/handleForms.php', {
        searchUsers: 1,
        query: query,
        document_id: document_id 
      }, function(data) {
        $('#userSearchResults').html(data);
      });
    });

    window.grantAccess = function(userId) {
      $.post('core/handleForms.php', {
        grantAccess: 1,
        user_id: userId,
        document_id: document_id
      }, function() {
        $.post('core/handleForms.php', {
          getSharedUsers: 1,
          document_id: document_id
        }, function(data) {
          $('#sharedUsersList').html(data);
        });
      });
    };

    function fetchSharedUsers(documentId) {
      $.post("handleForms.php", {
          getSharedUsers: true,
          document_id: document_id 
      }, function(data) {
          $("#sharedUserList").html(data);
      });
    }

  

    $('#sendMessageBtn').on('click', function() {
      const message = $('#messageInput').val().trim();
      if (message === '') return;

      $.post('core/handleForms.php', {
        sendMessage: 1,
        document_id: document_id,
        message: message
      }, function() {
        $('#messageInput').val('');
        fetchMessages();
      });
    });

    $('#messageModal').on('shown.bs.modal', function () {
      fetchMessages();
    });
  


    function fetchMessages() {
      $.post('core/handleForms.php', {
        fetchMessages: 1,
        document_id: document_id
      }, function(response) {
        $('#messageList').html(response);
      });
    }


    $('#activityLogButton').on('click', function() {
      if (!document_id) return;

      $.post('core/handleForms.php', {
        fetchActivityLogs: 1,
        document_id: document_id
      }, function(response) {
        $('#activityLogList').html(response);
        $('#activityLogModal').modal('show');
      });
    });



  });

  function textAppearBold() {
    document.execCommand('bold');
  }

  function textAppearItalic() {
    document.execCommand('italic');
  }

  function textAppearUnderlined() {
    document.execCommand('underline');
  }

  function textAppearH1() {
    document.execCommand('formatBlock', false, 'h1');
  }
  
  function textAppearP() {
    document.execCommand('formatBlock', false, 'p');
  }


  </script>

</body>
</html>