<?php
session_start();
include("connection.php");

header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $input = json_decode(file_get_contents('php://input'), true);
    $notification_ids = isset($input['notification_ids']) ? $input['notification_ids'] : [];
    
    if (!empty($notification_ids)) {
        // Create placeholders for the prepared statement
        $placeholders = implode(',', array_fill(0, count($notification_ids), '?'));
        
        // Update the notifications as read
        $stmt = $database->prepare("UPDATE notifications SET is_read = 1, read_at = NOW() WHERE notification_id IN ($placeholders)");
        
        // Bind parameters
        $types = str_repeat('i', count($notification_ids));
        $stmt->bind_param($types, ...$notification_ids);
        
        if ($stmt->execute()) {
            echo json_encode(['success' => true]);
        } else {
            echo json_encode(['success' => false, 'message' => 'Database update failed']);
        }
        
        $stmt->close();
    } else {
        echo json_encode(['success' => false, 'message' => 'No notification IDs provided']);
    }
} else {
    echo json_encode(['success' => false, 'message' => 'Invalid request method']);
}

$database->close();
?>