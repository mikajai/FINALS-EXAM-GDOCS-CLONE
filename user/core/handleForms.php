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
                $_SESSION['status'] = "400";
                header("Location: ../login.php");
                exit();
            }

            if (password_verify($password, $passwordFromDB)) {
                $_SESSION['user_id'] = $userIdFromDB;
                $_SESSION['username'] = $usernameFromDB;
                $_SESSION['is_admin'] = $isAdminStatusFromDB;
                header("Location: ../index.php");
            } else {
                $_SESSION['message'] = "Your username/password is invalid.";
                $_SESSION['status'] = "400";
                header("Location: ../login.php");   
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


// handles creating a document and saving it
if (isset($_POST['saveDocument'])) {
    $title = $_POST['title'] ?? 'Untitled Document';
    $content = $_POST['content'] ?? '';
    $user_id = $_SESSION['user_id'];

    if ($user_id && !empty($title)) {
        $insertedId = createNewDocument($pdo, $user_id, $title, $content);
        if ($insertedId) {

            $summary = "<strong>New document titled: </strong>" . htmlspecialchars($title) . " created.";
            logActivity($pdo, $insertedId, $user_id, 'Document Created', $summary);

            echo $insertedId;
        }
    }
}



// handles updating the document
if (isset($_POST['updateDocumentContent'])) {
    $document_id = $_POST['document_id'];
    $userId = $_SESSION['user_id'];
    $newContent = $_POST['content'];
    $newTitle = $_POST['title'] ?? '';

    // helps in fetching old title and content
    $sql = "SELECT title, content FROM documents WHERE id = ?";
    $stmtOld = $pdo->prepare($sql);
    $stmtOld->execute([$document_id]);
    $old = $stmtOld->fetch();

    // updates the title and content of the document
    $sql = "UPDATE documents SET title = ?, content = ?, updated_at = NOW() WHERE id = ?";
    $stmt = $pdo->prepare($sql);
    $stmt->execute([$newTitle, $newContent, $document_id]);


    // log summary for changes
    $logSummary = [];

    if ($old['title'] !== $newTitle) {
        $logSummary[] = "<br><strong>Title changed from: </strong>{$old['title']} <br><strong>to: </strong>{$newTitle}";
    }

    if ($old['content'] !== $newContent) {
        $logSummary[] = "<br><strong>Content changed from: </strong>" . substr(strip_tags($old['content']), 0, 100) .
                        "<br><strong> to: </strong>" . substr(strip_tags($newContent), 0, 100) . "";
    }

    if (!empty($logSummary)) {
        logActivity($pdo, $document_id, $userId, 'Document Updated', implode('. ', $logSummary));
    }

    echo 'success';
    exit;
}



// handles deleting a document
if (isset($_POST['deleteDocument'])) {
    $document_id = intval($_POST['document_id']);
    $user_id = $_SESSION['user_id'];

    if ($document_id && $user_id) {
       deleteDocument($pdo, $document_id);
    }
}


// handles searching a user under the share button
if (isset($_POST['searchUsers'])) {
    $search = $_POST['query'];
    $docId = $_POST['document_id'];

    $users = searchingForUsers($pdo, $search, $docId);

    foreach ($users as $user) {
        echo '<li class="list-group-item d-flex justify-content-between align-items-center">'
             . htmlspecialchars($user['username']) .
             '<button onclick="grantAccess(' . (int)$user['user_id'] . ')" class="btn btn-sm btn-primary">Share</button></li>';
    }
}


// handles permitting an access for another user
if (isset($_POST['grantAccess'])) {
    $user_id = $_POST['user_id'];
    $document_id = $_POST['document_id'];

    grantingDocumentAccess($pdo, $document_id, $user_id);
}


// handles getting the users a document has been shared to
if (isset($_POST['getSharedUsers'])) {
    $document_id = $_POST['document_id'];

    $sharedUsers = getSharedUsers($pdo, $document_id);

    foreach ($sharedUsers as $user) {
        echo '<li class="list-group-item d-flex justify-content-between align-items-center text-muted">'
           . htmlspecialchars($user['username']) .
           '<span class="badge badge-secondary">Document Shared</span></li>';
    }
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
        echo '<strong>' . ($msg['username']) . '</strong> <small class="text-muted">' . $msg['created_at'] . '</small><br>';
        echo nl2br(($msg['message']));
        echo '</div>';
    }
}


// fetching activity logs
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