<?php
// Put ALL PHP code at the TOP - this fixes session errors
session_start();

if(isset($_SESSION["user"])){
    if(($_SESSION["user"])=="" or $_SESSION['usertype']!='d'){
        header("location: ../login.php");
        exit();
    }else{
        $useremail=$_SESSION["user"];
    }
}else{
    header("location: ../login.php");
    exit();
}

// Import database
include("../connection.php");
$userrow = $database->query("select * from doctor where docemail='$useremail'");
$userfetch=$userrow->fetch_assoc();
$userid= $userfetch["docid"];
$username=$userfetch["docname"];

// Get data for dashboard
date_default_timezone_set('Asia/Kolkata');
$today = date('Y-m-d');
$patientrow = $database->query("select * from patient;");
$doctorrow = $database->query("select * from doctor;");
$appointmentrow = $database->query("select * from appointment where appodate>='$today';");
$schedulerow = $database->query("select * from schedule where scheduledate='$today';");
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
        .dashbord-tables,.doctor-heade{
            animation: transitionIn-Y-over 0.5s;
        }
        .filter-container{
            animation: transitionIn-Y-bottom  0.5s;
        }
        .sub-table,#anim{
            animation: transitionIn-Y-bottom 0.5s;
        }
        .doctor-heade{
            animation: transitionIn-Y-over 0.5s;
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
                                    <p class="profile-title"><?php echo substr($username,0,13) ?>..</p>
                                    <p class="profile-subtitle"><?php echo substr($useremail,0,22) ?></p>
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
                    <td class="menu-btn menu-icon-appoinment">
                        <a href="appointment.php" class="non-style-link-menu"><div><p class="menu-text">Mes Rendez-vous</p></div></a>
                    </td>
                </tr>
                <tr class="menu-row">
                    <td class="menu-btn menu-icon-session">
                        <a href="schedule.php" class="non-style-link-menu"><div><p class="menu-text">Mes Sessions</p></div></a>
                    </td>
                </tr>
                <tr class="menu-row">
                    <td class="menu-btn menu-icon-patient">
                        <a href="patient.php" class="non-style-link-menu"><div><p class="menu-text">Mes Patients</p></div></a>
                    </td>
                </tr>
                <tr class="menu-row">
                    <td class="menu-btn menu-icon-settings">
                        <a href="settings.php" class="non-style-link-menu"><div><p class="menu-text">Paramètres</p></div></a>
                    </td>
                </tr>
            </table>
        </div>
        
        <div class="dash-body" style="margin-top: 15px">
            <table border="0" width="100%" style="border-spacing: 0;margin:0;padding:0;">
                <tr>
                    <td colspan="1" class="nav-bar">
                        <p style="font-size: 23px;padding-left:12px;font-weight: 600;margin-left:20px;">Tableau de bord</p>
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
                    <td width="10%">
                        <button class="btn-label" style="display: flex;justify-content: center;align-items: center;">
                            <img src="../img/calendar.svg" width="100%">
                        </button>
                    </td>
                </tr>
                
                <tr>
                    <td colspan="4">
                        <center>
                            <table class="filter-container doctor-header" style="border: none;width:95%" border="0">
                                <tr>
                                    <td>
                                        <h3>Bienvenue !</h3>
                                        <h1><?php echo $username ?>.</h1>
                                        <p>Merci de nous avoir rejoint. Nous nous efforçons toujours de vous fournir un service complet.<br>
                                        Vous pouvez consulter votre planning quotidien, et suivre les rendez-vous de vos patients !<br><br>
                                        </p>
                                        <a href="appointment.php" class="non-style-link">
                                            <button class="btn-primary btn" style="width:30%">Voir Mes Rendez-vous</button>
                                        </a>
                                        <br><br>
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
                                                                <?php echo $doctorrow->num_rows ?>
                                                            </div><br>
                                                            <div class="h3-dashboard">
                                                                Tous les Médecins &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
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
                                                                Tous les Patients &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
                                                            </div>
                                                        </div>
                                                        <div class="btn-icon-back dashboard-icons" style="background-image: url('../img/icons/patients-hover.svg');"></div>
                                                    </div>
                                                </td>
                                            </tr>
                                            <tr>
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
                                                    <div class="dashboard-items" style="padding:20px;margin:auto;width:95%;display: flex;padding-top:21px;padding-bottom:21px;">
                                                        <div>
                                                            <div class="h1-dashboard">
                                                                <?php echo $schedulerow->num_rows ?>
                                                            </div><br>
                                                            <div class="h3-dashboard" style="font-size: 15px">
                                                                Sessions du jour
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
                                    <p id="anim" style="font-size: 20px;font-weight:600;padding-left: 40px;">Vos prochaines sessions jusqu'à la semaine prochaine</p>
                                    <center>
                                        <div class="abc scroll" style="height: 250px;padding: 0;margin: 0;">
                                            <table width="85%" class="sub-table scrolldown" border="0">
                                                <thead>
                                                    <tr>
                                                        <th class="table-headin">Titre de la Session</th>
                                                        <th class="table-headin">Date prévue</th>
                                                        <th class="table-headin">Heure</th>
                                                    </tr>
                                                </thead>
                                                <tbody>
<?php
$nextweek = date("Y-m-d", strtotime("+1 week"));
$sqlmain = "select schedule.scheduleid, schedule.title, doctor.docname, schedule.scheduledate, schedule.scheduletime, schedule.nop from schedule inner join doctor on schedule.docid=doctor.docid where schedule.scheduledate>='$today' and schedule.scheduledate<='$nextweek' order by schedule.scheduledate desc";
$result = $database->query($sqlmain);

if($result->num_rows == 0){
    echo '<tr>
        <td colspan="4">
        <br><br><br><br>
        <center>
        <img src="../img/notfound.svg" width="25%">
        <br>
        <p class="heading-main12" style="margin-left: 45px;font-size:20px;color:rgb(49, 49, 49)">Nous n\'avons rien trouvé correspondant à vos mots-clés !</p>
        <a class="non-style-link" href="schedule.php"><button class="login-btn btn-primary-soft btn" style="display: flex;justify-content: center;align-items: center;margin-left:20px;">&nbsp; Afficher toutes les sessions &nbsp;</button>
        </a>
        </center>
        <br><br><br><br>
        </td>
    </tr>';
} else {
    for ($x = 0; $x < $result->num_rows; $x++) {
        $row = $result->fetch_assoc();
        $scheduleid = $row["scheduleid"];
        $title = $row["title"];
        $docname = $row["docname"];
        $scheduledate = $row["scheduledate"];
        $scheduletime = $row["scheduletime"];
        $nop = $row["nop"];
        echo '<tr>
            <td style="padding:20px;"> &nbsp;' . substr($title, 0, 30) . '</td>
            <td style="padding:20px;font-size:13px;">' . substr($scheduledate, 0, 10) . '</td>
            <td style="text-align:center;">' . substr($scheduletime, 0, 5) . '</td>
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
</tr>
</table>
</div>
</div>

<!-- CHATBOT SIMPLE INLINE -->
<div id="chat-widget" class="chat-widget hidden">
    <div class="chat-header">
        <h4>🏥 Assistant IA EDOC</h4>
        <button id="chat-close">×</button>
    </div>
    <div id="chat-messages" class="chat-messages">
        <div class="message ai-message">
            Bonjour Dr. <?php echo $username; ?> ! Je suis votre assistant EDOC. Comment puis-je vous aider aujourd'hui ?
        </div>
    </div>
    <div class="chat-input">
        <input type="text" id="chat-input" placeholder="Posez-moi une question..." />
        <button id="chat-send">Envoyer</button>
    </div>
</div>

<button id="chat-toggle" class="chat-toggle">💬 Aide IA</button>

<style>
/* Styles simples du Chatbot */
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
// JavaScript simple du Chatbot
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
    
    // Réponse simple de l'IA
    setTimeout(() => {
        const response = getAIResponse(message);
        addMessage(response, 'ai');
    }, 1000);
}

function addMessage(text, sender) {
    const messagesDiv = document.getElementById('chat-messages');
    const messageDiv = document.createElement('div');
    messageDiv.className = `message ${sender}-message`;
    messageDiv.textContent = text;
    messagesDiv.appendChild(messageDiv);
    messagesDiv.scrollTop = messagesDiv.scrollHeight;
}

function getAIResponse(message) {
    const msg = message.toLowerCase();
    
    if (msg.includes('rendez-vous') || msg.includes('booking')) {
        return 'Vous pouvez consulter vos rendez-vous en cliquant sur "Mes Rendez-vous" dans le menu. Voulez-vous de l\'aide sur un rendez-vous spécifique ?';
    }
    if (msg.includes('patient') || msg.includes('patients')) {
        return 'Vous pouvez voir tous vos patients dans la section "Mes Patients". Cela affiche les patients qui vous sont assignés.';
    }
    if (msg.includes('planning') || msg.includes('session')) {
        return 'Vos prochaines sessions sont affichées sur ce tableau de bord. Consultez "Mes Sessions" pour la gestion complète du planning.';
    }
    if (msg.includes('bonjour') || msg.includes('salut')) {
        return 'Bonjour Docteur ! Je suis là pour vous aider à naviguer dans EDOC et répondre à vos questions sur votre pratique.';
    }
    
    return 'Merci pour votre question ! Je peux vous aider avec les rendez-vous, la gestion des patients, les plannings et la navigation dans le système. Que souhaitez-vous savoir ?';
}
</script>
</body>
</html>
