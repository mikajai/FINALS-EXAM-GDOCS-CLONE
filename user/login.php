<?php
require_once 'core/models.php';
require_once 'core/handleForms.php';
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Log In Page</title>

    <!-- jquery -->
    <script src="https://code.jquery.com/jquery-3.7.0.js"></script>

    <!-- link to login.css -->
    <link rel="stylesheet" href="login.css">
</head>
<body>

    <div  class="formProper">

        <div class="header">
            <img src="https://storage.googleapis.com/gweb-uniblog-publish-prod/original_images/Google_Docs.png" alt="">
            <h1>Login to Google Docs Clone</h1> 
        </div>

        <form id="loginForm" method="POST" action="core/handleForms.php">
            <p><label for="inputField">Username: </label><input type="text" id="usernameInput" name="usernameInput"></p>
            <p><label for="inputField">Password: </label><input type="password" id="passwordInput" name="passwordInput"></p>
            <input type="submit" value="Log In" name="loginUserAccountButton">
        </form>

        <p>No account yet? <a href="register.php">Register here</a></p>

        <div class="displayMessage">
            <?php 
                if (isset($_SESSION['message']) && isset($_SESSION['status'])) {    

                    if ($_SESSION['status'] == "200") {
                        echo "<h1 style='margin-top: 28px; color: #367E18; font-size: 20px;'>{$_SESSION['message']}</h1>";
                    } 
                    else {
                        echo "<h1 style='margin-top: 28px; color: #7D0A0A; font-size: 20px;'>{$_SESSION['message']}</h1>";
                    }
                }
                unset($_SESSION['message']);
                unset($_SESSION['status']);
            ?>
        </div>

    </div>
    
</body>
</html>