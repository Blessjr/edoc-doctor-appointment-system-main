<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="../css/animations.css">  
    <link rel="stylesheet" href="../css/main.css">  
    <link rel="stylesheet" href="../css/admin.css">
        
    <title>Patients</title>
    <style>
        .popup{
            animation: transitionIn-Y-bottom 0.5s;
        }
        .sub-table{
            animation: transitionIn-Y-bottom 0.5s;
        }
    </style>
</head>
<body>
<?php
session_start();

if(isset($_SESSION["user"])){
    if(($_SESSION["user"])=="" or $_SESSION['usertype']!='d'){
        header("location: ../login.php");
    }else{
        $useremail=$_SESSION["user"];
    }
}else{
    header("location: ../login.php");
}

// Importer la base de données
include("../connection.php");
$sqlmain= "select * from doctor where docemail=?";
$stmt = $database->prepare($sqlmain);
$stmt->bind_param("s",$useremail);
$stmt->execute();
$userrow = $stmt->get_result();
$userfetch=$userrow->fetch_assoc();
$userid= $userfetch["docid"];
$username=$userfetch["docname"];
?>
<div class="container">
    <div class="menu">
        <table class="menu-container" border="0">
            <tr>
                <td style="padding:10px" colspan="2">
                    <table border="0" class="profile-container">
                        <tr>
                            <td width="30%" style="padding-left:20px" >
                                <img src="../img/user.png" alt="" width="100%" style="border-radius:50%">
                            </td>
                            <td style="padding:0px;margin:0px;">
                                <p class="profile-title"><?php echo substr($username,0,13)  ?>..</p>
                                <p class="profile-subtitle"><?php echo substr($useremail,0,22)  ?></p>
                            </td>
                        </tr>
                        <tr>
                            <td colspan="2">
                                <a href="../logout.php"><input type="button" value="Déconnexion" class="logout-btn btn-primary-soft btn"></a>
                            </td>
                        </tr>
                    </table>
                </td>
            </tr>
            <tr class="menu-row" >
                <td class="menu-btn menu-icon-dashbord" >
                    <a href="index.php" class="non-style-link-menu"><div><p class="menu-text">Tableau de bord</p></div></a>
                </td>
            </tr>
            <tr class="menu-row">
                <td class="menu-btn menu-icon-appoinment">
                    <a href="appointment.php" class="non-style-link-menu"><div><p class="menu-text">Mes rendez-vous</p></div></a>
                </td>
            </tr>
            <tr class="menu-row" >
                <td class="menu-btn menu-icon-session">
                    <a href="schedule.php" class="non-style-link-menu"><div><p class="menu-text">Mes séances</p></div></a>
                </td>
            </tr>
            <tr class="menu-row" >
                <td class="menu-btn menu-icon-patient menu-active menu-icon-patient-active">
                    <a href="patient.php" class="non-style-link-menu non-style-link-menu-active"><div><p class="menu-text">Mes patients</p></div></a>
                </td>
            </tr>
            <tr class="menu-row" >
                <td class="menu-btn menu-icon-settings">
                    <a href="settings.php" class="non-style-link-menu"><div><p class="menu-text">Paramètres</p></div></a>
                </td>
            </tr>
        </table>
    </div>

<?php
$selecttype="Mes";
$current="Mes patients seulement";

if($_POST){
    if(isset($_POST["search"])){
        $keyword=$_POST["search12"];
        $sqlmain= "select * from patient where pemail='$keyword' or pname='$keyword' or pname like '$keyword%' or pname like '%$keyword' or pname like '%$keyword%'";
        $selecttype="Mes";
    }
    
    if(isset($_POST["filter"])){
        if($_POST["showonly"]=='all'){
            $sqlmain= "select * from patient";
            $selecttype="Tous";
            $current="Tous les patients";
        }else{
            $sqlmain= "select * from appointment inner join patient on patient.pid=appointment.pid inner join schedule on schedule.scheduleid=appointment.scheduleid where schedule.docid=$userid;";
            $selecttype="Mes";
            $current="Mes patients seulement";
        }
    }
}else{
    $sqlmain= "select * from appointment inner join patient on patient.pid=appointment.pid inner join schedule on schedule.scheduleid=appointment.scheduleid where schedule.docid=$userid;";
    $selecttype="Mes";
}
?>

<div class="dash-body">
    <table border="0" width="100%" style="border-spacing:0;margin:0;padding:0;margin-top:25px;">
        <tr>
            <td width="13%">
                <a href="patient.php"><button class="login-btn btn-primary-soft btn btn-icon-back" style="padding:11px 0;margin-left:20px;width:125px">Retour</button></a>
            </td>
            <td>
                <form action="" method="post" class="header-search">
                    <input type="search" name="search12" class="input-text header-searchbar" placeholder="Rechercher un patient par nom ou email" list="patient">&nbsp;&nbsp;
                    <?php
                        echo '<datalist id="patient">';
                        $list11 = $database->query($sqlmain);
                        for ($y=0;$y<$list11->num_rows;$y++){
                            $row00=$list11->fetch_assoc();
                            $d=$row00["pname"];
                            $c=$row00["pemail"];
                            echo "<option value='$d'><br/>";
                            echo "<option value='$c'><br/>";
                        };
                        echo '</datalist>';
                    ?>
                    <input type="submit" value="Rechercher" name="search" class="login-btn btn-primary btn" style="padding:10px 25px;">
                </form>
            </td>
            <td width="15%">
                <p style="font-size: 14px;color: rgb(119, 119, 119);text-align:right;">Date du jour</p>
                <p class="heading-sub12" style="padding:0;margin:0;">
                    <?php date_default_timezone_set('Africa/Douala'); echo date('Y-m-d'); ?>
                </p>
            </td>
            <td width="10%">
                <button class="btn-label" style="display:flex;justify-content:center;align-items:center;"><img src="../img/calendar.svg" width="100%"></button>
            </td>
        </tr>
        <tr>
            <td colspan="4" style="padding-top:10px;">
                <p class="heading-main12" style="margin-left:45px;font-size:18px;color:rgb(49,49,49)">
                    <?php echo $selecttype." Patients (".$list11->num_rows.")"; ?>
                </p>
            </td>
        </tr>
        <tr>
            <td colspan="4" style="padding-top:0;width:100%;">
                <center>
                <table class="filter-container" border="0">
                    <form action="" method="post">
                        <td style="text-align:right;">Afficher les détails de : &nbsp;</td>
                        <td width="30%">
                            <select name="showonly" class="box filter-container-items" style="width:90%;height:37px;">
                                <option value="" disabled selected hidden><?php echo $current; ?></option>
                                <option value="my">Mes patients seulement</option>
                                <option value="all">Tous les patients</option>
                            </select>
                        </td>
                        <td width="12%">
                            <input type="submit" name="filter" value="Filtrer" class="btn-primary-soft btn button-icon btn-filter" style="padding:15px;width:100%">
                        </td>
                    </form>
                </table>
                </center>
            </td>
        </tr>
        <tr>
            <td colspan="4">
                <center>
                <div class="abc scroll">
                    <table width="93%" class="sub-table scrolldown" style="border-spacing:0;">
                        <thead>
                            <tr>
                                <th>Nom</th>
                                <th>NIC</th>
                                <th>Téléphone</th>
                                <th>Email</th>
                                <th>Date de naissance</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php
                            $result= $database->query($sqlmain);
                            if($result->num_rows==0){
                                echo '<tr>
                                    <td colspan="6">
                                        <br><center>
                                        <img src="../img/notfound.svg" width="25%">
                                        <p style="font-size:20px;color:rgb(49,49,49)">Aucun patient ne correspond à vos critères de recherche !</p>
                                        <a href="patient.php">
                                            <button class="login-btn btn-primary-soft btn">Afficher tous les patients</button>
                                        </a>
                                        </center><br>
                                    </td>
                                </tr>';
                            } else {
                                for($x=0;$x<$result->num_rows;$x++){
                                    $row=$result->fetch_assoc();
                                    $pid=$row["pid"];
                                    $name=$row["pname"];
                                    $email=$row["pemail"];
                                    $nic=$row["pnic"];
                                    $dob=$row["pdob"];
                                    $tel=$row["ptel"];
                                    echo '<tr>
                                        <td>'.substr($name,0,35).'</td>
                                        <td>'.substr($nic,0,12).'</td>
                                        <td>'.substr($tel,0,10).'</td>
                                        <td>'.substr($email,0,20).'</td>
                                        <td>'.substr($dob,0,10).'</td>
                                        <td style="display:flex;justify-content:center;">
                                            <a href="?action=view&id='.$pid.'" class="non-style-link">
                                                <button class="btn-primary-soft btn button-icon btn-view" style="padding:12px 40px;margin-top:10px;">Voir</button>
                                            </a>
                                        </td>
                                    </tr>';
                                }
                            }
                            ?>
                        </tbody>
                    </table>
                </div>
                </center>
            </td>
        </tr>
    </table>
</div>

<?php 
if($_GET){
    $id=$_GET["id"];
    $action=$_GET["action"];
    $sqlmain= "select * from patient where pid=?";
    $stmt = $database->prepare($sqlmain);
    $stmt->bind_param("i",$id);
    $stmt->execute();
    $result = $stmt->get_result();
    $row=$result->fetch_assoc();
    $name=$row["pname"];
    $email=$row["pemail"];
    $nic=$row["pnic"];
    $dob=$row["pdob"];
    $tele=$row["ptel"];
    $address=$row["paddress"];
    echo '
    <div id="popup1" class="overlay">
        <div class="popup">
        <center>
            <a class="close" href="patient.php">&times;</a>
            <div class="content"></div>
            <div style="display:flex;justify-content:center;">
                <table width="80%" class="sub-table scrolldown add-doc-form-container" border="0">
                    <tr><td><p style="font-size:25px;font-weight:500;">Voir les détails</p><br><br></td></tr>
                    <tr><td colspan="2"><label class="form-label">ID Patient: </label>P-'.$id.'<br><br></td></tr>
                    <tr><td colspan="2"><label class="form-label">Nom: </label>'.$name.'<br><br></td></tr>
                    <tr><td colspan="2"><label class="form-label">Email: </label>'.$email.'<br><br></td></tr>
                    <tr><td colspan="2"><label class="form-label">NIC: </label>'.$nic.'<br><br></td></tr>
                    <tr><td colspan="2"><label class="form-label">Téléphone: </label>'.$tele.'<br><br></td></tr>
                    <tr><td colspan="2"><label class="form-label">Adresse: </label>'.$address.'<br><br></td></tr>
                    <tr><td colspan="2"><label class="form-label">Date de naissance: </label>'.$dob.'<br><br></td></tr>
                    <tr><td colspan="2"><a href="patient.php"><input type="button" value="OK" class="login-btn btn-primary-soft btn"></a></td></tr>
                </table>
            </div>
        </center><br><br>
        </div>
    </div>';
}
?>
</body>
</html>
