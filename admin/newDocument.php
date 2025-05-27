<?php 
require_once 'core/dbconfig.php'; 
require_once 'core/models.php'; 

if (!isset($_SESSION['username']) || $_SESSION['is_admin'] != 1) {
  header("Location: ../user/index.php");
  exit;
}

$doc_id = $_GET['id'] ?? null;

$document = [
  'title' => 'Untitled document',
  'content' => '',
];

if ($doc_id) {
    $stmt = $pdo->prepare("SELECT * FROM documents WHERE id = ?");
    $stmt->execute([$doc_id]);
    $document = $stmt->fetch();

    if (!$document) {
        $document = ['title' => 'Untitled document', 'content' => ''];
    }
}

// if the admin has permission, they can edit the document
$isEditable = $doc_id ? canEditDocument($pdo, $_SESSION['user_id'], $doc_id) : true;
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <title><?php echo htmlspecialchars($document['title']); ?></title>
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@4.6.2/dist/css/bootstrap.min.css">
  <script src="https://code.jquery.com/jquery-3.7.0.min.js"></script>
  <style>
    #document-proper {
      width: 816px;
      height: 1056px;
      background: #fff;
      padding: 96px;
      border: 1px solid #ddd;
      box-shadow: 0 0 10px rgba(0,0,0,0.05);
      outline: none;
    }
    .readonly {
      pointer-events: none;
      background-color: #f9f9f9;
    }
  </style>
</head>
<body class="bg-light">
  <?php include 'include/header.php'; ?>

  <div class="d-flex justify-content-center my-5">
    <div id="document-proper" class="<?php echo $isEditable ? '' : 'readonly'; ?>" contenteditable="<?php echo $isEditable ? 'true' : 'false'; ?>">
      <?php echo htmlspecialchars_decode($document['content']); ?>
    </div>
  </div>

  <?php if ($isEditable): ?>
    <script>
      var documentId = <?php echo $doc_id ? intval($doc_id) : 'null'; ?>;

      $(document).ready(function() {
        var saveTimeout;
        var $titleInput = $('#document-title');
        var $contentDiv = $('#document-proper');

        function saveDocument() {
          var title = $titleInput.val().trim() || 'Untitled Document';
          var content = $contentDiv.html();

          $('#save-status').text('Saving...');

          if (!documentId) {
            $.post('core/handleForms.php', {
              saveDocument: 1,
              title: title,
              content: content
            }, function(response) {
              if (response) {
                documentId = parseInt(response);
                window.history.pushState({}, '', '?id=' + documentId);
              }
              $('#save-status').text('All changes are saved.');
            });
          } else {
            $.post('core/handleForms.php', {
              updateDocumentContent: 1,
              document_id: documentId,
              content: content
            }, function(response) {
              $('#save-status').text('All changes are saved.');
            });
          }
        }

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


        $('#sendMessageBtn').on('click', function() {
          const message = $('#messageInput').val().trim();
          if (message === '') return;

          $.post('core/handleForms.php', {
            sendMessage: 1,
            document_id: documentId,
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
            document_id: documentId
          }, function(response) {
            $('#messageList').html(response);
          });
        }

        
        function fetchActivityLogs() {
          if (!documentId) return;
          $.post('core/handleForms.php', {
            fetchActivityLogs: 1,
            document_id: documentId
          }, function(response) {
            $('#activityLogList').html(response);
          });
        }
        
        $('#activityLogButton').on('click', function() {
          fetchActivityLogs();
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

   

  <?php endif; ?>

</body>
</html>
