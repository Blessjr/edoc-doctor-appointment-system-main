<?php
session_start();
include("../connection.php");

if(isset($_SESSION["user"]) && $_SESSION['usertype']=='d'){
    $useremail = $_SESSION["user"];
    $userrow = $database->query("SELECT * FROM doctor WHERE docemail='$useremail'");
    $userfetch = $userrow->fetch_assoc();
    $userid = $userfetch["docid"];
    
    $result = $database->query("
        SELECT COUNT(*) as count FROM notifications 
        WHERE (user_id = '$userid' OR user_type = 'd' OR user_type = 'all') 
        AND is_read = 0
    ");
    
    if ($result) {
        $row = $result->fetch_assoc();
        echo json_encode(['count' => $row['count']]);
    } else {
        echo json_encode(['count' => 0]);
    }
} else {
    echo json_encode(['count' => 0]);
}
?>