<?php
// Put ALL PHP code at the TOP - this fixes session errors
session_start();

if(isset($_SESSION["user"])){
    if(($_SESSION["user"])=="" or $_SESSION['usertype']!='a'){ // Ensure this checks for 'a' for admin
        header("location: ../login.php");
        exit();
    }
}else{
    header("location: ../login.php");
    exit();
}

// Import database
include("../connection.php");

// Fetch admin details if needed, though the provided code uses static 'Administrator' and 'admin@edoc.com'
// For a dynamic admin name/email, you would fetch it here similar to the doctor dashboard.
// Example:
// $adminemail = $_SESSION["user"];
// $adminrow = $database->query("select * from admin_table where adminemail='$adminemail'"); // Assuming an admin table
// $adminfetch = $adminrow->fetch_assoc();
// $adminname = $adminfetch["adminname"];

// Get data for dashboard
date_default_timezone_set('Africa/Douala');
$today = date('Y-m-d');
$patientrow = $database->query("select * from patient;");
$doctorrow = $database->query("select * from doctor;");
$appointmentrow = $database->query("select * from appointment where appodate>='$today';");
$schedulerow = $database->query("select * from schedule where scheduledate='$today';");

// For the search datalist (doctors in admin panel)
$list11 = $database->query("select docname,docemail from doctor;");
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="../css/animations.css">      
    <link rel="stylesheet" href="../css/main.css">      
    <link rel="stylesheet" href="../css/admin.css">
<title>Tableau de bord</title>
    <style>
        .dashbord-tables, .filter-container, .sub-table {
            animation: transitionIn-Y-bottom 0.5s;
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
                                <td width="30%" style="padding-left:20px">
                                    <img src="../img/user.png" alt="" width="100%" style="border-radius:50%">
                                </td>
                                <td style="padding:0px;margin:0px;">
                                    <p class="profile-title">Administrateur</p>
                                    <p class="profile-subtitle">admin@edoc.com</p>
                                </td>
                            </tr>
                            <tr>
                                <td colspan="2">
                                    <a href="../logout.php"><input type="button" value="Se déconnecter" class="logout-btn btn-primary-soft btn"></a>
                                </td>
                            </tr>
                        </table>
                    </td>
                </tr>
                <tr class="menu-row">
                    <td class="menu-btn menu-icon-dashbord menu-active menu-icon-dashbord-active">
                        <a href="index.php" class="non-style-link-menu non-style-link-menu-active"><div><p class="menu-text">Tableau de bord</p></div></a>
                    </td>
                </tr>
                <tr class="menu-row">
                    <td class="menu-btn menu-icon-doctor ">
                        <a href="doctors.php" class="non-style-link-menu"><div><p class="menu-text">Médecins</p></div></a>
                    </td>
                </tr>
                <tr class="menu-row">
                    <td class="menu-btn menu-icon-schedule">
                        <a href="schedule.php" class="non-style-link-menu"><div><p class="menu-text">Horaire</p></div></a>
                    </td>
                </tr>
                <tr class="menu-row">
                    <td class="menu-btn menu-icon-appoinment">
                        <a href="appointment.php" class="non-style-link-menu"><div><p class="menu-text">Rendez-vous</p></div></a>
                    </td>
                </tr>
                <tr class="menu-row">
                    <td class="menu-btn menu-icon-patient">
                        <a href="patient.php" class="non-style-link-menu"><div><p class="menu-text">Patients</p></div></a>
                    </td>
                </tr>
            </table>
        </div>
        <div class="dash-body" style="margin-top: 15px">
            <table border="0" width="100%" style="border-spacing: 0;margin:0;padding:0;">
                <tr>
                    <td colspan="2" class="nav-bar">
                        <form action="doctors.php" method="post" class="header-search">
                            <input type="search" name="search" class="input-text header-searchbar" placeholder="Rechercher le nom ou l'email du médecin" list="doctors">&nbsp;&nbsp;
                            <datalist id="doctors">
                                <?php 
                                for ($y = 0; $y < $list11->num_rows; $y++) {
                                    $row00 = $list11->fetch_assoc();
                                    $d = $row00["docname"];
                                    $c = $row00["docemail"];
                                    echo "<option value='$d'><br/>";
                                    echo "<option value='$c'><br/>";
                                };
                                ?>
                            </datalist>
                            <input type="Submit" value="Rechercher" class="login-btn btn-primary-soft btn" style="padding-left: 25px;padding-right: 25px;padding-top: 10px;padding-bottom: 10px;">
                        </form>
                    </td>
                    <td width="15%">
                        <p style="font-size: 14px;color: rgb(119, 119, 119);padding: 0;margin: 0;text-align: right;">
                            Date d'aujourd'hui
                        </p>
                        <p class="heading-sub12" style="padding: 0;margin: 0;">
                            <?php echo $today; ?>
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
                                    <td colspan="4">
                                        <p style="font-size: 20px;font-weight:600;padding-left: 12px;">Statut</p>
                                    </td>
                                </tr>
                                <tr>
                                    <td style="width: 25%;">
                                        <div class="dashboard-items" style="padding:20px;margin:auto;width:95%;display: flex">
                                            <div>
                                                <div class="h1-dashboard">
                                                    <?php echo $doctorrow->num_rows ?>
                                                </div><br>
                                                <div class="h3-dashboard">
                                                    Médecins &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
                                                </div>
                                            </div>
                                            <div class="btn-icon-back dashboard-icons" style="background-image: url('../img/icons/doctors-hover.svg');"></div>
                                        </div>
                                    </td>
                                    <td style="width: 25%;">
                                        <div class="dashboard-items" style="padding:20px;margin:auto;width:95%;display: flex;">
                                            <div>
                                                <div class="h1-dashboard">
                                                    <?php echo $patientrow->num_rows ?>
                                                </div><br>
                                                <div class="h3-dashboard">
                                                    Patients &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
                                                </div>
                                            </div>
                                            <div class="btn-icon-back dashboard-icons" style="background-image: url('../img/icons/patients-hover.svg');"></div>
                                        </div>
                                    </td>
<td style="width: 25%;">
                                        <div class="dashboard-items" style="padding:20px;margin:auto;width:95%;display: flex;">
                                            <div>
                                                <div class="h1-dashboard">
                                                    <?php echo $appointmentrow->num_rows ?>
                                                </div><br>
                                                <div class="h3-dashboard">
                                                    Nouvelles Réservations &nbsp;&nbsp;
                                                </div>
                                            </div>
                                            <div class="btn-icon-back dashboard-icons" style="margin-left: 0px;background-image: url('../img/icons/book-hover.svg');"></div>
                                        </div>
                                    </td>
                                    <td style="width: 25%;">
                                        <div class="dashboard-items" style="padding:20px;margin:auto;width:95%;display: flex;padding-top:26px;padding-bottom:26px;">
                                            <div>
                                                <div class="h1-dashboard">
                                                    <?php echo $schedulerow->num_rows ?>
                                                </div><br>
                                                <div class="h3-dashboard" style="font-size: 15px">
                                                    Séances d'Aujourd'hui
                                                </div>
                                            </div>
                                            <div class="btn-icon-back dashboard-icons" style="background-image: url('../img/icons/session-iceblue.svg');"></div>
                                        </div>
                                    </td>
                                </tr>
                            </table>
                        </center>
                    </td>
                </tr>
                <tr>
                    <td colspan="4">
                        <table width="100%" border="0" class="dashbord-tables">
                            <tr>
                                <td>
                                    <p style="padding:10px;padding-left:48px;padding-bottom:0;font-size:23px;font-weight:700;color:var(--primarycolor);">
                                        Rendez-vous à venir jusqu'à <?php echo date("l",strtotime("+1 week")); ?>
                                    </p>
                                    <p style="padding-bottom:19px;padding-left:50px;font-size:15px;font-weight:500;color:#212529e3;line-height: 20px;">
                                        Voici un accès rapide aux Rendez-vous à venir jusqu'à 7 jours<br>
                                        Plus de détails disponibles dans la section @Rendez-vous.
                                    </p>
                                </td>
                                <td>
                                    <p style="text-align:right;padding:10px;padding-right:48px;padding-bottom:0;font-size:23px;font-weight:700;color:var(--primarycolor);">
                                        Séances à venir jusqu'à <?php echo date("l",strtotime("+1 week")); ?>
                                    </p>
                                    <p style="padding-bottom:19px;text-align:right;padding-right:50px;font-size:15px;font-weight:500;color:#212529e3;line-height: 20px;">
                                        Voici un accès rapide aux Séances à venir programmées jusqu'à 7 jours<br>
                                        Ajouter, supprimer et de nombreuses fonctionnalités disponibles dans la section @Horaire.
                                    </p>
                                </td>
                            </tr>
                            <tr>
                                <td width="50%">
                                    <center>
                                        <div class="abc scroll" style="height: 200px;">
                                            <table width="85%" class="sub-table scrolldown" border="0">
                                                <thead>
                                                    <tr> 
                                                        <th class="table-headin" style="font-size: 12px;">
                                                            Numéro de rendez-vous
                                                        </th>
                                                        <th class="table-headin">
                                                            Nom du patient
                                                        </th>
                                                        <th class="table-headin">
                                                            Docteur
                                                        </th>
                                                        <th class="table-headin">
                                                            Séance
                                                        </th>
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                    <?php
                                                    $nextweek = date("Y-m-d",strtotime("+1 week"));
                                                    $sqlmain = "select appointment.appoid,schedule.scheduleid,schedule.title,doctor.docname,patient.pname,schedule.scheduledate,schedule.scheduletime,appointment.apponum,appointment.appodate from schedule inner join appointment on schedule.scheduleid=appointment.scheduleid inner join patient on patient.pid=appointment.pid inner join doctor on schedule.docid=doctor.docid where schedule.scheduledate>='$today' and schedule.scheduledate<='$nextweek' order by schedule.scheduledate desc";
                                                    $result = $database->query($sqlmain);
                                                    
                                                    if($result->num_rows == 0){
                                                        echo '<tr>
                                                        <td colspan="4">
                                                        <br><br><br><br>
                                                        <center>
                                                        <img src="../img/notfound.svg" width="25%">
                                                        <br>
                                                        <p class="heading-main12" style="margin-left: 45px;font-size:20px;color:rgb(49, 49, 49)">Nous n\'avons trouvé aucun résultat correspondant à vos mots-clés !</p>
                                                        <a class="non-style-link" href="appointment.php"><button class="login-btn btn-primary-soft btn" style="display: flex;justify-content: center;align-items: center;margin-left:20px;">&nbsp; Montrer tous les Rendez-vous &nbsp;</button>
                                                        </a>
                                                        </center>
                                                        <br><br><br><br>
                                                        </td>
                                                        </tr>';
                                                    }
                                                    else{
                                                        for ($x = 0; $x < $result->num_rows; $x++) {
                                                            $row = $result->fetch_assoc();
                                                            $appoid = $row["appoid"];
                                                            $scheduleid = $row["scheduleid"];
                                                            $title = $row["title"];
                                                            $docname = $row["docname"];
                                                            $scheduledate = $row["scheduledate"];
                                                            $scheduletime = $row["scheduletime"];
                                                            $pname = $row["pname"];
                                                            $apponum = $row["apponum"];
                                                            $appodate = $row["appodate"];
                                                            echo '<tr>
                                                                <td style="text-align:center;font-size:23px;font-weight:500; color: var(--btnnicetext);padding:20px;">
                                                                    '.$apponum.'
                                                                </td>
                                                                <td style="font-weight:600;"> &nbsp;'.
                                                                substr($pname, 0, 25)
                                                                .'</td>
                                                                <td style="font-weight:600;"> &nbsp;'.
                                                                substr($docname, 0, 25)
                                                                .'</td>
                                                                <td>
                                                                    '.substr($title, 0, 15).'
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
                                <td width="50%" style="padding: 0;">
                                    <center>
                                        <div class="abc scroll" style="height: 200px;padding: 0;margin: 0;">
                                            <table width="85%" class="sub-table scrolldown" border="0">
                                                <thead>
                                                    <tr>
                                                        <th class="table-headin">
                                                            Titre de la Séance
                                                        </th>
                                                        <th class="table-headin">
                                                            Docteur
                                                        </th>
                                                        <th class="table-headin">
                                                            Date & Heure programmées
                                                        </th>
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                    <?php
                                                    $nextweek = date("Y-m-d",strtotime("+1 week"));
                                                    $sqlmain = "select schedule.scheduleid,schedule.title,doctor.docname,schedule.scheduledate,schedule.scheduletime,schedule.nop from schedule inner join doctor on schedule.docid=doctor.docid where schedule.scheduledate>='$today' and schedule.scheduledate<='$nextweek' order by schedule.scheduledate desc";
                                                    $result = $database->query($sqlmain);
                                                    
                                                    if($result->num_rows == 0){
                                                        echo '<tr>
                                                        <td colspan="4">
                                                        <br><br><br><br>
                                                        <center>
                                                        <img src="../img/notfound.svg" width="25%">
                                                        <br>
                                                        <p class="heading-main12" style="margin-left: 45px;font-size:20px;color:rgb(49, 49, 49)">Nous n\'avons trouvé aucun résultat correspondant à vos mots-clés !</p>
                                                        <a class="non-style-link" href="schedule.php"><button class="login-btn btn-primary-soft btn" style="display: flex;justify-content: center;align-items: center;margin-left:20px;">&nbsp; Montrer toutes les Séances &nbsp;</button>
                                                        </a>
                                                        </center>
                                                        <br><br><br><br>
                                                        </td>
                                                        </tr>';
                                                    }
                                                    else{
                                                        for ($x = 0; $x < $result->num_rows; $x++) {
                                                            $row = $result->fetch_assoc();
                                                            $scheduleid = $row["scheduleid"];
                                                            $title = $row["title"];
                                                            $docname = $row["docname"];
                                                            $scheduledate = $row["scheduledate"];
                                                            $scheduletime = $row["scheduletime"];
                                                            $nop = $row["nop"];
                                                            echo '<tr>
                                                                <td style="padding:20px;"> &nbsp;'.
                                                                substr($title, 0, 30)
                                                                .'</td>
                                                                <td>
                                                                '.substr($docname, 0, 20).'
                                                                </td>
                                                                <td style="text-align:center;">
                                                                    '.substr($scheduledate, 0, 10).' '.substr($scheduletime, 0, 5).'
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
                            <tr>
                                <td>
                                    <center>
                                        <a href="appointment.php" class="non-style-link"><button class="btn-primary btn" style="width:85%">Montrer tous les Rendez-vous</button></a>
                                    </center>
                                </td>
                                <td>
                                    <center>
                                        <a href="schedule.php" class="non-style-link"><button class="btn-primary btn" style="width:85%">Montrer toutes les Séances</button></a>
                                    </center>
                                </td>
                            </tr>
                        </table>
                    </td>
                </tr>
            </table>
        </div>
    </div>

<!-- AJOUTER LE CHATBOT ICI - Version simple en ligne -->
    <div id="chat-widget" class="chat-widget hidden">
        <div class="chat-header">
            <h4>🏥 Assistant Virtuel</h4>
            <button id="chat-close">×</button>
        </div>
        <div id="chat-messages" class="chat-messages">
            <div class="message ai-message">
                Bonjour Administrateur ! Je suis votre assistant DOCTOLINK. Comment puis-je vous aider aujourd'hui ?
                <br><small><em>Note : Ceci est à des fins d'information uniquement. Consultez toujours des professionnels de la santé pour des conseils médicaux.</em></small>
            </div>
        </div>
        <div class="chat-input">
            <input type="text" id="chat-input" placeholder="Posez-moi n'importe quelle question..." />
            <button id="chat-send">Envoyer</button>
        </div>
    </div>
    <button id="chat-toggle" class="chat-toggle">💬 Aide IA</button>

    <style>
    /* Styles simples pour le Chatbot */
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
    // JavaScript simple pour le Chatbot
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
    document.getElementById('chat-send').addEventListener('click', sendMessage);
    document.getElementById('chat-input').addEventListener('keypress', function(e) {
        if (e.key === 'Enter') {
            sendMessage();
        }
    });
    function sendMessage() {
        const input = document.getElementById('chat-input');
        const message = input.value.trim();
        if (!message) return;
        // Ajouter le message de l'utilisateur
        addMessage(message, 'user');
        input.value = '';
        // Réponse simple de l'IA (en utilisant la logique basée sur les mots-clés)
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
                addMessage(data.response, 'ai');
            } else {
                addMessage('❌ Erreur : ' + (data.error || 'Erreur inconnue du serveur.'), 'ai');
            }
        })
        .catch(error => {
            console.error('Erreur:', error);
            addMessage('❌ Erreur de connexion. Veuillez vérifier votre connexion Internet et réessayer.', 'ai');
        });
    }
    function addMessage(text, sender) {
        const messagesDiv = document.getElementById('chat-messages');
        const messageDiv = document.createElement('div');
        messageDiv.className = `message ${sender}-message`;
        messageDiv.innerHTML = text; // Utiliser innerHTML pour permettre les petites balises dans la réponse de l'IA
        messagesDiv.appendChild(messageDiv);
        messagesDiv.scrollTop = messagesDiv.scrollHeight;
    }
    </script>
</body>
</html>