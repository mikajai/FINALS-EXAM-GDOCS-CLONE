<?php
require_once 'dbconfig.php';
require_once 'models.php';


// handles registering to the system
if (isset($_POST['insertNewUserAccountButton'])) {
    $firstName = trim($_POST['firstNameInput']);
    $lastName = trim($_POST['lastNameInput']);
    $username = trim($_POST['usernameInput']);
    $password = trim($_POST['passwordInput']);
    $confirmPassword = trim($_POST['confirmPasswordInput']);

    if (!empty($firstName) && !empty($lastName) && !empty($username) && !empty($password) && !empty($confirmPassword)) {

        if ($password == $confirmPassword) {
            $insertQuery = insertNewUserAccount($pdo, $firstName, $lastName, $username, password_hash($password, PASSWORD_DEFAULT));

            if ($insertQuery['status'] == '200') {
				$_SESSION['message'] = $insertQuery['message'];
				$_SESSION['status'] = $insertQuery['status'];
				header("Location: ../login.php");
			}
			else {
				$_SESSION['message'] = $insertQuery['message'];
				$_SESSION['status'] = $insertQuery['status'];
				header("Location: ../register.php");
			}
        }
        else {
            $_SESSION['message'] = "Please make sure both passwords are equal.";
			$_SESSION['status'] = '400';
			header("Location: ../register.php");
        }
    }
    else {
        $_SESSION['message'] = "Please make sure there are no empty input fields.";
			$_SESSION['status'] = '400';
			header("Location: ../register.php");
    }
}


// handles logging in to the system
if (isset($_POST['loginUserAccountButton'])) {
    $username = trim($_POST['usernameInput']);
    $password = trim($_POST['passwordInput']);

    if (!empty($username) && !empty($password)) {
        
        $loginQuery = checkIfUserAccountExists($pdo, $username);

        if ($loginQuery['result']) {
            $user = $loginQuery['userInfoArray'];
            $userIdFromDB = $user['user_id'];
            $usernameFromDB = $user['username'];
            $passwordFromDB = $user['password'];
            $isAdminStatusFromDB = $user['is_admin'];
            $isSuspended = $user['is_suspended'];

            if ($isSuspended == '1') {
                $_SESSION['message'] = "Your account has been suspended. Contact an administrator.";
                $_SESSION['status'] = "403";
                header("Location: ../login.php");
                exit();
            }

            if (password_verify($password, $passwordFromDB)) {
                $_SESSION['user_id'] = $userIdFromDB;
                $_SESSION['username'] = $usernameFromDB;
                $_SESSION['is_admin'] = $isAdminStatusFromDB;
                header("Location: ../index.php");
                exit();
            } else {
                $_SESSION['message'] = "Your username/password is invalid.";
                $_SESSION['status'] = "400";
                header("Location: ../login.php");
                exit();
            }
        }
        else {
            $_SESSION['message'] = $loginQuery['message'];
            $_SESSION['status'] = "400";
            header("Location: ../login.php");
        }
    } 
    else {
        $_SESSION['message'] = "Please make sure there are no empty input fields.";
        $_SESSION['status'] = '400';
        header("Location: ../login.php");
    }
}


if (isset($_GET['logoutUserButton'])) {
	unset($_SESSION['username']);
	header("Location: ../login.php");
}


// handles suspending or unsuspending user account
if (isset($_POST['suspendOrUnspendUser'])) {
	$suspend_or_unsuspend = $_POST['suspend_or_unsuspend'];
	$user_id = $_POST['user_id'];

	suspendOrUnspendUser($pdo, $suspend_or_unsuspend, $user_id);
}


// handles updating the document content
if (isset($_POST['updateDocumentContent'])) {
    $document_id = intval($_POST['document_id']);
    $userId = $_SESSION['user_id'];
    $newContent = $_POST['content'];

    // helps in fetching old content
    $sql = "SELECT content FROM documents WHERE id = ?";
    $stmtOld = $pdo->prepare($sql);
    $stmtOld->execute([$document_id]);
    $old = $stmtOld->fetch();

     // updates content of the document
    $sql = "UPDATE documents SET content = ?, updated_at = NOW() WHERE id = ?";
    $stmt = $pdo->prepare($sql);
    $stmt->execute([$newContent, $document_id]);


    $logSummary = "<br><strong>Content changed from: </strong>" . substr(strip_tags($old['content']), 0, 100) .
                  "<br><strong>to: </strong>" . substr(strip_tags($newContent), 0, 100);

    logActivity($pdo, $document_id, $userId, 'Content Updated', $logSummary);

    echo 'success';
    exit;
}



// handles sending a message
if (isset($_POST['sendMessage'])) {
    $user_id = $_SESSION['user_id'];
    $document_id = $_POST['document_id'];
    $message = trim($_POST['message']);

    sendAMessage($pdo, $document_id, $user_id, $message);
}


// handles fetching messages
if (isset($_POST['fetchMessages'])) {
    $document_id = $_POST['document_id'];
    
    $messages = fetchMessages($pdo, $document_id);

    foreach ($messages as $msg) {
        echo '<div class="border rounded p-2 mb-2">';
        echo '<strong>' . htmlspecialchars($msg['username']) . '</strong> <small class="text-muted">' . $msg['created_at'] . '</small><br>';
        echo nl2br(htmlspecialchars($msg['message']));
        echo '</div>';
    }
}


// Fetch activity logs for a document
if (isset($_POST['fetchActivityLogs'])) {
    $document_id = $_POST['document_id'];

    $logs = getActivityLogs($pdo, $document_id);

    foreach ($logs as $log) {
        echo '<li class="list-group-item">';
        echo '<strong>' . htmlspecialchars($log['username']) . '</strong>: ';
        echo htmlspecialchars($log['action_type']) . ' ';
        echo $log['change_summary'] . '<br>';
        echo '<small class="text-muted">' . $log['timestamp'] . '</small>';
        echo '</li>';
    }

    exit;
}


?>