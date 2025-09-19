<?php

    session_start();

    if(isset($_SESSION["user"])){
        if(($_SESSION["user"])=="" or $_SESSION['usertype']!='p'){
            header("location: ../login.php");
        }

    }else{
        header("location: ../login.php");
    }
    
    
    if($_GET){
        //import database
        include("../connection.php");
        $id=$_GET["id"];
        
        // Simple delete query (make sure $id is properly validated/sanitized)
        $sql = "DELETE FROM appointment WHERE appoid = '$id'";
        $result = $database->query($sql);
        
        if($result){
            header("location: appointment.php");
            exit();
        } else {
            echo "Error deleting appointment: " . $database->error;
            exit();
        }
    }

?>