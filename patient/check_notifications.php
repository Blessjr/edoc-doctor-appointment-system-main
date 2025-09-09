<?php
session_start();
include("../connection.php");

if(isset($_SESSION["user"]) && $_SESSION['usertype']=='p'){
    $useremail=$_SESSION["user"];
    
    $sqlmain= "select * from patient where pemail=?";
    $stmt = $database->prepare($sqlmain);
    $stmt->bind_param("s",$useremail);
    $stmt->execute();
    $userrow = $stmt->get_result();
    $userfetch=$userrow->fetch_assoc();
    $userid= $userfetch["pid"];
    
    // Count unread notifications
    $stmt = $database->prepare("
        SELECT COUNT(*) as count FROM notifications 
        WHERE ((user_id = ? AND user_type = 'patient') OR user_type = 'all') 
        AND is_read = 0
    ");
    $stmt->bind_param("i", $userid);
    $stmt->execute();
    $result = $stmt->get_result();
    $count = $result->fetch_assoc()['count'];
    
    echo json_encode(['count' => $count]);
} else {
    echo json_encode(['count' => 0]);
}
?>