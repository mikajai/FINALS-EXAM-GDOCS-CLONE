<?php
require_once 'core/models.php';
require_once 'core/handleForms.php';
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Register Page</title>

    <!-- jquery -->
    <script src="https://code.jquery.com/jquery-3.7.0.js"></script>

    <!-- link to login.css -->
    <link rel="stylesheet" href="register.css">
</head>
<body>

    <div class="formProper">

        <div class="header">
            <img src="https://storage.googleapis.com/gweb-uniblog-publish-prod/original_images/Google_Docs.png" alt="">
            <h1>Register to Google Docs Clone | ADMIN</h1> 
        </div>
        
        <form id="registrationForm" method="POST" action="core/handleForms.php">
            <div class="basicInfo">
                <p><label for="inputField">First Name: </label><input type="text" id="firstNameInput"  name="firstNameInput"></p>
                <p><label for="inputField">Last Name: </label><input type="text" id="lastNameInput"  name="lastNameInput"></p>
            </div>
            
            <p><label for="inputField">Username: </label><input type="text" id="usernameInput" name="usernameInput"></p>
            <div class="basicInfo">
                <p><label for="inputField">Password: </label><input type="password" id="passwordInput" name="passwordInput"></p>
                <p><label for="inputField">Confirm Password: </label><input type="password" id="confirmPasswordInput" name="confirmPasswordInput"></p>  
            </div>
            <input type="submit" value="Register" name="insertNewUserAccountButton">
        </form>


        <div class="displayMessage">
            <?php 
                if (isset($_SESSION['message']) && isset($_SESSION['status'])) {

                    if ($_SESSION['status'] == "200") {
                        echo "<h1 style='margin-top: 28px; color: #399918; font-size: 18px;'>{$_SESSION['message']}</h1>";
                    } 
                    else {
                        echo "<h1 style='margin-top: 28px; color: #7D0A0A; font-size: 18px;'>{$_SESSION['message']}</h1>";
                    }
                }
                unset($_SESSION['message']);
                unset($_SESSION['status']);
            ?>
        </div>
    </div>
        
</body>
</html>