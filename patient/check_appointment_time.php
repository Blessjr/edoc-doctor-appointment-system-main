<?php
session_start();
include("../connection.php");

// Set timezone to Cameroon
date_default_timezone_set('Africa/Douala');
$currentDateTime = new DateTime();
$currentDate = $currentDateTime->format('Y-m-d');
$currentTime = $currentDateTime->format('H:i:s');

if(isset($_SESSION["user"]) && $_SESSION['usertype'] == 'p') {
    $useremail = $_SESSION["user"];
    
    // Get user ID
    $sqlmain = "SELECT pid FROM patient WHERE pemail = ?";
    $stmt = $database->prepare($sqlmain);
    $stmt->bind_param("s", $useremail);
    $stmt->execute();
    $userrow = $stmt->get_result();
    $userfetch = $userrow->fetch_assoc();
    $userid = $userfetch["pid"];
    
    // Check for active appointments
    $sql = "SELECT appointment.appoid, appointment.start_time, appointment.end_time, schedule.scheduledate 
            FROM appointment 
            INNER JOIN schedule ON appointment.scheduleid = schedule.scheduleid 
            WHERE appointment.pid = ? AND appointment.status = 'scheduled' AND schedule.scheduledate = ?";
    
    $stmt = $database->prepare($sql);
    $stmt->bind_param("is", $userid, $currentDate);
    $stmt->execute();
    $result = $stmt->get_result();
    
    $activeAppointment = false;
    
    while($row = $result->fetch_assoc()) {
        $appointmentStart = DateTime::createFromFormat('Y-m-d H:i:s', $currentDate . ' ' . $row['start_time']);
        $earlyStart = clone $appointmentStart;
        $earlyStart->modify('-5 minutes');
        
        if ($currentDateTime >= $earlyStart) {
            $activeAppointment = true;
            break;
        }
    }
    
    echo json_encode(['active' => $activeAppointment]);
} else {
    echo json_encode(['active' => false]);
}
?>