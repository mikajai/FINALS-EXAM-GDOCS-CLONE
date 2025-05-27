<?php
require_once 'dbconfig.php';


// function to check if user exists
function checkIfUserAccountExists($pdo, $username) {
    $response = array();
    $sql = "SELECT * FROM user_accounts WHERE username = ?";
    $stmt = $pdo->prepare($sql);

    if ($stmt->execute([$username])) {

        $userInfoArray = $stmt->fetch();

        if ($stmt->rowCount() > 0){
            $response = array(
                "result"=> true,
                "status"=> "200",
                "userInfoArray"=> $userInfoArray
            );
        }
        else {
            $response = array(
                "result"=> false,
                "status"=> "400",
                "message"=> "This user does not exist."
            );
        }
    }
    return $response;
}


// function to insert new user account
function insertNewUserAccount($pdo, $first_name, $last_name, $username, $password) {
    $response = array();
    $checkIfUserAccountExists = checkIfUserAccountExists($pdo, $username);

    if (!$checkIfUserAccountExists['result']) {
        $sql = "INSERT INTO user_accounts (first_name, last_name, username, is_admin, password) VALUES (?, ?, ?, ?, ?)";

        $stmt = $pdo->prepare($sql);

        if ($stmt->execute([$first_name, $last_name, $username, true, $password])) {
            $response = array(
                "status" => "200",
                "message" => "User successfully inserted!"
            );
        }
        else {
            $response = array(
                "status" => "400",
                "message" => "An error occured!"
            );
        }
    }
    else {
        $response = array(
            "status" => "400",
            "message" => "This user already exists!"
        );
    }
    return $response;
}


// function for getting all accounts in the database
function getAllAccounts($pdo) {
    $sql = "SELECT * FROM user_accounts";
    $stmt = $pdo->prepare($sql);
    $stmt->execute();

    return $stmt->fetchAll();
}

// function for suspending or unsuspending user account
function suspendOrUnspendUser($pdo, $suspend_or_unsuspend, $user_id) {
	if ($suspend_or_unsuspend == "Suspend") {
		$sql = "UPDATE user_accounts
                SET is_suspended = '1'
				WHERE user_id = ?";
	}
	if ($suspend_or_unsuspend == "Unsuspend") {
		$sql = "UPDATE user_accounts
                SET is_suspended = '0'
				WHERE user_id = ?";
	}
	
    $stmt = $pdo->prepare($sql);
    return $stmt->execute([$user_id]);
}


// function for checking if user is suspended or not
function checkIfUserSuspended($pdo, $user_id) {
	$sql = "SELECT user_id
            FROM user_accounts
			WHERE is_suspended = '1' 
            AND user_id = ?";
	$stmt = $pdo->prepare($sql);
	$stmt->execute([$user_id]);

	if ($stmt->rowCount() > 0) {
		return true;
	}
}


// function for getting all the documents in the database
function getAllDocuments($pdo) {
    $sql = "SELECT * 
            FROM documents
            ORDER BY updated_at DESC";
    $stmt = $pdo->prepare($sql);
    $stmt->execute();

    return $stmt->fetchAll();
}


// function for getting all the shared documents with the admin 
function getAllSharedDocuments($pdo, $admin_id) {
    $sql = "SELECT d.*, 'shared' AS access_type
            FROM documents d
            INNER JOIN document_permissions dp ON dp.document_id = d.id
            WHERE dp.user_id = ?
            ORDER BY d.updated_at DESC";
    $stmt = $pdo->prepare($sql);
    $stmt->execute([$admin_id]);

    return $stmt->fetchAll();
}


// function for allowing admin to edit shared documents
function canEditDocument($pdo, $user_id, $document_id) {

    // checks if the admin is the owner of the document
    $sql = "SELECT id 
            FROM documents 
            WHERE id = ? 
            AND owner_id = ?";
    $stmt = $pdo->prepare($sql);
    $stmt->execute([$document_id, $user_id]);

    if ($stmt->rowCount() > 0) {
        return true;
    }

    // checks if the admin has an edit permission
    $sql = "SELECT 1
            FROM document_permissions 
            WHERE document_id = ? 
            AND user_id = ? 
            AND can_edit = 1";
    $stmt = $pdo->prepare($sql);
    $stmt->execute([$document_id, $user_id]);

    return $stmt->rowCount() > 0;
}


// function for saving and updating document by the admin
function saveOrUpdateDocument($pdo, $user_id, $document_id, $title, $content) {

    if (canEditDocument($pdo, $user_id, $document_id)) {
        return updateDocument($pdo, $document_id, $title, $content);
    }
    return false;
}


// function for sending messages
function sendAMessage($pdo, $document_id, $user_id, $message) {
    $sql = "INSERT INTO messages (document_id, user_id, message) 
            VALUES (?, ?, ?)";
    $stmt = $pdo->prepare($sql);
    $stmt->execute([$document_id, $user_id, $message]);
}


// function for fetching messages
function fetchMessages($pdo, $document_id) {
    $sql = "SELECT messages.message, 
                messages.created_at, 
                user_accounts.username 
            FROM messages
            JOIN user_accounts 
            ON messages.user_id = user_accounts.user_id
            WHERE messages.document_id = ?
            ORDER BY messages.created_at ASC";
    $stmt = $pdo->prepare($sql);
    $stmt->execute([$document_id]);

    return $stmt->fetchAll();
}


// function for activity logs
function logActivity($pdo, $documentId, $userId, $actionType, $changeSummary) {
    $sql = "INSERT INTO activity_logs (document_id, user_id, action_type, change_summary) 
            VALUES (?, ?, ?, ?)";
    $stmt = $pdo->prepare($sql);
    $stmt->execute([$documentId, $userId, $actionType, $changeSummary]);
}


// function for getting all the activity logs
function getActivityLogs($pdo, $document_id) {
    $sql = "SELECT activity_logs.*, 
                    user_accounts.username 
            FROM activity_logs
            JOIN user_accounts ON activity_logs.user_id = user_accounts.user_id
            WHERE activity_logs.document_id = ?
            ORDER BY activity_logs.timestamp DESC";
    $stmt = $pdo->prepare($sql);
    $stmt->execute([$document_id]);
    
    return $stmt->fetchAll();
}


// function to fetch old content
function getDocumentContent($pdo, $documentId) {
    $sql = "SELECT content 
            FROM documents 
            WHERE id = ?";
    $stmt = $pdo->prepare($sql);
    $stmt->execute([$documentId]);
    return $stmt->fetchColumn();
}

// update document content 
function updateDocumentContent($pdo, $documentId, $newContent) {
    $sql = "UPDATE documents 
            SET content = ?, updated_at = NOW() 
            WHERE id = ?";
    $stmt = $pdo->prepare($sql);
    return $stmt->execute([$newContent, $documentId]);
}


?>