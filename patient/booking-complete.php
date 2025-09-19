<?php
session_start();
include("../connection.php");

if(isset($_SESSION["user"])){
    if(($_SESSION["user"])=="" or $_SESSION['usertype']!='p'){
        header("location: ../login.php");
    } else {
        $useremail=$_SESSION["user"];
    }
} else {
    header("location: ../login.php");
}

// Get user ID
$sqlmain= "select * from patient where pemail=?";
$stmt = $database->prepare($sqlmain);
$stmt->bind_param("s",$useremail);
$stmt->execute();
$userrow = $stmt->get_result();
$userfetch=$userrow->fetch_assoc();
$userid= $userfetch["pid"];

if($_POST){
    $scheduleid=$_POST["scheduleid"];
    $apponum=$_POST["apponum"];
    $appodate=$_POST["date"];
    $start_time=$_POST["start_time"];
    $end_time=$_POST["end_time"];
    
    // Insert appointment with calculated times
    $sql="INSERT INTO appointment (pid, apponum, scheduleid, appodate, start_time, end_time, status) VALUES (?,?,?,?,?,?,'scheduled')";
    $stmt = $database->prepare($sql);
    $stmt->bind_param("iiisss", $userid, $apponum, $scheduleid, $appodate, $start_time, $end_time);
    
    if($stmt->execute()){
        $appoid = $database->insert_id;
        header("location: appointment.php?action=booking-added&id=$appoid");
    } else {
        header("location: appointment.php?action=booking-failed");
    }
} else {
    header("location: schedule.php");
}
?>