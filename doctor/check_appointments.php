<?php
// check_appointments.php
session_start();
include("../connection.php");

// Enable error reporting for debugging
error_reporting(E_ALL);
ini_set('display_errors', 1);

header('Content-Type: application/json');

if(isset($_SESSION["user"]) && $_SESSION['usertype']=='d'){
    $useremail = $_SESSION["user"];
    
    // Get doctor ID
    $userrow = $database->query("SELECT * FROM doctor WHERE docemail='$useremail'");
    if ($userrow && $userrow->num_rows > 0) {
        $userfetch = $userrow->fetch_assoc();
        $userid = $userfetch["docid"];
        
        $today = date('Y-m-d');
        $current_time = date('H:i:s');
        $thirty_min_later = date('H:i:s', strtotime('+30 minutes'));
        
        $notification_query = $database->query("
            SELECT a.*, p.pname 
            FROM appointment a 
            INNER JOIN patient p ON a.pid = p.pid 
            WHERE a.docid = '$userid' 
            AND a.appodate = '$today' 
            AND a.appotime BETWEEN '$current_time' AND '$thirty_min_later'
            AND a.status != 'Completed'
        ");
        
        if ($notification_query) {
            $notifications = array();
            while ($row = $notification_query->fetch_assoc()) {
                $notifications[] = $row;
            }
            
            echo json_encode([
                'success' => true,
                'appointments' => $notifications
            ]);
        } else {
            echo json_encode([
                'success' => false,
                'message' => 'Query failed: ' . $database->error
            ]);
        }
    } else {
        echo json_encode([
            'success' => false,
            'message' => 'Doctor not found'
        ]);
    }
} else {
    echo json_encode([
        'success' => false,
        'message' => 'Not authorized'
    ]);
}
?>