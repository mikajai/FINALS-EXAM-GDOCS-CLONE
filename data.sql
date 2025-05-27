CREATE TABLE user_accounts (
	user_id INT AUTO_INCREMENT PRIMARY KEY,
	first_name VARCHAR(255),
	last_name VARCHAR(255),
	username VARCHAR(255),
    password TEXT,
    is_admin BOOLEAN,
	is_suspended BOOLEAN,
	date_added TIMESTAMP DEFAULT CURRENT_TIMESTAMP 
);

CREATE TABLE documents (
    id INT AUTO_INCREMENT PRIMARY KEY,
    title VARCHAR(255) DEFAULT 'Untitled Document',
    content TEXT,
    owner_id INT,
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
    updated_at DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    admin_can_edit TINYINT(1) DEFAULT 0,
);

CREATE TABLE document_permissions (
    id INT AUTO_INCREMENT PRIMARY KEY,
    document_id INT,
    user_id INT,
    can_edit TINYINT(1) DEFAULT 1,
);

CREATE TABLE messages (
    message_id INT AUTO_INCREMENT PRIMARY KEY,
    document_id INT NOT NULL,
    user_id INT NOT NULL,
    message TEXT NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
);

CREATE TABLE activity_logs (
    logs_id INT AUTO_INCREMENT PRIMARY KEY,
    document_id INT,
    user_id INT,
    action_type VARCHAR(50),
    change_summary TEXT,
    timestamp DATETIME DEFAULT CURRENT_TIMESTAMP,
);
