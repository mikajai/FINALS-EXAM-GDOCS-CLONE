<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@4.6.2/dist/css/bootstrap.min.css" integrity="sha384-xOolHFLEh07PJGoPkLv1IbcEPTNtaed2xpHsD9ESMhqIYd0nLMwNLD69Npy4HI+N" crossorigin="anonymous">

<nav class="navbar navbar-expand-lg navbar-light" style="background-color: #ffffff;">
  
  <div class="container-fluid">
    
    <a class="navbar-brand d-flex align-items-center text-dark" href="#">

      <!-- gdocs logo -->
      <img src="https://storage.googleapis.com/gweb-uniblog-publish-prod/original_images/Google_Docs.png" 
           alt="Logo" width="40" height="40" class="d-inline-block align-top mr-2">

      <div class="d-flex flex-column">
        <span class="h5 mb-0">Docs</span>
        <small style="font-size: 15px" class="text-muted">You're signed in as: <?php echo ($_SESSION['username']); ?></small>
      </div>

    </a>


    <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarNav" 
            aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
      <span class="navbar-toggler-icon"></span>
    </button>

    
    <div class="collapse navbar-collapse" id="navbarNav">
      <ul class="navbar-nav ml-auto">

        <li class="nav-item">
          <a class="nav-link text-dark" href="core/handleForms.php?logoutUserButton=1">Logout</a>
        </li>

      </ul>
    </div>

  </div>

</nav>
