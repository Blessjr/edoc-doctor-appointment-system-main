<?php
session_start();
include("connection.php");

header('Content-Type: application/json');

// Get user ID and type from request
$user_id = isset($_GET['user_id']) ? intval($_GET['user_id']) : 0;
$user_type = isset($_GET['user_type']) ? $_GET['user_type'] : '';

if ($user_id > 0 && in_array($user_type, ['patient', 'doctor', 'admin'])) {
    // Fetch notifications from database
    $stmt = $database->prepare("SELECT * FROM notifications WHERE user_id = ? AND user_type = ? ORDER BY created_at DESC");
    $stmt->bind_param("is", $user_id, $user_type);
    $stmt->execute();
    $result = $stmt->get_result();
    
    $notifications = [];
    while($row = $result->fetch_assoc()) {
        $notifications[] = $row;
    }
    
    echo json_encode($notifications);
    
    $stmt->close();
} else {
    echo json_encode(['error' => 'Invalid user ID or type']);
}

$database->close();
?>