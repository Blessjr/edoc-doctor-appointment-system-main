CREATE TABLE IF NOT EXISTS chat_logs (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_email VARCHAR(255),
    user_role ENUM('doctor', 'patient', 'admin', 'guest') DEFAULT 'guest',
    message TEXT NOT NULL,
    response TEXT NOT NULL,
    timestamp TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    INDEX idx_user_email (user_email),
    INDEX idx_timestamp (timestamp)
);
