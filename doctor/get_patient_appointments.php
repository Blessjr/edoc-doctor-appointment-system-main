<?php
session_start();
include("../connection.php");

if(!isset($_SESSION["user"]) || $_SESSION["usertype"] != 'd'){
    header('HTTP/1.1 403 Forbidden');
    echo json_encode(['error' => 'Access denied']);
    exit();
}

if(isset($_GET['patient_id']) && isset($_GET['doctor_id'])) {
    $patient_id = $_GET['patient_id'];
    $doctor_id = $_GET['doctor_id'];
    
    $appointment_result = $database->query("
        SELECT a.*, s.title 
        FROM appointment a 
        LEFT JOIN schedule s ON a.scheduleid = s.scheduleid 
        WHERE a.pid = '$patient_id' AND a.docid = '$doctor_id'
        ORDER BY a.appodate DESC
    ");
    
    $appointments = [];
    if($appointment_result){
        while($row = $appointment_result->fetch_assoc()){
            $appointments[] = $row;
        }
    }
    
    header('Content-Type: application/json');
    echo json_encode($appointments);
    exit();
} else {
    header('HTTP/1.1 400 Bad Request');
    echo json_encode(['error' => 'Missing parameters']);
    exit();
}
?>