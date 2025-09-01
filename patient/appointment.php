<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="../css/animations.css">  
    <link rel="stylesheet" href="../css/main.css">  
    <link rel="stylesheet" href="../css/admin.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
        
    <title>Rendez-vous</title>
    <style>
        .popup{
            animation: transitionIn-Y-bottom 0.5s;
        }
        .sub-table{
            animation: transitionIn-Y-bottom 0.5s;
        }
        
        /* MiroTalk Integration Styles */
        .call-button-container {
            text-align: center;
            margin: 30px 0;
        }
        
        .call-button {
            padding: 15px 30px;
            background: #4a6bdf;
            color: white;
            border: none;
            border-radius: 8px;
            font-size: 18px;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.3s ease;
            box-shadow: 0 4px 15px rgba(74, 107, 223, 0.3);
        }
        
        .call-button:hover {
            background: #3a5bc7;
            transform: translateY(-2px);
            box-shadow: 0 6px 20px rgba(74, 107, 223, 0.4);
        }
        
        .call-button i {
            margin-right: 10px;
        }
        
        .mirotalk-fullscreen {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: white;
            z-index: 9999;
            display: none;
        }
        
        .mirotalk-header {
            background: #4a6bdf;
            color: white;
            padding: 15px 20px;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }
        
        .mirotalk-title {
            font-size: 20px;
            font-weight: 600;
        }
        
        .close-call {
            background: rgba(255, 255, 255, 0.2);
            border: none;
            color: white;
            padding: 8px 15px;
            border-radius: 5px;
            cursor: pointer;
            transition: background 0.3s ease;
        }
        
        .close-call:hover {
            background: rgba(255, 255, 255, 0.3);
        }
        
        .mirotalk-frame {
            width: 100%;
            height: calc(100% - 60px);
            border: none;
        }
    </style>
</head>
<body>
<?php
session_start();

if(isset($_SESSION["user"])){
    if(($_SESSION["user"])=="" or $_SESSION['usertype']!='p'){
        header("location: ../login.php");
    } else {
        $useremail=$_SESSION["user"];
    }
} else {
    header("location: ../login.php");
}

// Connexion à la base de données
include("../connection.php");
$sqlmain= "select * from patient where pemail=?";
$stmt = $database->prepare($sqlmain);
$stmt->bind_param("s",$useremail);
$stmt->execute();
$userrow = $stmt->get_result();
$userfetch=$userrow->fetch_assoc();
$userid= $userfetch["pid"];
$username=$userfetch["pname"];

// Récupération des rendez-vous
$sqlmain= "select appointment.appoid,schedule.scheduleid,schedule.title,doctor.docname,patient.pname,schedule.scheduledate,schedule.scheduletime,appointment.apponum,appointment.appodate from schedule inner join appointment on schedule.scheduleid=appointment.scheduleid inner join patient on patient.pid=appointment.pid inner join doctor on schedule.docid=doctor.docid  where  patient.pid=$userid ";

if($_POST){
    if(!empty($_POST["sheduledate"])){
        $sheduledate=$_POST["sheduledate"];
        $sqlmain.=" and schedule.scheduledate='$sheduledate' ";
    };
}

$sqlmain.="order by appointment.appodate asc";
$result= $database->query($sqlmain);
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
                                <a href="../logout.php" ><input type="button" value="Déconnexion" class="logout-btn btn-primary-soft btn"></a>
                            </td>
                        </tr>
                    </table>
                </td>
            </tr>
            <tr class="menu-row" >
                <td class="menu-btn menu-icon-home" >
                    <a href="index.php" class="non-style-link-menu "><div><p class="menu-text">Accueil</p></a></div></a>
                </td>
            </tr>
            <tr class="menu-row">
                <td class="menu-btn menu-icon-doctor">
                    <a href="doctors.php" class="non-style-link-menu"><div><p class="menu-text">Tous les médecins</p></a></div>
                </td>
            </tr>
            <tr class="menu-row" >
                <td class="menu-btn menu-icon-session">
                    <a href="schedule.php" class="non-style-link-menu"><div><p class="menu-text">Sessions planifiées</p></div></a>
                </td>
            </tr>
            <tr class="menu-row" >
                <td class="menu-btn menu-icon-appoinment  menu-active menu-icon-appoinment-active">
                    <a href="appointment.php" class="non-style-link-menu non-style-link-menu-active"><div><p class="menu-text">Mes réservations</p></a></div>
                </td>
            </tr>
            <tr class="menu-row" >
                <td class="menu-btn menu-icon-settings">
                    <a href="settings.php" class="non-style-link-menu"><div><p class="menu-text">Paramètres</p></a></div>
                </td>
            </tr>
        </table>
    </div>
    <div class="dash-body">
        <table border="0" width="100%" style=" border-spacing: 0;margin:0;padding:0;margin-top:25px; ">
            <tr >
                <td width="13%" >
                    <a href="appointment.php" ><button  class="login-btn btn-primary-soft btn btn-icon-back"  style="padding-top:11px;padding-bottom:11px;margin-left:20px;width:125px"><font class="tn-in-text">Retour</font></button></a>
                </td>
                <td>
                    <p style="font-size: 23px;padding-left:12px;font-weight: 600;">Historique de mes réservations</p>
                </td>
                <td width="15%">
                    <p style="font-size: 14px;color: rgb(119, 119, 119);padding: 0;margin: 0;text-align: right;">
                        Date d'aujourd'hui
                    </p>
                    <p class="heading-sub12" style="padding: 0;margin: 0;">
                        <?php 
                        date_default_timezone_set('Africa/Douala');
                        $today = date('Y-m-d');
                        echo $today;
                        ?>
                    </p>
                </td>
                <td width="10%">
                    <button  class="btn-label"  style="display: flex;justify-content: center;align-items: center;"><img src="../img/calendar.svg" width="100%"></button>
                </td>
            </tr>
            <tr>
                <td colspan="4" style="padding-top:10px;width: 100%;" >
                    <p class="heading-main12" style="margin-left: 45px;font-size:18px;color:rgb(49, 49, 49)">Mes réservations (<?php echo $result->num_rows; ?>)</p>
                </td>
            </tr>
            <tr>
                <td colspan="4" style="padding-top:0px;width: 100%;" >
                    <center>
                        <table class="filter-container" border="0" >
                            <tr>
                                <td width="10%"></td> 
                                <td width="5%" style="text-align: center;">Date:</td>
                                <td width="30%">
                                    <form action="" method="post">
                                        <input type="date" name="sheduledate" id="date" class="input-text filter-container-items" style="margin: 0;width: 95%;">
                                </td>
                                <td width="12%">
                                    <input type="submit"  name="filter" value=" Filtrer" class=" btn-primary-soft btn button-icon btn-filter"  style="padding: 15px; margin :0;width:100%">
                                    </form>
                                </td>
                            </tr>
                        </table>
                    </center>
                </td>
            </tr>
            
            <!-- Call Button -->
            <tr>
                <td colspan="4">
                    <div class="call-button-container">
                        <button class="call-button" onclick="openVideoCall()">
                            <i class="fas fa-video"></i> Démarrer l'appel vidéo
                        </button>
                    </div>
                </td>
            </tr>
            
            <tr>
                <td colspan="4">
                    <center>
                        <div class="abc scroll">
                            <table width="93%" class="sub-table scrolldown" border="0" style="border:none">
                                <tbody>
                                <?php
                                if($result->num_rows==0){
                                    echo '<tr>
                                    <td colspan="7">
                                    <br><br><br><br>
                                    <center>
                                    <img src="../img/notfound.svg" width="25%">
                                    <br>
                                    <p class="heading-main12" style="margin-left: 45px;font-size:20px;color:rgb(49, 49, 49)">Aucun résultat correspondant à vos critères !</p>
                                    <a class="non-style-link" href="appointment.php"><button  class="login-btn btn-primary-soft btn"  style="display: flex;justify-content: center;align-items: center;margin-left:20px;">&nbsp; Voir tous les rendez-vous &nbsp;</font></button></a>
                                    </center>
                                    <br><br><br><br>
                                    </td>
                                    </tr>';
                                } else {
                                    for ($x=0; $x<$result->num_rows;$x++){
                                        echo "<tr>";
                                        for($q=0;$q<3;$q++){
                                            $row=$result->fetch_assoc();
                                            if (!isset($row)) break;
                                            $scheduleid=$row["scheduleid"];
                                            $title=$row["title"];
                                            $docname=$row["docname"];
                                            $scheduledate=$row["scheduledate"];
                                            $scheduletime=$row["scheduletime"];
                                            $apponum=$row["apponum"];
                                            $appodate=$row["appodate"];
                                            $appoid=$row["appoid"];

                                            if($scheduleid=="") break;

                                            echo '
                                            <td style="width: 25%;">
                                                <div  class="dashboard-items search-items"  >
                                                    <div style="width:100%;">
                                                        <div class="h3-search">
                                                            Date de réservation: '.substr($appodate,0,30).'<br>
                                                            Numéro de référence: OC-000-'.$appoid.'
                                                        </div>
                                                        <div class="h1-search">
                                                            '.substr($title,0,21).'<br>
                                                        </div>
                                                        <div class="h3-search">
                                                            Numéro de rendez-vous:<div class="h1-search">0'.$apponum.'</div>
                                                        </div>
                                                        <div class="h3-search">
                                                            '.substr($docname,0,30).'
                                                        </div>
                                                        <div class="h4-search">
                                                            Date prévue: '.$scheduledate.'<br>Heure de début: <b>@'.substr($scheduletime,0,5).'</b> (24h)
                                                        </div>
                                                        <br>
                                                        <a href="?action=drop&id='.$appoid.'&title='.$title.'&doc='.$docname.'" ><button  class="login-btn btn-primary-soft btn "  style="padding-top:11px;padding-bottom:11px;width:100%"><font class="tn-in-text">Annuler la réservation</font></button></a>
                                                    </div>
                                                </div>
                                            </td>';
                                        }
                                        echo "</tr>";
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
        
        <!-- MiroTalk Full Screen Video Conference -->
        <div id="mirotalk-fullscreen" class="mirotalk-fullscreen">
            <div class="mirotalk-header">
                <div class="mirotalk-title">Appel Vidéo en Cours</div>
                <button class="close-call" onclick="closeVideoCall()">
                    <i class="fas fa-times"></i> Terminer l'appel
                </button>
            </div>
            <iframe 
                id="mirotalk-frame"
                class="mirotalk-frame"
                allow="camera; microphone; display-capture; fullscreen; clipboard-read; clipboard-write; web-share; autoplay"
                src=""
            ></iframe>
        </div>
        
    </div>
</div>

<?php
if($_GET){
    $id=$_GET["id"];
    $action=$_GET["action"];
    if($action=='booking-added'){
        echo '
        <div id="popup1" class="overlay">
            <div class="popup">
                <center>
                    <br><br>
                    <h2>Réservation réussie.</h2>
                    <a class="close" href="appointment.php">&times;</a>
                    <div class="content">
                        Votre numéro de rendez-vous est '.$id.'.<br><br>
                    </div>
                    <div style="display: flex;justify-content: center;">
                        <a href="appointment.php" class="non-style-link"><button  class="btn-primary btn" style="display: flex;justify-content: center;align-items: center;margin:10px;padding:10px;"><font class="tn-in-text">&nbsp;&nbsp;OK&nbsp;&nbsp;</font></button></a>
                        <br><br><br><br>
                    </div>
                </center>
            </div>
        </div>';
    } elseif($action=='drop'){
        $title=$_GET["title"];
        $docname=$_GET["doc"];
        echo '
        <div id="popup1" class="overlay">
            <div class="popup">
                <center>
                    <h2>Êtes-vous sûr ?</h2>
                    <a class="close" href="appointment.php">&times;</a>
                    <div class="content">
                        Voulez-vous annuler ce rendez-vous ?<br><br>
                        Nom de la session: &nbsp;<b>'.substr($title,0,40).'</b><br>
                        Nom du médecin&nbsp; : <b>'.substr($docname,0,40).'</b><br><br>
                    </div>
                    <div style="display: flex;justify-content: center;">
                        <a href="delete-appointment.php?id='.$id.'" class="non-style-link"><button  class="btn-primary btn" style="display: flex;justify-content: center;align-items: center;margin:10px;padding:10px;"<font class="tn-in-text">&nbsp;Oui&nbsp;</font></button></a>&nbsp;&nbsp;&nbsp;
                        <a href="appointment.php" class="non-style-link"><button  class="btn-primary btn" style="display: flex;justify-content: center;align-items: center;margin:10px;padding:10px;"><font class="tn-in-text">&nbsp;&nbsp;Non&nbsp;&nbsp;</font></button></a>
                    </div>
                </center>
            </div>
        </div>';
    }
}
?>

<!-- Notifications dynamiques -->
<div id="notifications" style="position: fixed; top: 20px; right: 20px; z-index: 9999;"></div>
<script>
function fetchNotifications(){
    fetch('check_notifications.php')
        .then(response => response.json())
        .then(data => {
            const container = document.getElementById('notifications');
            container.innerHTML = '';
            data.forEach(note => {
                const div = document.createElement('div');
                div.style = "background-color: #fffae6; border-left: 6px solid #ffcc00; padding: 10px; margin-bottom: 10px; box-shadow: 0px 2px 6px rgba(0,0,0,0.2);";
                div.textContent = note;
                container.appendChild(div);
                setTimeout(() => div.remove(), 10000);
            });
        })
        .catch(err => console.error(err));
}
fetchNotifications();
setInterval(fetchNotifications, 60000);

// MiroTalk Video Call Functions
function openVideoCall() {
    const fullscreenDiv = document.getElementById('mirotalk-fullscreen');
    const iframe = document.getElementById('mirotalk-frame');
    
    // Set the iframe source to the direct join URL
    iframe.src = 'https://c2c.mirotalk.com/join?room=consultation&name=<?php echo $username; ?>';
    
    // Show the fullscreen container
    fullscreenDiv.style.display = 'block';
    
    // Prevent scrolling on the background page
    document.body.style.overflow = 'hidden';
}

function closeVideoCall() {
    const fullscreenDiv = document.getElementById('mirotalk-fullscreen');
    const iframe = document.getElementById('mirotalk-frame');
    
    // Hide the fullscreen container
    fullscreenDiv.style.display = 'none';
    
    // Stop the video call by removing the iframe source
    iframe.src = '';
    
    // Re-enable scrolling on the background page
    document.body.style.overflow = 'auto';
}

// Close the video call when pressing the Escape key
document.addEventListener('keydown', function(event) {
    if (event.key === 'Escape') {
        closeVideoCall();
    }
});
</script>
</body>
</html>