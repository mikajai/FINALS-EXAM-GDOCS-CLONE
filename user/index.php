<?php 
require_once 'core/dbconfig.php'; 
require_once 'core/models.php'; 

if (!isset($_SESSION['username'])) {
  header("Location: login.php");
}

if ($_SESSION['is_admin'] == 1) {
  header("Location: ../admin/index.php");
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Google Docs Clone</title>

  <!-- bootstrap CSS -->
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@4.6.2/dist/css/bootstrap.min.css">
  
  <!-- jQuery -->
  <script src="https://code.jquery.com/jquery-3.7.0.js"></script>
</head>
<body class="bg-light">

  <?php include 'include/navbar.php'; ?>

  <!-- starting/creating a new document -->
  <div class="container mt-5 px-5">
    <p>Start a new document</p>

    <a href="newDocument.php">
      <img src="https://ssl.gstatic.com/docs/templates/thumbnails/docs-blank-googlecolors.png" alt="New Document" width="140" height="190" class="border">
    </a>

  </div>

  
  <div class="container p-5">

    <h5 class="mb-4">All documents</h5>
    <?php
      $getAllDocuments = getAllDocuments($pdo, $_SESSION['user_id']);
      $owned = [];
      $shared = [];

      foreach ($getAllDocuments as $row) {
        if ($row['access_type'] === 'owned') {
          $owned[] = $row;
        } 
        else {
          $shared[] = $row;
        }
      }
    ?>

    <!-- showing all the documents of the user -->
    <?php if (!empty($owned)) { ?>
      <h6 class="mt-4">My Documents</h6>
      <div class="row">
        <?php foreach ($owned as $row) { ?>
          <div class="col-md-3 mb-4">
            <div class="card shadow-sm h-100">
              <a href="newDocument.php?id=<?php echo $row['id']; ?>" style="text-decoration: none; color: inherit;">
                <div class="document-preview p-4" style="height: 190px; overflow: hidden; border-bottom: 1px solid #eee;">
                  <?php echo ($row['content']); ?>
                </div>
                <div class="card-body">
                  <h6 class="card-title text-truncate"><?php echo ($row['title']) ?: 'Untitled document'; ?></h6>
                  <p class="card-text text-muted small">
                    <img width="25" height="25" src="https://storage.googleapis.com/gweb-uniblog-publish-prod/original_images/Google_Docs.png" alt="">
                    Modified <?php echo date("M j, Y", strtotime($row['updated_at'])); ?>
                  </p>
                </div>
              </a>
              <form method="POST" action="core/handleForms.php" onsubmit="return confirm('Delete this document?');" class="px-3 pb-3">
                <input type="hidden" class="document_id" name="document_id" value="<?php echo $row['id']; ?>">
                <button type="button" name="deleteDocument" class="deleteButton btn-danger btn-sm btn-block">Delete</button>
              </form>
            </div>
          </div>
        <?php } ?>
      </div>
    <?php } ?>

    <!-- showing all the shared documents with the user -->
    <?php if (!empty($shared)) { ?>
      <h6 class="mt-5">Shared With Me</h6>
      <div class="row">
        <?php foreach ($shared as $row) { ?>
          <div class="col-md-3 mb-4">
            <div class="card shadow-sm h-100">
              <a href="newDocument.php?id=<?php echo $row['id']; ?>" style="text-decoration: none; color: inherit;">
                <div class="document-preview p-4" style="height: 190px; overflow: hidden; border-bottom: 1px solid #eee;">
                  <?php echo ($row['content']); ?>
                </div>
                <div class="card-body">
                  <h6 class="card-title text-truncate"><?php echo ($row['title']) ?: 'Untitled document'; ?></h6>
                  <p class="card-text text-muted small">
                    <img width="25" height="25" src="https://storage.googleapis.com/gweb-uniblog-publish-prod/original_images/Google_Docs.png" alt="">
                    Modified <?php echo date("M j, Y", strtotime($row['updated_at'])); ?>
                  </p>
                </div>
              </a>
            </div>
          </div>
        <?php } ?>
      </div>
    <?php } ?>
  </div>


  <!-- javascripts -->
  <script>

    $('.deleteButton').on('click', function (event) {
      event.preventDefault();
      var card = $(this).closest('.card');
      var document_id = card.find('.document_id').val();

      $.ajax({
        type: 'POST',
        url: 'core/handleForms.php',
        data: {
          document_id: document_id,
          deleteDocument: 1
        },
        success: function (data) {
          console.log(data);
          card.fadeOut(300, function () {
            setTimeout(function () {
              window.location.reload();
            }, 300);
          });
        }
      });
    });
    
  </script>

</body>
</html>
