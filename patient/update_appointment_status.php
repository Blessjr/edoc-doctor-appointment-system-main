<?php
session_start();
include("../connection.php");

// Set timezone to Cameroon
date_default_timezone_set('Africa/Douala');

if(isset($_GET['appoid']) && isset($_GET['status'])) {
    $appoid = $_GET['appoid'];
    $status = $_GET['status'];
    
    // Update appointment status
    $sql = "UPDATE appointment SET status = ? WHERE appoid = ?";
    $stmt = $database->prepare($sql);
    $stmt->bind_param("si", $status, $appoid);
    
    if($stmt->execute()) {
        echo json_encode(['success' => true]);
    } else {
        echo json_encode(['success' => false, 'error' => $database->error]);
    }
} else {
    echo json_encode(['success' => false, 'error' => 'Missing parameters']);
}
?>