<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="../css/animations.css">  
    <link rel="stylesheet" href="../css/main.css">  
    <link rel="stylesheet" href="../css/admin.css">
        
    <title>Paramètres</title>
    <style>
        .dashbord-tables{
            animation: transitionIn-Y-over 0.5s;
        }
        .filter-container{
            animation: transitionIn-X  0.5s;
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
    if(($_SESSION["user"])=="" or $_SESSION['usertype']!='p'){
        header("location: ../login.php");
    }else{
        $useremail=$_SESSION["user"];
    }
}else{
    header("location: ../login.php");
}

// Importation de la base de données
include("../connection.php");
$sqlmain= "SELECT * FROM patient WHERE pemail=?";
$stmt = $database->prepare($sqlmain);
$stmt->bind_param("s",$useremail);
$stmt->execute();
$result = $stmt->get_result();
$userfetch=$result->fetch_assoc();
$userid= $userfetch["pid"];
$username=$userfetch["pname"];
?>
<div class="container">
    <div class="menu">
        <table class="menu-container" border="0">
            <tr>
                <td style="padding:10px" colspan="2">
                    <table border="0" class="profile-container">
                        <tr>
                            <td width="30%" style="padding-left:20px">
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
            <tr class="menu-row">
                <td class="menu-btn menu-icon-home">
                    <a href="index.php" class="non-style-link-menu"><div><p class="menu-text">Accueil</p></div></a>
                </td>
            </tr>
            <tr class="menu-row">
                <td class="menu-btn menu-icon-doctor">
                    <a href="doctors.php" class="non-style-link-menu"><div><p class="menu-text">Tous les médecins</p></div></a>
                </td>
            </tr>
            <tr class="menu-row">
                <td class="menu-btn menu-icon-session">
                    <a href="schedule.php" class="non-style-link-menu"><div><p class="menu-text">Séances programmées</p></div></a>
                </td>
            </tr>
            <tr class="menu-row">
                <td class="menu-btn menu-icon-appoinment">
                    <a href="appointment.php" class="non-style-link-menu"><div><p class="menu-text">Mes rendez-vous</p></div></a>
                </td>
            </tr>
            <tr class="menu-row">
                <td class="menu-btn menu-icon-settings menu-active menu-icon-settings-active">
                    <a href="settings.php" class="non-style-link-menu non-style-link-menu-active"><div><p class="menu-text">Paramètres</p></div></a>
                </td>
            </tr>
        </table>
    </div>

    <div class="dash-body" style="margin-top: 15px">
        <table border="0" width="100%" style="border-spacing: 0;margin:0;padding:0;">
            <tr>
                <td width="13%">
                    <a href="settings.php"><button class="login-btn btn-primary-soft btn btn-icon-back" style="padding-top:11px;padding-bottom:11px;margin-left:20px;width:125px"><font class="tn-in-text">Retour</font></button></a>
                </td>
                <td>
                    <p style="font-size: 23px;padding-left:12px;font-weight: 600;">Paramètres</p>
                </td>
                <td width="15%">
                    <p style="font-size: 14px;color: rgb(119, 119, 119);padding: 0;margin: 0;text-align: right;">Date d'aujourd'hui</p>
                    <p class="heading-sub12" style="padding: 0;margin: 0;">
                        <?php 
                        date_default_timezone_set('Africa/Douala');
                        $today = date('Y-m-d');
                        echo $today;

                        $patientrow = $database->query("SELECT * FROM patient;");
                        $doctorrow = $database->query("SELECT * FROM doctor;");
                        $appointmentrow = $database->query("SELECT * FROM appointment WHERE appodate>='$today';");
                        $schedulerow = $database->query("SELECT * FROM schedule WHERE scheduledate='$today';");
                        ?>
                    </p>
                </td>
                <td width="10%">
                    <button class="btn-label" style="display: flex;justify-content: center;align-items: center;"><img src="../img/calendar.svg" width="100%"></button>
                </td>
            </tr>

            <tr>
                <td colspan="4">
                    <center>
                        <table class="filter-container" style="border: none;" border="0">
                            <tr>
                                <td colspan="4"><p style="font-size: 20px">&nbsp;</p></td>
                            </tr>
                            <tr>
                                <td style="width: 25%;">
                                    <a href="?action=edit&id=<?php echo $userid ?>&error=0" class="non-style-link">
                                        <div class="dashboard-items setting-tabs" style="padding:20px;margin:auto;width:95%;display: flex">
                                            <div class="btn-icon-back dashboard-icons-setting" style="background-image: url('../img/icons/doctors-hover.svg');"></div>
                                            <div>
                                                <div class="h1-dashboard">Paramètres du compte</div><br>
                                                <div class="h3-dashboard" style="font-size: 15px;">Modifier vos informations et changer le mot de passe</div>
                                            </div>
                                        </div>
                                    </a>
                                </td>
                            </tr>

                            <tr><td colspan="4"><p style="font-size: 5px">&nbsp;</p></td></tr>

                            <tr>
                                <td style="width: 25%;">
                                    <a href="?action=view&id=<?php echo $userid ?>" class="non-style-link">
                                        <div class="dashboard-items setting-tabs" style="padding:20px;margin:auto;width:95%;display: flex;">
                                            <div class="btn-icon-back dashboard-icons-setting" style="background-image: url('../img/icons/view-iceblue.svg');"></div>
                                            <div>
                                                <div class="h1-dashboard">Voir les détails du compte</div><br>
                                                <div class="h3-dashboard" style="font-size: 15px;">Voir les informations personnelles de votre compte</div>
                                            </div>
                                        </div>
                                    </a>
                                </td>
                            </tr>

                            <tr><td colspan="4"><p style="font-size: 5px">&nbsp;</p></td></tr>

                            <tr>
                                <td style="width: 25%;">
                                    <a href="?action=drop&id=<?php echo $userid.'&name='.$username ?>" class="non-style-link">
                                        <div class="dashboard-items setting-tabs" style="padding:20px;margin:auto;width:95%;display: flex;">
                                            <div class="btn-icon-back dashboard-icons-setting" style="background-image: url('../img/icons/patients-hover.svg');"></div>
                                            <div>
                                                <div class="h1-dashboard" style="color: #ff5050;">Supprimer le compte</div><br>
                                                <div class="h3-dashboard" style="font-size: 15px;">Supprime définitivement votre compte</div>
                                            </div>
                                        </div>
                                    </a>
                                </td>
                            </tr>
                        </table>
                    </center>
                </td>
            </tr>
        </table>
    </div>
</div>

<?php
if($_GET){
    $id=$_GET["id"];
    $action=$_GET["action"];
    if($action=='drop'){
        $nameget=$_GET["name"];
        echo '
        <div id="popup1" class="overlay">
            <div class="popup">
                <center>
                    <h2>Êtes-vous sûr ?</h2>
                    <a class="close" href="settings.php">&times;</a>
                    <div class="content">
                        Vous voulez supprimer votre compte<br>('.substr($nameget,0,40).').
                    </div>
                    <div style="display: flex;justify-content: center;">
                        <a href="delete-account.php?id='.$id.'" class="non-style-link"><button class="btn-primary btn" style="margin:10px;padding:10px;">Oui</button></a>&nbsp;&nbsp;&nbsp;
                        <a href="settings.php" class="non-style-link"><button class="btn-primary btn" style="margin:10px;padding:10px;">Non</button></a>
                    </div>
                </center>
            </div>
        </div>';
    }elseif($action=='view'){
        $sqlmain= "SELECT * FROM patient WHERE pid=?";
        $stmt = $database->prepare($sqlmain);
        $stmt->bind_param("i", $id);
        $stmt->execute();
        $result = $stmt->get_result();
        $row=$result->fetch_assoc();
        $name=$row["pname"];
        $email=$row["pemail"];
        $address=$row["paddress"];
        $dob=$row["pdob"];
        $nic=$row['pnic'];
        $tele=$row['ptel'];
        echo '
        <div id="popup1" class="overlay">
            <div class="popup">
                <center>
                    <a class="close" href="settings.php">&times;</a>
                    <div class="content">Application Web DOCTO LINK</div>
                    <div style="display: flex;justify-content: center;">
                        <table width="80%" class="sub-table scrolldown add-doc-form-container" border="0">
                            <tr>
                                <td><p style="font-size: 25px;font-weight: 500;">Détails du compte</p><br><br></td>
                            </tr>
                            <tr><td>Nom : '.$name.'</td></tr>
                            <tr><td>Email : '.$email.'</td></tr>
                            <tr><td>NIC : '.$nic.'</td></tr>
                            <tr><td>Téléphone : '.$tele.'</td></tr>
                            <tr><td>Adresse : '.$address.'</td></tr>
                            <tr><td>Date de naissance : '.$dob.'</td></tr>
                            <tr><td><a href="settings.php"><input type="button" value="OK" class="login-btn btn-primary-soft btn"></a></td></tr>
                        </table>
                    </div>
                </center>
            </div>
        </div>';
    }elseif($action=='edit'){
        $sqlmain= "SELECT * FROM patient WHERE pid=?";
        $stmt = $database->prepare($sqlmain);
        $stmt->bind_param("i", $id);
        $stmt->execute();
        $result = $stmt->get_result();
        $row=$result->fetch_assoc();
        $name=$row["pname"];
        $email=$row["pemail"];
        $address=$row["paddress"];
        $nic=$row['pnic'];
        $tele=$row['ptel'];

        $error_1=$_GET["error"];
        $errorlist= array(
            '1'=>'<label style="color:rgb(255, 62, 62);text-align:center;">Un compte existe déjà pour cet email.</label>',
            '2'=>'<label style="color:rgb(255, 62, 62);text-align:center;">Erreur de confirmation du mot de passe !</label>',
            '3'=>'<label style="color:rgb(255, 62, 62);text-align:center;"></label>',
            '4'=>"",'0'=>'',
        );

        echo '
        <div id="popup1" class="overlay">
            <div class="popup">
                <center>
                    <a class="close" href="settings.php">&times;</a>
                    <div style="display: flex;justify-content: center;">
                        <div class="abc">
                            <table width="80%" class="sub-table scrolldown add-doc-form-container" border="0">
                                <tr><td colspan="2">'.$errorlist[$error_1].'</td></tr>
                                <tr><td><p style="font-size: 25px;font-weight: 500;">Modifier le compte utilisateur</p>User ID : '.$id.' (Auto Généré)<br><br></td></tr>
                                <tr><td colspan="2">
                                    <form action="edit-user.php" method="POST" class="add-new-form">
                                        <input type="hidden" value="'.$id.'" name="id00">
                                        <input type="hidden" name="oldemail" value="'.$email.'">
                                        Email : <input type="email" name="email" value="'.$email.'" required><br>
                                        Nom : <input type="text" name="name" value="'.$name.'" required><br>
                                        NIC : <input type="text" name="nic" value="'.$nic.'" required><br>
                                        Téléphone : <input type="tel" name="Tele" value="'.$tele.'" required><br>
                                        Adresse : <input type="text" name="address" value="'.$address.'" required><br>
                                        Mot de passe : <input type="password" name="password" required><br>
                                        Confirmer le mot de passe : <input type="password" name="cpassword" required><br>
                                        <input type="reset" value="Réinitialiser" class="login-btn btn-primary-soft btn">
                                        <input type="submit" value="Enregistrer" class="login-btn btn-primary btn">
                                    </form>
                                </td></tr>
                            </table>
                        </div>
                    </div>
                </center>
            </div>
        </div>';
    }else{
        echo '
        <div id="popup1" class="overlay">
            <div class="popup">
                <center>
                    <h2>Modification réussie !</h2>
                    <a class="close" href="settings.php">&times;</a>
                    <div class="content">Si vous avez changé votre email, veuillez vous déconnecter et vous reconnecter avec le nouvel email.</div>
                    <div style="display: flex;justify-content: center;">
                        <a href="settings.php" class="non-style-link"><button class="btn-primary btn">OK</button></a>
                        <a href="../logout.php" class="non-style-link"><button class="btn-primary-soft btn">Déconnexion</button></a>
                    </div>
                </center>
            </div>
        </div>';
    }
}
?>
</body>
</html>
