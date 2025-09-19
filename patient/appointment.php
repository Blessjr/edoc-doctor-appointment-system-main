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
            cursor: button;
            transition: all 0.3s ease;
            box-shadow: 0 4px 15px rgba(74, 107, 223, 0.3);
        }
        
        .call-button:hover {
            background: #3a5bc7;
            transform: translateY(-2px);
            box-shadow: 0 6px 20px rgba(74, 107, 223, 0.4);
        }
        
        .call-button:disabled {
            background: #cccccc;
            cursor: not-allowed;
            transform: none;
            box-shadow: none;
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
            cursor: button;
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
        
        .appointment-status {
            padding: 5px 10px;
            border-radius: 4px;
            font-size: 12px;
            font-weight: bold;
            display: inline-block;
            margin-top: 5px;
        }
        
        .status-scheduled {
            background-color: #fff3cd;
            color: #856404;
        }
        
        .status-in-progress {
            background-color: #d1ecf1;
            color: #0c5460;
        }
        
        .status-completed {
            background-color: #d4edda;
            color: #155724;
        }
        
        .status-cancelled {
            background-color: #f8d7da;
            color: #721c24;
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

// Set timezone to Cameroon
date_default_timezone_set('Africa/Douala');
$currentDateTime = new DateTime();
$currentDate = $currentDateTime->format('Y-m-d');
$currentTime = $currentDateTime->format('H:i:s');

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

// Récupération des rendez-vous avec les nouvelles colonnes
$sqlmain= "SELECT appointment.appoid, schedule.scheduleid, schedule.title, doctor.docname, 
                  patient.pname, schedule.scheduledate, schedule.scheduletime, 
                  appointment.apponum, appointment.appodate, appointment.start_time, 
                  appointment.end_time, appointment.status
           FROM schedule 
           INNER JOIN appointment ON schedule.scheduleid=appointment.scheduleid 
           INNER JOIN patient ON patient.pid=appointment.pid 
           INNER JOIN doctor ON schedule.docid=doctor.docid  
           WHERE patient.pid=$userid ";

if(!empty($_POST["sheduledate"])){
    $sheduledate = $database->real_escape_string($_POST["sheduledate"]);
    $sqlmain.=" and schedule.scheduledate='$sheduledate' ";
};

$sqlmain.=" ORDER BY appointment.appodate ASC, appointment.start_time ASC";
$result= $database->query($sqlmain);

// Check for active appointments
$activeAppointment = false;
$activeAppointmentId = null;
$activeAppointmentData = null;

if ($result->num_rows > 0) {
    while($row = $result->fetch_assoc()) {
        $appointmentDate = $row['scheduledate'];
        $startTime = $row['start_time'];
        $endTime = $row['end_time'];
        $status = $row['status'];
        
        // Check if current appointment is active (within time range and scheduled)
        if ($status == 'scheduled' && $appointmentDate == $currentDate) {
            $appointmentStart = DateTime::createFromFormat('Y-m-d H:i:s', $appointmentDate . ' ' . $startTime);
            $appointmentEnd = DateTime::createFromFormat('Y-m-d H:i:s', $appointmentDate . ' ' . $endTime);
            
            // Allow call 5 minutes before and 30 minutes after scheduled time
            $earlyStart = clone $appointmentStart;
            $earlyStart->modify('-5 minutes');
            $lateEnd = clone $appointmentEnd;
            $lateEnd->modify('+30 minutes');
            
            if ($currentDateTime >= $earlyStart && $currentDateTime <= $lateEnd) {
                $activeAppointment = true;
                $activeAppointmentId = $row['appoid'];
                $activeAppointmentData = $row;
                break;
            }
        }
    }
    
    // Reset result pointer
    $result->data_seek(0);
}
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
                        <?php echo $currentDate; ?>
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
                                        <input type="date" name="sheduledate" id="date" class="input-text filter-container-items" style="margin: 0;width: 95%;" value="<?php echo !empty($_POST['sheduledate']) ? $_POST['sheduledate'] : ''; ?>">
                                </td>
                                <td width="12%">
                                    <input type="submit"  name="filter" value=" Filtrer" class=" btn-primary-soft btn button-icon btn-filter"  style="padding: 15px; margin :0;width:100%">
                                    </form>
                                </td>
                                <td width="12%">
                                    <a href="appointment.php" class="non-style-link"><button  class="btn-primary-soft btn button-icon btn-filter"  style="padding: 15px; margin :0;width:100%">Réinitialiser</button></a>
                                </td>
                            </tr>
                        </table>
                    </center>
                </td>
            </tr>
            
            <!-- Call Button - Only show if there's an active appointment -->
            <?php if ($activeAppointment): ?>
            <tr>
                <td colspan="4">
                    <div class="call-button-container">
                        <button class="call-button" onclick="openVideoCall()">
                            <i class="fas fa-video"></i> Rejoindre la consultation
                        </button>
                        <p style="margin-top: 10px; color: #4a6bdf;">
                            <i class="fas fa-info-circle"></i> Votre consultation avec Dr. <?php echo $activeAppointmentData['docname']; ?> est maintenant disponible
                        </p>
                    </div>
                </td>
            </tr>
            <?php else: ?>
            <tr>
                <td colspan="4">
                    <div class="call-button-container">
                        <button class="call-button" disabled>
                            <i class="fas fa-video"></i> Consultation non disponible
                        </button>
                        <p style="margin-top: 10px; color: #666;">
                            <i class="fas fa-info-circle"></i> L'appel vidéo sera disponible 5 minutes avant votre rendez-vous
                        </p>
                    </div>
                </td>
            </tr>
            <?php endif; ?>
            
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
                                    echo "<tr>";
                                    $count = 0;
                                    while($row = $result->fetch_assoc()){
                                        if ($count % 3 == 0 && $count != 0) {
                                            echo "</tr><tr>";
                                        }
                                        
                                        $scheduleid=$row["scheduleid"];
                                        $title=$row["title"];
                                        $docname=$row["docname"];
                                        $scheduledate=$row["scheduledate"];
                                        $scheduletime=$row["scheduletime"];
                                        $apponum=$row["apponum"];
                                        $appodate=$row["appodate"];
                                        $appoid=$row["appoid"];
                                        $start_time=$row["start_time"];
                                        $end_time=$row["end_time"];
                                        $status=$row["status"];
                                        
                                        // Determine status class
                                        $status_class = "status-" . str_replace(' ', '-', $status);
                                        $status_text = ucfirst($status);
                                        
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
                                                        Date prévue: '.$scheduledate.'<br>
                                                        Heure: <b>'.substr($start_time,0,5).' - '.substr($end_time,0,5).'</b>
                                                    </div>
                                                    <div class="appointment-status '.$status_class.'">
                                                        '.$status_text.'
                                                    </div>
                                                    <br>';
                                                    
                                                    // Only show cancel button for scheduled appointments
                                                    if ($status == 'scheduled') {
                                                        echo '<a href="?action=drop&id='.$appoid.'&title='.$title.'&doc='.$docname.'" ><button  class="login-btn btn-primary-soft btn "  style="padding-top:11px;padding-bottom:11px;width:100%"><font class="tn-in-text">Annuler la réservation</font></button></a>';
                                                    }
                                                    
                                                    echo '
                                                </div>
                                            </div>
                                        </td>';
                                        $count++;
                                    }
                                    // Fill remaining cells if needed
                                    while ($count % 3 != 0) {
                                        echo '<td style="width: 25%;"></td>';
                                        $count++;
                                    }
                                    echo "</tr>";
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
                <div class="mirotalk-title">Consultation avec Dr. <?php echo $activeAppointment ? $activeAppointmentData['docname'] : ''; ?></div>
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
    iframe.src = 'https://c2c.mirotalk.com/join?room=consultation-<?php echo $activeAppointmentId; ?>&name=<?php echo urlencode($username); ?>';
    
    // Show the fullscreen container
    fullscreenDiv.style.display = 'block';
    
    // Prevent scrolling on the background page
    document.body.style.overflow = 'hidden';
    
    // Update appointment status to in-progress
    fetch('update_appointment_status.php?appoid=<?php echo $activeAppointmentId; ?>&status=in-progress')
        .catch(err => console.error('Error updating appointment status:', err));
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
    
    // Update appointment status to completed
    fetch('update_appointment_status.php?appoid=<?php echo $activeAppointmentId; ?>&status=completed')
        .catch(err => console.error('Error updating appointment status:', err));
}

// Close the video call when pressing the Escape key
document.addEventListener('keydown', function(event) {
    if (event.key === 'Escape') {
        closeVideoCall();
    }
});

// Check if the appointment time has arrived
function checkAppointmentTime() {
    fetch('check_appointment_time.php')
        .then(response => response.json())
        .then(data => {
            if (data.active) {
                window.location.reload();
            }
        })
        .catch(err => console.error('Error checking appointment time:', err));
}

// Check every minute if an appointment is about to start
setInterval(checkAppointmentTime, 60000);
</script>
</body>
</html>