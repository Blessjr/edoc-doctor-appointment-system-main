<?php
// Mettre tout le code PHP en haut pour éviter les erreurs de session
session_start();

if(isset($_SESSION["user"])){
    if(($_SESSION["user"])=="" or $_SESSION['usertype']!='p'){ // Vérifie que c'est bien un patient
        header("location: ../login.php");
        exit();
    }else{
        $useremail=$_SESSION["user"];
    }
}else{
    header("location: ../login.php");
    exit();
}

// Importer la base de données
include("../connection.php");

$sqlmain= "select * from patient where pemail=?";
$stmt = $database->prepare($sqlmain);
$stmt->bind_param("s",$useremail);
$stmt->execute();
$userrow = $stmt->get_result();
$userfetch=$userrow->fetch_assoc();
$userid= $userfetch["pid"];
$username=$userfetch["pname"];

// Données pour le tableau de bord
date_default_timezone_set('Africa/Douala');
$today = date('Y-m-d');
$patientrow_count = $database->query("select * from patient;");
$doctorrow_count = $database->query("select * from doctor;");
$appointmentrow_count = $database->query("select * from appointment where appodate>='$today';");
$schedulerow_count = $database->query("select * from schedule where scheduledate='$today';");

// Pour la liste de recherche des médecins
$list11 = $database->query("select docname,docemail from doctor;");

// Fetch notifications for the patient
$notifications = array();
$notification_query = $database->query("
    SELECT * FROM notifications 
    WHERE user_id = '$userid' OR user_type = 'p' OR user_type = 'all'
    ORDER BY created_at DESC 
    LIMIT 10
");

if($notification_query && $notification_query->num_rows > 0) {
    while($row = $notification_query->fetch_assoc()) {
        $notifications[] = $row;
    }
}

$unread_count = 0;
$unread_query = $database->query("
    SELECT COUNT(*) as count FROM notifications 
    WHERE (user_id = '$userid' OR user_type = 'p' OR user_type = 'all') 
    AND is_read = 0
");
if($unread_query && $unread_query->num_rows > 0) {
    $unread_data = $unread_query->fetch_assoc();
    $unread_count = $unread_data['count'];
}
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="../css/animations.css">      
    <link rel="stylesheet" href="../css/main.css">      
    <link rel="stylesheet" href="../css/admin.css">
    <title>Tableau de bord</title>
    <style>
        .dashbord-tables{
            animation: transitionIn-Y-over 0.5s;
        }
        .filter-container{
            animation: transitionIn-Y-bottom  0.5s;
        }
        .sub-table,.anime{
            animation: transitionIn-Y-bottom 0.5s;
        }
        
        /* Notification Styles */
        .notification-container {
            position: relative;
            display: inline-block;
        }
        
        .notification-bell {
            position: relative;
            cursor: pointer;
            font-size: 24px;
            padding: 10px;
            color: #2c5cc7;
        }
        
        .notification-badge {
            position: absolute;
            top: 0;
            right: 0;
            background-color: #dc3545;
            color: white;
            border-radius: 50%;
            width: 20px;
            height: 20px;
            display: flex;
            justify-content: center;
            align-items: center;
            font-size: 12px;
            font-weight: bold;
        }
        
        .notification-dropdown {
            position: absolute;
            top: 100%;
            right: 0;
            width: 350px;
            background: white;
            border: 1px solid #ddd;
            border-radius: 8px;
            box-shadow: 0 5px 15px rgba(0,0,0,0.2);
            z-index: 1000;
            display: none;
            max-height: 400px;
            overflow-y: auto;
        }
        
        .notification-dropdown.show {
            display: block;
            animation: fadeIn 0.3s;
        }
        
        .notification-header {
            padding: 15px;
            border-bottom: 1px solid #eee;
            display: flex;
            justify-content: space-between;
            align-items: center;
            background: #f8f9fa;
            border-radius: 8px 8px 0 0;
        }
        
        .notification-header h3 {
            margin: 0;
            font-size: 16px;
            color: #2c5cc7;
        }
        
        .mark-all-read {
            background: none;
            border: none;
            color: #2c5cc7;
            cursor: pointer;
            font-size: 13px;
        }
        
        .notification-list {
            padding: 0;
            margin: 0;
            list-style: none;
        }
        
        .notification-item {
            padding: 15px;
            border-bottom: 1px solid #f1f1f1;
            display: flex;
            align-items: flex-start;
            transition: background-color 0.2s;
        }
        
        .notification-item:hover {
            background-color: #f8f9fa;
        }
        
        .notification-item.unread {
            background-color: #e8f4ff;
        }
        
        .notification-icon {
            margin-right: 15px;
            font-size: 18px;
            color: #2c5cc7;
            flex-shrink: 0;
        }
        
        .notification-content {
            flex: 1;
        }
        
        .notification-title {
            font-weight: 600;
            margin-bottom: 5px;
            color: #333;
        }
        
        .notification-message {
            font-size: 14px;
            color: #666;
            margin-bottom: 5px;
        }
        
        .notification-time {
            font-size: 12px;
            color: #999;
        }
        
        .notification-empty {
            padding: 20px;
            text-align: center;
            color: #999;
        }
        
        .notification-footer {
            padding: 10px 15px;
            text-align: center;
            border-top: 1px solid #eee;
        }
        
        .view-all-link {
            color: #2c5cc7;
            text-decoration: none;
            font-size: 14px;
        }
        
        @keyframes fadeIn {
            from { opacity: 0; }
            to { opacity: 1; }
        }
    </style>
</head>
<body>
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
                                    <p class="profile-title"><?php echo substr($username,0,13) ?>..</p>
                                    <p class="profile-subtitle"><?php echo substr($useremail,0,22) ?></p>
                                </td>
                            </tr>
                            <tr>
                                <td colspan="2">
                                    <a href="../logout.php" ><input type="button" value="Se déconnecter" class="logout-btn btn-primary-soft btn"></a>
                                </td>
                            </tr>
                        </table>
                    </td>
                </tr>
                <tr class="menu-row" >
                    <td class="menu-btn menu-icon-home menu-active menu-icon-home-active" >
                        <a href="index.php" class="non-style-link-menu non-style-link-menu-active"><div><p class="menu-text">Accueil</p></a></div></a>
                    </td>
                </tr>
                <tr class="menu-row">
                    <td class="menu-btn menu-icon-doctor">
                        <a href="doctors.php" class="non-style-link-menu"><div><p class="menu-text">Tous les médecins</p></a></div>
                    </td>
                </tr>
                <tr class="menu-row" >
                    <td class="menu-btn menu-icon-session">
                        <a href="schedule.php" class="non-style-link-menu"><div><p class="menu-text">Séances programmées</p></div></a>
                    </td>
                </tr>
                <tr class="menu-row" >
                    <td class="menu-btn menu-icon-appoinment">
                        <a href="appointment.php" class="non-style-link-menu"><div><p class="menu-text">Mes réservations</p></a></div>
                    </td>
                </tr>
                
                <!-- NEW: Medical Record Option -->
                <tr class="menu-row">
                    <td class="menu-btn menu-icon-medical-record">
                        <a href="patient_medical_record.php" class="non-style-link-menu"><div><p class="menu-text">Dossier Médical</p></a></div>
                    </td>
                </tr>
                
                <tr class="menu-row" >
                    <td class="menu-btn menu-icon-settings">
                        <a href="settings.php" class="non-style-link-menu"><div><p class="menu-text">Paramètres</p></a></div>
                    </td>
                </tr>
            </table>
        </div>
        <div class="dash-body" style="margin-top: 15px">
            <table border="0" width="100%" style=" border-spacing: 0;margin:0;padding:0;" >
                <tr>
                    <td colspan="1" class="nav-bar" >
                        <p style="font-size: 23px;padding-left:12px;font-weight: 600;margin-left:20px;">Accueil</p>
                    </td>
                    <td width="25%"></td>
                    <td width="15%">
                        <p style="font-size: 14px;color: rgb(119, 119, 119);padding: 0;margin: 0;text-align: right;">
                            Date du jour
                        </p>
                        <p class="heading-sub12" style="padding: 0;margin: 0;">
                            <?php echo $today; ?>
                        </p>
                    </td>
                    <td width="5%">
                        <button class="btn-label" style="display: flex;justify-content: center;align-items: center;"><img src="../img/calendar.svg" width="100%"></button>
                    </td>
                    <td width="5%">
                        <!-- Notification Bell -->
                        <div class="notification-container">
                            <div class="notification-bell" id="notificationBell">
                                🔔
                                <?php if ($unread_count > 0): ?>
                                    <span class="notification-badge"><?php echo $unread_count; ?></span>
                                <?php endif; ?>
                            </div>
                            <div class="notification-dropdown" id="notificationDropdown">
                                <div class="notification-header">
                                    <h3>Notifications</h3>
                                    <button class="mark-all-read" id="markAllRead">Tout marquer comme lu</button>
                                </div>
                                <ul class="notification-list">
                                    <?php if (count($notifications) > 0): ?>
                                        <?php foreach ($notifications as $notification): ?>
                                            <li class="notification-item <?php echo $notification['is_read'] == 0 ? 'unread' : ''; ?>" data-id="<?php echo $notification['id']; ?>">
                                                <div class="notification-icon">
                                                    <?php 
                                                    $icon = '📋';
                                                    if (strpos($notification['type'], 'appointment') !== false) $icon = '📅';
                                                    if (strpos($notification['type'], 'prescription') !== false) $icon = '💊';
                                                    if (strpos($notification['type'], 'announcement') !== false) $icon = '📢';
                                                    if (strpos($notification['type'], 'medical') !== false) $icon = '📝';
                                                    echo $icon;
                                                    ?>
                                                </div>
                                                <div class="notification-content">
                                                    <div class="notification-title"><?php echo htmlspecialchars($notification['title']); ?></div>
                                                    <div class="notification-message"><?php echo htmlspecialchars($notification['message']); ?></div>
                                                    <div class="notification-time"><?php echo date('d M, Y H:i', strtotime($notification['created_at'])); ?></div>
                                                </div>
                                            </li>
                                        <?php endforeach; ?>
                                    <?php else: ?>
                                        <li class="notification-empty">Aucune notification</li>
                                    <?php endif; ?>
                                </ul>
                                <div class="notification-footer">
                                    <a href="patient_notifications.php" class="view-all-link">Voir toutes les notifications</a>
                                </div>
                            </div>
                        </div>
                    </td>
                </tr>
                <tr>
                    <td colspan="4" >
                        <center>
                            <table class="filter-container doctor-header patient-header" style="border: none;width:95%" border="0" >
                                <tr>
                                    <td >
                                        <h3>Bienvenue !</h3>
                                        <h1><?php echo $username ?>.</h1>
                                        <p>Vous n'avez pas d'idée sur les médecins ? Pas de problème, consultez la section 
                                            <a href="doctors.php" class="non-style-link"><b>"Tous les médecins"</b></a> ou 
                                            <a href="schedule.php" class="non-style-link"><b>"Séances"</b></a>.<br>
                                            Suivez l'historique de vos rendez-vous passés et futurs.<br>Découvrez également l'heure d'arrivée prévue de votre médecin ou consultant médical.<br><br>
                                        </p>
                                        <h3>Prendre rendez-vous avec un médecin ici</h3>
                                        <form action="schedule.php" method="post" style="display: flex">
                                            <input type="search" name="search" class="input-text " placeholder="Cherchez un médecin et nous trouverons les séances disponibles" list="doctors" style="width:45%;">&nbsp;&nbsp;
                                            <?php
                                                echo '<datalist id="doctors">';
                                                for ($y=0;$y<$list11->num_rows;$y++){
                                                    $row00=$list11->fetch_assoc();
                                                    $d=$row00["docname"];
                                                    echo "<option value='$d'><br/>";
                                                };
                                                echo ' </datalist>';
                                            ?>
                                            <input type="Submit" value="Rechercher" class="login-btn btn-primary btn" style="padding-left: 25px;padding-right: 25px;padding-top: 10px;padding-bottom: 10px;">
                                            <br><br>
                                        </form>
                                    </td>
                                </tr>
                            </table>
                        </center>
                    </td>
                </tr>
                <tr>
                    <td colspan="4">
                        <table border="0" width="100%">
                            <tr>
                                <td width="50%">
                                    <center>
                                        <table class="filter-container" style="border: none;" border="0">
                                            <tr>
                                                <td colspan="4">
                                                    <p style="font-size: 20px;font-weight:600;padding-left: 12px;">Statut</p>
                                                </td>
                                            </tr>
                                            <tr>
                                                <td style="width: 25%;">
                                                    <div class="dashboard-items" style="padding:20px;margin:auto;width:95%;display: flex">
                                                        <div>
                                                            <div class="h1-dashboard">
                                                                <?php echo $doctorrow_count->num_rows ?>
                                                            </div><br>
                                                            <div class="h3-dashboard">
                                                                Tous les médecins &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
                                                            </div>
                                                        </div>
                                                        <div class="btn-icon-back dashboard-icons" style="background-image: url('../img/icons/doctors-hover.svg');"></div>
                                                    </div>
                                                </td>
                                                <td style="width: 25%;">
                                                    <div class="dashboard-items" style="padding:20px;margin:auto;width:95%;display: flex;">
                                                        <div>
                                                            <div class="h1-dashboard">
                                                                <?php echo $patientrow_count->num_rows ?>
                                                            </div><br>
                                                            <div class="h3-dashboard">
                                                                Tous les patients &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
                                                            </div>
                                                        </div>
                                                        <div class="btn-icon-back dashboard-icons" style="background-image: url('../img/icons/patients-hover.svg');"></div>
                                                    </div>
                                                </td>
                                            </tr>
                                            <tr>
                                                <td style="width: 25%;">
                                                    <div class="dashboard-items" style="padding:20px;margin:auto;width:95%;display: flex; ">
                                                        <div>
                                                            <div class="h1-dashboard" >
                                                                <?php echo $appointmentrow_count->num_rows ?>
                                                            </div><br>
                                                            <div class="h3-dashboard" >
                                                                Nouvelles réservations &nbsp;&nbsp;
                                                            </div>
                                                        </div>
                                                        <div class="btn-icon-back dashboard-icons" style="margin-left: 0px;background-image: url('../img/icons/book-hover.svg');"></div>
                                                    </div>
                                                </td>
                                                <td style="width: 25%;">
                                                    <div class="dashboard-items" style="padding:20px;margin:auto;width:95%;display: flex;padding-top:21px;padding-bottom:21px;">
                                                        <div>
                                                            <div class="h1-dashboard">
                                                                <?php echo $schedulerow_count->num_rows ?>
                                                            </div><br>
                                                            <div class="h3-dashboard" style="font-size: 15px">
                                                                Séances aujourd'hui
                                                            </div>
                                                        </div>
                                                        <div class="btn-icon-back dashboard-icons" style="background-image: url('../img/icons/session-iceblue.svg');"></div>
                                                    </div>
                                                </td>
                                            </tr>
                                        </table>
                                    </center>
                                </td>
                                <td>
                                    <p style="font-size: 20px;font-weight:600;padding-left: 40px;" class="anime">Vos prochaines réservations</p>
                                    <center>
                                        <div class="abc scroll" style="height: 250px;padding: 0;margin: 0;">
                                            <table width="85%" class="sub-table scrolldown" border="0" >
                                                <thead>
                                                    <tr>
                                                        <th class="table-headin">
                                                            Numéro de rendez-vous
                                                        </th>
                                                        <th class="table-headin">
                                                            Titre de la séance
                                                        </th>
                                                        <th class="table-headin">
                                                            Médecin
                                                        </th>
                                                        <th class="table-headin">
                                                            Date et heure prévues
                                                        </th>
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                    <?php
                                                        $nextweek=date("Y-m-d",strtotime("+1 week"));
                                                        $sqlmain= "select * from schedule inner join appointment on schedule.scheduleid=appointment.scheduleid inner join patient on patient.pid=appointment.pid inner join doctor on schedule.docid=doctor.docid where patient.pid=$userid and schedule.scheduledate>='$today' order by schedule.scheduledate asc";
                                                        $result= $database->query($sqlmain);
                                                        
                                                        if($result->num_rows==0){
                                                            echo '<tr>
                                                            <td colspan="4">
                                                            <br><br><br><br>
                                                            <center>
                                                            <img src="../img/notfound.svg" width="25%">
                                                            <br>
                                                            <p class="heading-main12" style="margin-left: 45px;font-size:20px;color:rgb(49, 49, 49)">Rien à afficher ici !</p>
                                                            <a class="non-style-link" href="schedule.php"><button class="login-btn btn-primary-soft btn" style="display: flex;justify-content: center;align-items: center;margin-left:20px;">&nbsp; Prendre rendez-vous &nbsp;</button>
                                                            </a>
                                                            </center>
                                                            <br><br><br><br>
                                                            </td>
                                                            </tr>';
                                                        }
                                                        else{
                                                            for ( $x=0; $x<$result->num_rows;$x++){
                                                                $row=$result->fetch_assoc();
                                                                $scheduleid=$row["scheduleid"];
                                                                $title=$row["title"];
                                                                $apponum=$row["apponum"];
                                                                $docname=$row["docname"];
                                                                $scheduledate=$row["scheduledate"];
                                                                $scheduletime=$row["scheduletime"];
                                                                
                                                                echo '<tr>
                                                                    <td style="padding:30px;font-size:25px;font-weight:700;"> &nbsp;'.
                                                                    $apponum
                                                                    .'</td>
                                                                    <td style="padding:20px;"> &nbsp;'.
                                                                    substr($title,0,30)
                                                                    .'</td>
                                                                    <td>
                                                                    '.substr($docname,0,20).'
                                                                    </td>
                                                                    <td style="text-align:center;">
                                                                        '.substr($scheduledate,0,10).' '.substr($scheduletime,0,5).'
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
                    </td>
                <tr>
            </table>
        </div>
    </div>

    <!-- CHATBOT WIDGET -->
    <div id="chat-widget" class="chat-widget hidden">
        <div class="chat-header">
            <h4>🏥 DOCTO LINK Assistant IA</h4>
            <button id="chat-close">×</button>
        </div>
        <div id="chat-messages" class="chat-messages">
            <div class="message ai-message">
                Bonjour <?php echo $username; ?> ! Je suis votre assistant DOCTO LINK. Comment puis-je vous aider aujourd'hui ?
                <br><small><em>Note : Ceci est uniquement à titre informatif. Consultez toujours un professionnel de santé pour des conseils médicaux.</em></small>
            </div>
        </div>
        <div class="chat-input">
            <input type="text" id="chat-input" placeholder="Posez-moi une question..." />
            <button id="chat-send">Envoyer</button>
        </div>
    </div>
    <button id="chat-toggle" class="chat-toggle">💬 Aide IA</button>

    <style>
    /* Styles du chatbot */
    .chat-toggle {
        position: fixed;
        bottom: 20px;
        right: 20px;
        background: #007bff;
        color: white;
        border: none;
        border-radius: 25px;
        padding: 15px 20px;
        cursor: pointer;
        font-size: 14px;
        z-index: 1000;
        box-shadow: 0 2px 10px rgba(0,123,255,0.3);
    }
    .chat-widget {
        position: fixed;
        bottom: 80px;
        right: 20px;
        width: 350px;
        height: 450px;
        background: white;
        border: 1px solid #ddd;
        border-radius: 10px;
        box-shadow: 0 5px 15px rgba(0,0,0,0.3);
        z-index: 1001;
        display: flex;
        flex-direction: column;
    }
    .chat-widget.hidden {
        display: none;
    }
    .chat-header {
        background: #007bff;
        color: white;
        padding: 15px;
        border-radius: 10px 10px 0 0;
        display: flex;
        justify-content: space-between;
        align-items: center;
    }
    .chat-header h4 {
        margin: 0;
        font-size: 16px;
    }
    #chat-close {
        background: none;
        border: none;
        color: white;
        font-size: 20px;
        cursor: pointer;
    }
    .chat-messages {
        flex: 1;
        padding: 15px;
        overflow-y: auto;
        background: #f8f9fa;
    }
    .message {
        margin: 10px 0;
        padding: 10px;
        border-radius: 10px;
        max-width: 80%;
    }
    .user-message {
        background: #007bff;
        color: white;
        margin-left: auto;
        text-align: right;
    }
    .ai-message {
        background: white;
        border: 1px solid #ddd;
    }
    .chat-input {
        padding: 15px;
        border-top: 1px solid #ddd;
        display: flex;
        gap: 10px;
        align-items: center;
    }
    #chat-input {
        flex: 1;
        padding: 10px;
        border: 1px solid #ddd;
        border-radius: 20px;
        outline: none;
    }
    #chat-input:focus {
        border-color: #0056b3;
    }
    #chat-send {
        background: #007bff;
        color: white;
        border: none;
        padding: 10px 15px;
        border-radius: 20px;
        cursor: pointer;
    }
    #chat-send:hover {
        background: #0056b3;
    }
    </style>

    <script>
    // JavaScript Chatbot en français
    let chatOpen = false;
    document.getElementById('chat-toggle').addEventListener('click', function() {
        const widget = document.getElementById('chat-widget');
        const button = this;
        if (chatOpen) {
            widget.classList.add('hidden');
            button.textContent = '💬 Aide IA';
            chatOpen = false;
        } else {
            widget.classList.remove('hidden');
            button.textContent = '💬 Fermer';
            chatOpen = true;
            document.getElementById('chat-input').focus();
        }
    });
    document.getElementById('chat-close').addEventListener('click', function() {
        document.getElementById('chat-widget').classList.add('hidden');
        document.getElementById('chat-toggle').textContent = '💬 Aide IA';
        chatOpen = false;
    });
    document.getElementById('chat-send').addEventListener('click', envoyerMessage);
    document.getElementById('chat-input').addEventListener('keypress', function(e) {
        if (e.key === 'Enter') {
            envoyerMessage();
        }
    });

    function envoyerMessage() {
        const input = document.getElementById('chat-input');
        const message = input.value.trim();
        if (!message) return;

        ajouterMessage(message, 'user');
        input.value = '';

        fetch('../chatbot/chat.php', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
            },
            body: JSON.stringify({ message: message }),
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                ajouterMessage(data.response, 'ai');
            } else {
                ajouterMessage('❌ Erreur : ' + (data.error || 'Erreur inconnue du serveur.'), 'ai');
            }
        })
        .catch(error => {
            console.error('Erreur :', error);
            ajouterMessage('❌ Erreur de connexion. Vérifiez votre internet et réessayez.', 'ai');
        });
    }

    function ajouterMessage(text, sender) {
        const messagesDiv = document.getElementById('chat-messages');
        const messageDiv = document.createElement('div');
        messageDiv.className = `message ${sender}-message`;
        messageDiv.innerHTML = text;
        messagesDiv.appendChild(messageDiv);
        messagesDiv.scrollTop = messagesDiv.scrollHeight;
    }
    </script>
    
    <!-- Notification JavaScript -->
    <script>
    document.addEventListener('DOMContentLoaded', function() {
        const notificationBell = document.getElementById('notificationBell');
        const notificationDropdown = document.getElementById('notificationDropdown');
        const markAllReadBtn = document.getElementById('markAllRead');
        
        // Toggle notification dropdown
        notificationBell.addEventListener('click', function(e) {
            e.stopPropagation();
            notificationDropdown.classList.toggle('show');
        });
        
        // Close dropdown when clicking outside
        document.addEventListener('click', function(e) {
            if (!notificationBell.contains(e.target) && !notificationDropdown.contains(e.target)) {
                notificationDropdown.classList.remove('show');
            }
        });
        
        // Mark all as read
        markAllReadBtn.addEventListener('click', function() {
            const unreadItems = document.querySelectorAll('.notification-item.unread');
            const notificationIds = Array.from(unreadItems).map(item => item.getAttribute('data-id'));
            
            if (notificationIds.length > 0) {
                // Send AJAX request to mark notifications as read
                fetch('mark_notifications_read.php', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                    },
                    body: JSON.stringify({ notification_ids: notificationIds })
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        // Update UI
                        unreadItems.forEach(item => {
                            item.classList.remove('unread');
                        });
                        
                        // Update badge count
                        const badge = document.querySelector('.notification-badge');
                        if (badge) {
                            badge.remove();
                        }
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                });
            }
        });
        
        // Mark individual notification as read when clicked
        const notificationItems = document.querySelectorAll('.notification-item');
        notificationItems.forEach(item => {
            item.addEventListener('click', function() {
                if (this.classList.contains('unread')) {
                    const notificationId = this.getAttribute('data-id');
                    
                    // Send AJAX request to mark this notification as read
                    fetch('mark_notification_read.php', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                        },
                        body: JSON.stringify({ notification_id: notificationId })
                    })
                    .then(response => response.json())
                    .then(data => {
                        if (data.success) {
                            this.classList.remove('unread');
                            
                            // Update badge count
                            const badge = document.querySelector('.notification-badge');
                            if (badge) {
                                const count = parseInt(badge.textContent) - 1;
                                if (count > 0) {
                                    badge.textContent = count;
                                } else {
                                    badge.remove();
                                }
                            }
                        }
                    })
                    .catch(error => {
                        console.error('Error:', error);
                    });
                }
            });
        });
        
        // Check for new notifications periodically
        setInterval(checkNewNotifications, 30000); // Check every 30 seconds
    });
    
    function checkNewNotifications() {
        fetch('check_new_notifications.php')
            .then(response => response.json())
            .then(data => {
                if (data.count > 0) {
                    // Update badge
                    let badge = document.querySelector('.notification-badge');
                    if (!badge) {
                        badge = document.createElement('span');
                        badge.className = 'notification-badge';
                        document.getElementById('notificationBell').appendChild(badge);
                    }
                    badge.textContent = data.count;
                    
                    // Show notification alert
                    if (data.count === 1) {
                        showNotificationAlert('Vous avez ' + data.count + ' nouvelle notification');
                    } else {
                        showNotificationAlert('Vous avez ' + data.count + ' nouvelles notifications');
                    }
                }
            })
            .catch(error => {
                console.error('Error checking notifications:', error);
            });
    }
    
    function showNotificationAlert(message) {
        // Create and show a temporary alert
        const alert = document.createElement('div');
        alert.style.position = 'fixed';
        alert.style.top = '20px';
        alert.style.right = '20px';
        alert.style.backgroundColor = '#2c5cc7';
        alert.style.color = 'white';
        alert.style.padding = '15px 20px';
        alert.style.borderRadius = '5px';
        alert.style.zIndex = '10000';
        alert.style.boxShadow = '0 3px 10px rgba(0,0,0,0.2)';
        alert.textContent = message;
        
        document.body.appendChild(alert);
        
        // Remove after 3 seconds
        setTimeout(() => {
            alert.remove();
        }, 3000);
    }
    </script>
</body>
</html>