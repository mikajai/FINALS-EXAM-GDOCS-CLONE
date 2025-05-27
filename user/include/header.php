<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@4.6.2/dist/css/bootstrap.min.css" integrity="sha384-xOolHFLEh07PJGoPkLv1IbcEPTNtaed2xpHsD9ESMhqIYd0nLMwNLD69Npy4HI+N" crossorigin="anonymous">

<style>
  .toolbar-btn {
    font-size: 0.9rem;
    padding: 2px 8px;
    min-width: 36px;
    height: 32px;
  }
</style>


<nav class="navbar navbar-light shadow-sm flex-column px-2 pt-3" style="background-color: #f8f9fa; font-family: Arial, sans-serif;">


  <div class="container-fluid d-flex align-items-center justify-content-between w-100">
    
    <div class="d-flex align-items-center">

      <!-- gdocs logo -->
      <a class="navbar-brand d-flex align-items-center text-dark mb-0 mr-2" href="index.php">
        <img src="https://storage.googleapis.com/gweb-uniblog-publish-prod/original_images/Google_Docs.png" 
             alt="Logo" width="40" height="40">
      </a>


      <!-- title and signed in indicator -->
      <div class="d-flex flex-column">

        <div class="d-flex align-items-center">
          <input id="document-title" type="text" 
                class="form border-0 bg-transparent font-weight-semibold mr-2" 
                style="font-size: 1.3rem; width: 240px;" 
                value="<?php echo htmlspecialchars($document['title'] ?? 'Untitled document'); ?>"
          />

          <!-- save status indicator beside title -->
          <span id="save-status" class="text-muted" style="white-space: nowrap; font-size: 12px;"></span>
        </div>

        <small class="text-muted ml-1" style="font-size: 0.8rem;">You're signed in as: <?php echo htmlspecialchars($_SESSION['username']); ?></small>

      </div>

    </div>

    
    <div class="d-flex align-items-center">

      <!-- activity logs button -->
      <button id="activityLogButton" class="btn btn-sm ml-2" data-toggle="modal" data-target="#activityLogModal">
        <img src="https://cdn-icons-png.flaticon.com/128/4991/4991413.png" alt="activity log" width="22" height="22">
      </button>

      <!-- message button -->
      <button class="btn btn-sm mx-2" data-toggle="modal" data-target="#messageModal">
        <img src="https://cdn-icons-png.flaticon.com/128/542/542638.png" alt="message" width="25" height="25">
      </button>

      <!-- share button -->
      <button id="shareButton" class="btn btn-primary btn-sm mx-2">Share</button>

    </div>

  </div>

  <!-- toolbar buttons section -->
  <div class="w-100 d-flex justify-content-center mt-2">

    <div class="d-flex align-items-center">
      <button class="btn btn-sm mx-1 toolbar-btn" onclick="textAppearH1()">Heading</button>
      <button class="btn btn-sm mx-1 toolbar-btn" onclick="textAppearP()">paragraph</button>
      <button class="btn btn-sm mx-1 toolbar-btn font-weight-bold" onclick="textAppearBold()">B</button>
      <button class="btn btn-sm mx-1 toolbar-btn font-italic" onclick="textAppearItalic()">I</button>
      <button class="btn btn-sm mx-1 toolbar-btn" style="text-decoration: underline;" onclick="textAppearUnderlined()">U</button>
      <button id="uploadImageButton" class="btn btn-sm mx-1 mb-1 toolbar-btn" style="text-decoration: underline;" onclick="uploadAnImage()"><img src="https://cdn-icons-png.flaticon.com/128/44/44289.png" alt="image" width="15" height="15"></button>
    </div>

  </div>

  <!-- modal when share button is clicked -->
  <div class="modal fade" id="shareModal" tabindex="-1" role="dialog">
    <div class="modal-dialog modal-dialog-centered" role="document">
      <div class="modal-content p-3">

        <div class="modal-header">
          <h5 class="modal-title">Share Document</h5>
          <button type="button" class="close" data-dismiss="modal" aria-label="Close">
            <span>&times;</span>
          </button>
        </div>

        <div class="modal-body">
          <h6>Users with Access</h6>
          <ul id="sharedUsersList" class="list-group mb-3"></ul>

          <input type="text" id="userSearchInput" class="form-control mb-3" placeholder="Search users...">

          <h6>Search Results</h6>
          <ul id="userSearchResults" class="list-group"></ul>
        </div>

      </div>
    </div>
  </div>


  <!-- modal when message button is clicked -->
  <div class="modal fade" id="messageModal" tabindex="-1" role="dialog" aria-labelledby="messageModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-scrollable" role="document">
      <div class="modal-content">

        <div class="modal-header">
          <h5 class="modal-title">Document Chat</h5>
          <button type="button" class="close" data-dismiss="modal" aria-label="Close">
            <span>x</span>
          </button>
        </div>

        <div class="modal-body">
          <div id="messageList" style="max-height: 300px; overflow-y: auto;"></div>
          <textarea id="messageInput" class="form-control mt-3" placeholder="Type a message..."></textarea>
          <button id="sendMessageBtn" class="btn btn-primary btn-sm mt-2 float-right">Send</button>
        </div>

      </div>
    </div>
  </div>


 
  <!-- Activity Log Modal -->
  <div class="modal fade" id="activityLogModal" tabindex="-1" role="dialog" aria-labelledby="activityLogModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-scrollable" role="document">
      <div class="modal-content">

        <div class="modal-header">
          <h5 class="modal-title">Activity Logs</h5>
          <button type="button" class="close" data-dismiss="modal" aria-label="Close">
            <span>x</span>
          </button>
        </div>

        <div class="modal-body">
          <ul id="activityLogList" class="list-group">
            <!-- Activity logs will be dynamically loaded here -->
          </ul>
        </div>

      </div>
    </div>
  </div>



  
</nav> 

<!-- script for using modal -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@4.6.2/dist/js/bootstrap.bundle.min.js"></script>


