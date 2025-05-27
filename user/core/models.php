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

        if ($stmt->execute([$first_name, $last_name, $username, false, $password])) {
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


// function for getting all the documents in the database 
function getAllDocuments($pdo, $user_id) {
    $sql = "SELECT 
            documents.id, 
            documents.title, 
            documents.content, 
            documents.owner_id, 
            documents.created_at, 
            documents.updated_at, 
            'owned' AS access_type
        FROM documents
        WHERE documents.owner_id = ?
        UNION
        SELECT 
            documents.id, 
            documents.title, 
            documents.content, 
            documents.owner_id, 
            documents.created_at, 
            documents.updated_at, 
            'shared' AS access_type
        FROM documents
        INNER JOIN document_permissions 
            ON document_permissions.document_id = documents.id
        WHERE document_permissions.user_id = ?

        ORDER BY updated_at DESC";

    $stmt = $pdo->prepare($sql);
    $stmt->execute([$user_id, $user_id]);
    
    return $stmt->fetchAll();
}


// function for creating a document
function createNewDocument($pdo, $user_id, $title, $content) {
    $sql = "INSERT INTO documents (title, content, owner_id) VALUES (?, ?, ?)";
    $stmt = $pdo->prepare($sql);

    if ($stmt->execute([$title, $content, $user_id])) {
        return $pdo->lastInsertId();
    }
    return false;
}


// function for updating a document
function updateDocument($pdo, $document_id, $title, $content) {
    $sql = "UPDATE documents 
            SET title = ?, content = ?, updated_at = NOW()
            WHERE id = ?";
    $stmt = $pdo->prepare($sql);

    return $stmt->execute([$title, $content, $document_id]);
}


// function for deleting a document
function deleteDocument($pdo, $document_id) {
    $sql = "DELETE FROM documents 
            WHERE id = ?";
    $stmt = $pdo->prepare($sql);

    return $stmt->execute([$document_id]);
}


// function for searching users in the database
function searchingForUsers($pdo, $search, $document_id) {
    $sql = "SELECT user_id, username 
            FROM user_accounts 
            WHERE username LIKE ? 
            AND user_id NOT IN (
                SELECT user_id FROM document_permissions WHERE document_id = ?) 
            LIMIT 10";

    $stmt = $pdo->prepare($sql);
    $stmt->execute(["%$search%", $document_id]);

    return $stmt->fetchAll();
}


// function that permits an access to documents
function grantingDocumentAccess($pdo, $document_id, $user_id) {
    $sql = "INSERT IGNORE INTO document_permissions (document_id, user_id, can_edit) 
            VALUES (?, ?, 1)";
    $stmt = $pdo->prepare($sql);
    $stmt->execute([$document_id, $user_id]);
}


// function for getting all the users where the document is shared to
function getSharedUsers($pdo, $document_id) {
    $sql = "SELECT username 
            FROM user_accounts
            JOIN document_permissions 
            ON user_accounts.user_id = document_permissions.user_id
            WHERE document_id = ?";
    $stmt = $pdo->prepare($sql);
    $stmt->execute([$document_id]);

    return $stmt->fetchAll();
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


?>
