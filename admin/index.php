<?php 
require_once 'core/dbconfig.php';
require_once 'core/models.php'; 

if (!isset($_SESSION['username'])) {
  header("Location: login.php");
  exit;
}

if ($_SESSION['is_admin'] == 0) {
  header("Location: ../user/index.php");
  exit;
}

$allDocuments = getAllDocuments($pdo);
$sharedWithAdmin = getAllSharedDocuments($pdo, $_SESSION['user_id']);
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Admin Dashboard</title>

  <!-- bootstrap CSS -->
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@4.6.2/dist/css/bootstrap.min.css">
  
  <!-- jQuery -->
  <script src="https://code.jquery.com/jquery-3.7.0.js"></script>

</head>
<body class="bg-light">

  <?php include 'include/navbar.php'; ?>

  <div class="container p-5">


    <!-- displaying all the documents shared with the admin -->
    <?php if (!empty($sharedWithAdmin)) { ?>
      <h5 class="mb-4">Documents Shared With You</h5>
      <div class="row">
        <?php foreach ($sharedWithAdmin as $row) { ?>
          <div class="col-md-3 mb-4">
            <div class="card shadow-sm h-100">
              <a href="newDocument.php?id=<?php echo $row['id']; ?>" style="text-decoration: none; color: inherit;">
                <div class="document-preview p-4" style="height: 190px; overflow: hidden; border-bottom: 1px solid #eee;">
                  <?php echo ($row['content']); ?>
                </div>
                <div class="card-body">
                  <h6 class="card-title text-truncate"><?php echo ($row['title']) ?: 'Untitled document'; ?></h6>
                  <p class="card-text text-muted small">
                    Modified <?php echo date("M j, Y", strtotime($row['updated_at'])); ?>
                  </p>
                </div>
              </a>
            </div>
          </div>
        <?php } ?>
      </div>
    <?php } ?>

    <!-- displaying all the documents in the database -->
    <h5 class="mb-4">All Documents</h5>
    <div class="row">
      <?php foreach ($allDocuments as $row) { ?>
        <div class="col-md-3 mb-4">
          <div class="card shadow-sm h-100">
            <a href="newDocument.php?id=<?php echo $row['id']; ?>" style="text-decoration: none; color: inherit;">
              <div class="document-preview p-4" style="height: 190px; overflow: hidden; border-bottom: 1px solid #eee;">
                <?php echo ($row['content']); ?>
              </div>
              <div class="card-body">
                <h6 class="card-title text-truncate"><?php echo ($row['title']) ?: 'Untitled document'; ?></h6>
                <p class="card-text text-muted small">
                  Modified <?php echo date("M j, Y", strtotime($row['updated_at'])); ?>
                </p>
              </div>
            </a>
          </div>
        </div>
      <?php } ?>
    </div>

    
  </div>
</body>
</html>
