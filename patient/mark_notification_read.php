<?php
session_start();
include("connection.php");

header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $input = json_decode(file_get_contents('php://input'), true);
    $notification_id = isset($input['notification_id']) ? intval($input['notification_id']) : 0;
    
    if ($notification_id > 0) {
        // Update the notification as read
        $stmt = $database->prepare("UPDATE notifications SET is_read = 1, read_at = NOW() WHERE notification_id = ?");
        $stmt->bind_param("i", $notification_id);
        
        if ($stmt->execute()) {
            echo json_encode(['success' => true]);
        } else {
            echo json_encode(['success' => false, 'message' => 'Database update failed']);
        }
        
        $stmt->close();
    } else {
        echo json_encode(['success' => false, 'message' => 'Invalid notification ID']);
    }
} else {
    echo json_encode(['success' => false, 'message' => 'Invalid request method']);
}

$database->close();
?>