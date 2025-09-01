<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="../css/animations.css">  
    <link rel="stylesheet" href="../css/main.css">  
    <link rel="stylesheet" href="../css/admin.css">
        
    <title>Médecins</title>
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
$userrow = $database->query("select * from patient where pemail='$useremail'");
$userfetch=$userrow->fetch_assoc();
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
                                <a href="../logout.php" ><input type="button" value="Déconnexion" class="logout-btn btn-primary-soft btn"></a>
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
                <td class="menu-btn menu-icon-doctor menu-active menu-icon-doctor-active">
                    <a href="doctors.php" class="non-style-link-menu non-style-link-menu-active"><div><p class="menu-text">Tous les Médecins</p></div></a>
                </td>
            </tr>
            <tr class="menu-row">
                <td class="menu-btn menu-icon-session">
                    <a href="schedule.php" class="non-style-link-menu"><div><p class="menu-text">Sessions programmées</p></div></a>
                </td>
            </tr>
            <tr class="menu-row">
                <td class="menu-btn menu-icon-appoinment">
                    <a href="appointment.php" class="non-style-link-menu"><div><p class="menu-text">Mes Réservations</p></div></a>
                </td>
            </tr>
            <tr class="menu-row">
                <td class="menu-btn menu-icon-settings">
                    <a href="settings.php" class="non-style-link-menu"><div><p class="menu-text">Paramètres</p></div></a>
                </td>
            </tr>
        </table>
    </div>
    <div class="dash-body">
        <table border="0" width="100%" style="border-spacing:0;margin:0;padding:0;margin-top:25px;">
            <tr>
                <td width="13%">
                    <a href="doctors.php"><button class="login-btn btn-primary-soft btn btn-icon-back" style="padding-top:11px;padding-bottom:11px;margin-left:20px;width:125px"><font class="tn-in-text">Retour</font></button></a>
                </td>
                <td>
                    <form action="" method="post" class="header-search">
                        <input type="search" name="search" class="input-text header-searchbar" placeholder="Rechercher nom ou email du médecin" list="doctors">&nbsp;&nbsp;
                        <?php
                        echo '<datalist id="doctors">';
                        $list11 = $database->query("select docname, docemail from doctor;");
                        for ($y=0;$y<$list11->num_rows;$y++){
                            $row00=$list11->fetch_assoc();
                            $d=$row00["docname"];
                            $c=$row00["docemail"];
                            echo "<option value='$d'><br/>";
                            echo "<option value='$c'><br/>";
                        };
                        echo '</datalist>';
                        ?>
                        <input type="Submit" value="Rechercher" class="login-btn btn-primary btn" style="padding-left:25px;padding-right:25px;padding-top:10px;padding-bottom:10px;">
                    </form>
                </td>
                <td width="15%">
                    <p style="font-size:14px;color:rgb(119,119,119);padding:0;margin:0;text-align:right;">Date du jour</p>
                    <p class="heading-sub12" style="padding:0;margin:0;">
                        <?php 
                        date_default_timezone_set('Africa/Douala');
                        $date = date('Y-m-d');
                        echo $date;
                        ?>
                    </p>
                </td>
                <td width="10%">
                    <button class="btn-label" style="display:flex;justify-content:center;align-items:center;"><img src="../img/calendar.svg" width="100%"></button>
                </td>
            </tr>
            <tr>
                <td colspan="4" style="padding-top:10px;">
                    <p class="heading-main12" style="margin-left:45px;font-size:18px;color:rgb(49,49,49)">Tous les Médecins (<?php echo $list11->num_rows; ?>)</p>
                </td>
            </tr>
            <?php
            if($_POST){
                $keyword=$_POST["search"];
                $sqlmain= "select * from doctor where docemail='$keyword' or docname='$keyword' or docname like '$keyword%' or docname like '%$keyword' or docname like '%$keyword%'";
            }else{
                $sqlmain= "select * from doctor order by docid desc";
            }
            ?>
            <tr>
                <td colspan="4">
                    <center>
                        <div class="abc scroll">
                            <table width="93%" class="sub-table scrolldown" border="0">
                                <thead>
                                    <tr>
                                        <th class="table-headin">Nom du Médecin</th>
                                        <th class="table-headin">Email</th>
                                        <th class="table-headin">Spécialités</th>
                                        <th class="table-headin">Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php
                                    $result= $database->query($sqlmain);
                                    if($result->num_rows==0){
                                        echo '<tr>
                                            <td colspan="4">
                                                <br><br><br><br>
                                                <center>
                                                    <img src="../img/notfound.svg" width="25%">
                                                    <br>
                                                    <p class="heading-main12" style="margin-left:45px;font-size:20px;color:rgb(49,49,49)">Aucun résultat trouvé pour vos mots-clés !</p>
                                                    <a class="non-style-link" href="doctors.php"><button class="login-btn btn-primary-soft btn" style="display:flex;justify-content:center;align-items:center;margin-left:20px;">&nbsp; Afficher tous les médecins &nbsp;</button></a>
                                                </center>
                                                <br><br><br><br>
                                            </td>
                                        </tr>';
                                    } else {
                                        for ($x=0; $x<$result->num_rows; $x++){
                                            $row=$result->fetch_assoc();
                                            $docid=$row["docid"];
                                            $name=$row["docname"];
                                            $email=$row["docemail"];
                                            $spe=$row["specialties"];
                                            $spcil_res= $database->query("select sname from specialties where id='$spe'");
                                            $spcil_array= $spcil_res->fetch_assoc();
                                            $spcil_name=$spcil_array["sname"];
                                            echo '<tr>
                                                <td>&nbsp;'.substr($name,0,30).'</td>
                                                <td>'.substr($email,0,20).'</td>
                                                <td>'.substr($spcil_name,0,20).'</td>
                                                <td>
                                                    <div style="display:flex;justify-content:center;">
                                                        <a href="?action=view&id='.$docid.'" class="non-style-link"><button class="btn-primary-soft btn button-icon btn-view" style="padding-left:40px;padding-top:12px;padding-bottom:12px;margin-top:10px;">Voir</button></a>
                                                        &nbsp;&nbsp;&nbsp;
                                                        <a href="?action=session&id='.$docid.'&name='.$name.'" class="non-style-link"><button class="btn-primary-soft btn button-icon menu-icon-session-active" style="padding-left:40px;padding-top:12px;padding-bottom:12px;margin-top:10px;">Sessions</button></a>
                                                    </div>
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
</div>

<?php
// Gestion des popups
if($_GET){
    $id=$_GET["id"];
    $action=$_GET["action"];
    $nameget = $_GET["name"] ?? '';
    $error_1=$_GET["error"] ?? '';

    $errorlist= array(
        '1'=>'<label class="form-label" style="color:rgb(255, 62, 62);text-align:center;">Un compte avec cet email existe déjà.</label>',
        '2'=>'<label class="form-label" style="color:rgb(255, 62, 62);text-align:center;">Erreur de confirmation du mot de passe !</label>',
        '3'=>'<label class="form-label" style="color:rgb(255, 62, 62);text-align:center;"></label>',
        '4'=>"",
        '0'=>'',
    );

    if($action=='drop'){
        echo '
        <div id="popup1" class="overlay">
            <div class="popup">
                <center>
                    <h2>Êtes-vous sûr ?</h2>
                    <a class="close" href="doctors.php">&times;</a>
                    <div class="content">
                        Vous voulez supprimer cet enregistrement<br>('.substr($nameget,0,40).').
                    </div>
                    <div style="display:flex;justify-content:center;">
                        <a href="delete-doctor.php?id='.$id.'" class="non-style-link"><button class="btn-primary btn" style="margin:10px;padding:10px;">Oui</button></a>
                        &nbsp;&nbsp;&nbsp;
                        <a href="doctors.php" class="non-style-link"><button class="btn-primary btn" style="margin:10px;padding:10px;">Non</button></a>
                    </div>
                </center>
            </div>
        </div>';
    } elseif($action=='view'){
        $sqlmain = "SELECT * FROM doctor WHERE docid=?";
        $stmt = $database->prepare($sqlmain);
        $stmt->bind_param("i",$id);
        $stmt->execute();
        $result = $stmt->get_result();
        $row = $result->fetch_assoc();
        $name=$row["docname"];
        $email=$row["docemail"];
        $spe=$row["specialties"];
        $stmt = $database->prepare("select sname from specialties where id=?");
        $stmt->bind_param("s",$spe);
        $stmt->execute();
        $spcil_res = $stmt->get_result();
        $spcil_array= $spcil_res->fetch_assoc();
        $spcil_name=$spcil_array["sname"];
        $nic=$row['docnic'];
        $tele=$row['doctel'];

        echo '
        <div id="popup1" class="overlay">
            <div class="popup">
                <center>
                    <a class="close" href="doctors.php">&times;</a>
                    <div class="content">eDoc Web App</div>
                    <table width="80%" class="sub-table scrolldown add-doc-form-container" border="0">
                        <tr><td colspan="2"><p style="font-size:25px;font-weight:500;">Voir les détails</p><br><br></td></tr>
                        <tr><td colspan="2">Nom : '.$name.'<br><br></td></tr>
                        <tr><td colspan="2">Email : '.$email.'<br><br></td></tr>
                        <tr><td colspan="2">NIC : '.$nic.'<br><br></td></tr>
                        <tr><td colspan="2">Téléphone : '.$tele.'<br><br></td></tr>
                        <tr><td colspan="2">Spécialités : '.$spcil_name.'<br><br></td></tr>
                        <tr><td colspan="2"><a href="doctors.php"><input type="button" value="OK" class="login-btn btn-primary-soft btn"></a></td></tr>
                    </table>
                </center>
            </div>
        </div>';
    } elseif($action=='session'){
        echo '
        <div id="popup1" class="overlay">
            <div class="popup">
                <center>
                    <h2>Rediriger vers les sessions du médecin ?</h2>
                    <a class="close" href="doctors.php">&times;</a>
                    <div class="content">
                        Vous voulez voir toutes les sessions de <br>('.substr($nameget,0,40).').
                    </div>
                    <form action="schedule.php" method="post" style="display:flex">
                        <input type="hidden" name="search" value="'.$nameget.'">
                        <div style="display:flex;justify-content:center;margin-left:45%;margin-top:6%;margin-bottom:6%;">
                            <input type="submit" value="Oui" class="btn-primary btn">
                        </div>
                    </form>
                </center>
            </div>
        </div>';
    } elseif($action=='edit'){
        $sqlmain = "SELECT * FROM doctor WHERE docid=?";
        $stmt = $database->prepare($sqlmain);
        $stmt->bind_param("i",$id);
        $stmt->execute();
        $result = $stmt->get_result();
        $row = $result->fetch_assoc();

        $name=$row["docname"];
        $email=$row["docemail"];
        $nic=$row["docnic"];
        $tele=$row["doctel"];
        $spe=$row["specialties"];

        echo '
        <div id="popup1" class="overlay">
            <div class="popup">
                <center>
                    <a class="close" href="doctors.php">&times;</a>
                    <div class="content">Modifier le médecin</div>
                    <form action="edit-doctor.php" method="post" class="add-new-form">
                        <input type="hidden" name="id" value="'.$id.'">
                        <table width="80%" class="sub-table scrolldown" border="0">
                            <tr><td colspan="2">'.$errorlist[$error_1].'</td></tr>
                            <tr>
                                <td class="label-td">Nom : </td>
                                <td class="label-td"><input type="text" name="name" value="'.$name.'" class="input-text" required></td>
                            </tr>
                            <tr>
                                <td class="label-td">Email : </td>
                                <td class="label-td"><input type="email" name="email" value="'.$email.'" class="input-text" required></td>
                            </tr>
                            <tr>
                                <td class="label-td">NIC : </td>
                                <td class="label-td"><input type="text" name="nic" value="'.$nic.'" class="input-text" required></td>
                            </tr>
                            <tr>
                                <td class="label-td">Téléphone : </td>
                                <td class="label-td"><input type="text" name="tele" value="'.$tele.'" class="input-text" required></td>
                            </tr>
                            <tr>
                                <td class="label-td">Spécialités : </td>
                                <td class="label-td">
                                    <select name="spe" class="box" required>
                                    <option value="'.$spe.'">Rester: '.$spe.'</option>';
                                    $list11 = $database->query("select * from specialties;");
                                    for ($y=0;$y<$list11->num_rows;$y++){
                                        $row00=$list11->fetch_assoc();
                                        $sn=$row00["sname"];
                                        echo "<option value='$sn'>$sn</option>";
                                    };
                                    echo '</select>
                                </td>
                            </tr>
                            <tr>
                                <td colspan="2"><input type="submit" value="Modifier" class="login-btn btn-primary btn" style="width:100%;"></td>
                            </tr>
                        </table>
                    </form>
                </center>
            </div>
        </div>';
    }
}
?>
</body>
</html>
