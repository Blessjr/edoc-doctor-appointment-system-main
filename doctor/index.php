<?php
// Put ALL PHP code at the TOP - this fixes session errors
session_start();

// Initialize all variables with default values to prevent undefined variable errors
$username = "Docteur";
$useremail = "email@exemple.com";
$today = date('Y-m-d');
$doctor_count = 0;
$patient_count = 0;
$appointment_count = 0;
$prescription_count = 0;
$notification_count = 0;
$recent_prescriptions = array();
$notifications = array();
$userid = 0;

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

// Check database connection
if ($database) {
    // Get doctor information
    $userrow = $database->query("select * from doctor where docemail='$useremail'");
    if ($userrow) {
        $userfetch = $userrow->fetch_assoc();
        $userid = $userfetch["docid"];
        $username = $userfetch["docname"];
    }

    // Get data for dashboard
    date_default_timezone_set('Asia/Kolkata');

    // Get patient count
    $patient_result = $database->query("select * from patient");
    if ($patient_result) {
        $patient_count = $patient_result->num_rows;
    }

    // Get doctor count
    $doctor_result = $database->query("select * from doctor");
    if ($doctor_result) {
        $doctor_count = $doctor_result->num_rows;
    }

    // Get appointment count
    $appointment_result = $database->query("select * from appointment where appodate>='$today'");
    if ($appointment_result) {
        $appointment_count = $appointment_result->num_rows;
    }

    // Check for upcoming appointments in the next 30 minutes
    $current_time = date('H:i:s');
    $thirty_min_later = date('H:i:s', strtotime('+30 minutes'));

    $notification_query = $database->query("
        SELECT a.*, p.pname 
        FROM appointment a 
        INNER JOIN patient p ON a.pid = p.pid 
        WHERE a.docid = '$userid' 
        AND a.appodate = '$today' 
        AND a.appotime BETWEEN '$current_time' AND '$thirty_min_later'
        AND a.status != 'Completed'
    ");

    if ($notification_query) {
        $notification_count = $notification_query->num_rows;
        while ($row = $notification_query->fetch_assoc()) {
            $notifications[] = $row;
        }
    }

    // Get recent prescriptions (last 5)
    $prescription_result = $database->query("
        SELECT p.*, pt.pname as patient_name 
        FROM prescriptions p 
        INNER JOIN patient pt ON p.patient_id = pt.pid 
        WHERE p.doctor_id = '$userid' 
        ORDER BY p.prescription_date DESC 
        LIMIT 5
    ");

    if ($prescription_result) {
        while ($row = $prescription_result->fetch_assoc()) {
            $recent_prescriptions[] = $row;
        }
    }

    // Get prescription count
    $prescription_count_result = $database->query("
        SELECT COUNT(*) as count 
        FROM prescriptions 
        WHERE doctor_id = '$userid'
    ");
    if ($prescription_count_result) {
        $count_data = $prescription_count_result->fetch_assoc();
        $prescription_count = $count_data['count'];
    }
} else {
    $db_error = "Erreur de connexion à la base de données";
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
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
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
        
        /* New Sidebar Styles based on the image */
        .menu {
            width: 280px;
            background: #f8f9fa;
            color: #333;
            height: 100vh;
            position: fixed;
            overflow-y: auto;
            z-index: 1000;
            padding: 0;
            border-right: 1px solid #e0e0e0;
            box-shadow: 2px 0 5px rgba(0,0,0,0.05);
        }
        
        .menu-container {
            width: 100%;
            border: none;
        }
        
        .profile-container {
            width: 100%;
            padding: 25px 20px;
            text-align: center;
            background: white;
            border-bottom: 1px solid #e0e0e0;
        }
        
        .profile-title {
            font-size: 18px;
            font-weight: 600;
            color: #2c5cc7;
            margin: 15px 0 5px;
        }
        
        .profile-subtitle {
            font-size: 14px;
            color: #666;
            margin: 0 0 20px;
        }
        
        .logout-btn {
            width: 100%;
            padding: 10px;
            background: #2c5cc7;
            color: white;
            border: none;
            border-radius: 4px;
            cursor: pointer;
            font-size: 14px;
            transition: all 0.3s;
            font-weight: 500;
        }
        
        .logout-btn:hover {
            background: #1e4bb5;
        }
        
        .menu-row {
            border: none;
        }
        
        .menu-btn {
            padding: 15px 25px;
            display: flex;
            align-items: center;
            color: #555;
            text-decoration: none;
            transition: all 0.3s ease;
            width: 100%;
            border-left: 4px solid transparent;
            background: transparent;
        }
        
        .menu-btn:hover {
            background: #e9ecef;
            color: #2c5cc7;
        }
        
        .menu-active {
            background: #e3f2fd;
            color: #2c5cc7;
            border-left-color: #2c5cc7;
            font-weight: 600;
        }
        
        .menu-btn i {
            width: 24px;
            margin-right: 15px;
            font-size: 18px;
            text-align: center;
            color: #666;
        }
        
        .menu-active i, .menu-btn:hover i {
            color: #2c5cc7;
        }
        
        .menu-text {
            font-size: 16px;
            font-weight: 500;
        }
        
        /* Improved Dashboard Items */
        .dashboard-items {
            padding: 20px;
            margin: auto;
            width: 95%;
            display: flex;
            border-radius: 10px;
            background: white;
            box-shadow: 0 4px 10px rgba(0,0,0,0.08);
            transition: all 0.3s ease;
        }
        
        .dashboard-items:hover {
            transform: translateY(-3px);
            box-shadow: 0 6px 15px rgba(0,0,0,0.12);
        }
        
        .h1-dashboard {
            font-size: 32px;
            font-weight: 700;
            color: #2c5cc7;
            margin-bottom: 5px;
        }
        
        .h3-dashboard {
            font-size: 14px;
            color: #6c757d;
            font-weight: 500;
        }
        
        .btn-icon-back {
            width: 60px;
            height: 60px;
            border-radius: 12px;
            display: flex;
            align-items: center;
            justify-content: center;
            background: #e0e8ff;
            margin-left: 15px;
        }
        
        .btn-icon-back i {
            font-size: 24px;
            color: #2c5cc7;
        }
        
        /* Improved Prescriptions Card */
        .prescriptions-card {
            background: white;
            border-radius: 10px;
            box-shadow: 0 4px 10px rgba(0,0,0,0.08);
            padding: 25px;
            margin: 20px 0;
        }
        
        .prescriptions-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 20px;
            padding-bottom: 15px;
            border-bottom: 1px solid #eee;
        }
        
        .prescriptions-title {
            font-size: 20px;
            font-weight: 600;
            color: #2c5cc7;
        }
        
        .view-all-link {
            color: #2c5cc7;
            text-decoration: none;
            font-weight: 500;
            font-size: 14px;
        }
        
        .view-all-link:hover {
            text-decoration: underline;
        }
        
        .prescriptions-table {
            width: 100%;
            border-collapse: collapse;
        }
        
        .prescriptions-table th {
            text-align: left;
            padding: 15px;
            background: #f8f9fa;
            font-weight: 600;
            color: #495057;
            border-bottom: 2px solid #e9ecef;
        }
        
        .prescriptions-table td {
            padding: 15px;
            border-bottom: 1px solid #e9ecef;
            color: #495057;
        }
        
        .prescriptions-table tr:last-child td {
            border-bottom: none;
        }
        
        .prescriptions-table tr:hover {
            background: #f8f9fa;
        }
        
        .empty-prescriptions {
            text-align: center;
            padding: 40px 20px;
            color: #6c757d;
        }
        
        .empty-prescriptions i {
            font-size: 50px;
            color: #dee2e6;
            margin-bottom: 15px;
        }
        
        /* Notification Styles */
        .notification-container {
            position: relative;
            display: inline-block;
            margin-right: 20px;
        }
        
        .notification-bell {
            font-size: 20px;
            cursor: pointer;
            position: relative;
            color: #6c757d;
            width: 42px;
            height: 42px;
            display: flex;
            align-items: center;
            justify-content: center;
            border-radius: 50%;
            transition: all 0.3s ease;
        }
        
        .notification-bell:hover {
            background: #f8f9fa;
            color: #2c5cc7;
        }
        
        .notification-count {
            position: absolute;
            top: -5px;
            right: -5px;
            background-color: #ff4757;
            color: white;
            border-radius: 50%;
            width: 20px;
            height: 20px;
            font-size: 12px;
            display: flex;
            justify-content: center;
            align-items: center;
            font-weight: bold;
            border: 2px solid white;
        }
        
        .notification-dropdown {
            position: absolute;
            top: 50px;
            right: 0;
            width: 320px;
            background: white;
            border-radius: 8px;
            box-shadow: 0 5px 15px rgba(0,0,0,0.2);
            z-index: 1000;
            display: none;
            max-height: 400px;
            overflow-y: auto;
        }
        
        .notification-dropdown.active {
            display: block;
            animation: fadeIn 0.3s;
        }
        
        .notification-header {
            padding: 15px;
            border-bottom: 1px solid #eee;
            font-weight: bold;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }
        
        .notification-item {
            padding: 12px 15px;
            border-bottom: 1px solid #f1f1f1;
            cursor: pointer;
            transition: background 0.2s;
        }
        
        .notification-item:hover {
            background: #f9f9f9;
        }
        
        .notification-item.unread {
            background: #f0f7ff;
        }
        
        .notification-time {
            font-size: 12px;
            color: #888;
            margin-top: 5px;
        }
        
        .notification-empty {
            padding: 20px;
            text-align: center;
            color: #888;
        }
        
        .mark-read {
            color: #007bff;
            font-size: 12px;
            cursor: pointer;
        }
        
        @keyframes fadeIn {
            from { opacity: 0; }
            to { opacity: 1; }
        }
        
        @keyframes pulse {
           0% { transform: scale(1); }
           50% { transform: scale(1.1); }
           100% { transform: scale(1); }
        }
        
        .pulse {
            animation: pulse 0.5s ease-in-out;
        }
        
        /* Audio element for notification sound */
        #notification-sound {
            display: none;
        }
        
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
        
        /* Main content adjustment for new sidebar */
        .dash-body {
            margin-left: 280px;
            width: calc(100% - 280px);
            padding: 20px;
            background: #f5f7fa;
            min-height: 100vh;
        }
        
        @media (max-width: 992px) {
            .menu {
                width: 230px;
            }
            
            .dash-body {
                margin-left: 230px;
                width: calc(100% - 230px);
            }
        }
        
        @media (max-width: 768px) {
            .menu {
                transform: translateX(-100%);
                transition: transform 0.3s ease;
            }
            
            .menu.active {
                transform: translateX(0);
            }
            
            .dash-body {
                margin-left: 0;
                width: 100%;
            }
            
            .menu-toggle {
                display: block;
                position: fixed;
                top: 20px;
                left: 20px;
                z-index: 999;
                background: #2c5cc7;
                color: white;
                border: none;
                width: 40px;
                height: 40px;
                border-radius: 50%;
                font-size: 20px;
                cursor: pointer;
                box-shadow: 0 2px 10px rgba(0, 0, 0, 0.2);
            }
        }
    </style>
</head>
<body>
    <button class="menu-toggle" style="display: none;">
        <i class="fas fa-bars"></i>
    </button>

    <div class="container">
        <div class="menu">
            <table class="menu-container" border="0">
                <tr>
                    <td style="padding:10px" colspan="2">
                        <table border="0" class="profile-container">
                            <tr>
                                <td width="100%" style="padding-left:20px; text-align: center;">
                                    <img src="../img/user.png" alt="" width="80" style="border-radius:50%">
                                </td>
                            </tr>
                            <tr>
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
                    <td class="menu-btn menu-active">
                        <a href="index.php" class="non-style-link-menu non-style-link-menu-active">
                            <i class="fas fa-chart-line"></i>
                            <p class="menu-text">Tableau de bord</p>
                        </a>
                    </td>
                </tr>
                <tr class="menu-row">
                    <td class="menu-btn">
                        <a href="appointment.php" class="non-style-link-menu">
                            <i class="fas fa-calendar-check"></i>
                            <p class="menu-text">Mes Rendez-vous</p>
                        </a>
                    </td>
                </tr>
                <tr class="menu-row">
                    <td class="menu-btn">
                        <a href="schedule.php" class="non-style-link-menu">
                            <i class="fas fa-clock"></i>
                            <p class="menu-text">Mes Séances</p>
                        </a>
                    </td>
                </tr>
                <tr class="menu-row">
                    <td class="menu-btn">
                        <a href="patient.php" class="non-style-link-menu">
                            <i class="fas fa-user-injured"></i>
                            <p class="menu-text">Mes Patients</p>
                        </a>
                    </td>
                </tr>
                <tr class="menu-row">
                    <td class="menu-btn">
                        <a href="settings.php" class="non-style-link-menu">
                            <i class="fas fa-cog"></i>
                            <p class="menu-text">Paramètres</p>
                        </a>
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
                    <td width="5%">
                        <!-- Notification Bell -->
                        <div class="notification-container">
                            <div class="notification-bell" id="notificationBell">
                                <i class="fas fa-bell"></i>
                                <?php if ($notification_count > 0): ?>
                                <span class="notification-count" id="notificationCount"><?php echo $notification_count; ?></span>
                                <?php endif; ?>
                            </div>
                            <div class="notification-dropdown" id="notificationDropdown">
                                <div class="notification-header">
                                    <span>Notifications</span>
                                    <span class="mark-read" id="markAllRead">Tout marquer comme lu</span>
                                </div>
                                <div id="notificationList">
                                    <?php if ($notification_count > 0): ?>
                                        <?php foreach ($notifications as $notif): ?>
                                            <div class="notification-item unread" data-id="<?php echo $notif['appoid']; ?>">
                                                <strong>Rendez-vous imminent</strong><br>
                                                Patient: <?php echo $notif['pname']; ?><br>
                                                À: <?php echo date('H:i', strtotime($notif['appotime'])); ?>
                                                <div class="notification-time">Dans <?php echo round((strtotime($notif['appotime']) - time()) / 60); ?> minutes</div>
                                            </div>
                                        <?php endforeach; ?>
                                    <?php else: ?>
                                        <div class="notification-empty">Aucune notification</div>
                                    <?php endif; ?>
                                </div>
                            </div>
                        </div>
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
                                        <a href="dossier_medical.php" class="non-style-link">
                                            <button class="btn-primary btn" style="width:30%; margin-left: 10px;">Dossiers Médicaux</button>
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
                                                    <div class="dashboard-items">
                                                        <div>
                                                            <div class="h1-dashboard">
                                                                <?php echo $doctor_count ?>
                                                            </div><br>
                                                            <div class="h3-dashboard">
                                                                Tous les Médecins &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
                                                            </div>
                                                        </div>
                                                        <div class="btn-icon-back">
                                                            <i class="fas fa-user-md"></i>
                                                        </div>
                                                    </div>
                                                </td>
                                                <td style="width: 25%;">
                                                    <div class="dashboard-items">
                                                        <div>
                                                            <div class="h1-dashboard">
                                                                <?php echo $patient_count ?>
                                                            </div><br>
                                                            <div class="h3-dashboard">
                                                                Tous les Patients &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
                                                            </div>
                                                        </div>
                                                        <div class="btn-icon-back">
                                                            <i class="fas fa-users"></i>
                                                        </div>
                                                    </div>
                                                </td>
                                            </tr>
                                            <tr>
                                                <td style="width: 25%;">
                                                    <div class="dashboard-items">
                                                        <div>
                                                            <div class="h1-dashboard">
                                                                <?php echo $appointment_count ?>
                                                            </div><br>
                                                            <div class="h3-dashboard">
                                                                Nouvelles Réservations &nbsp;&nbsp;
                                                            </div>
                                                        </div>
                                                        <div class="btn-icon-back">
                                                            <i class="fas fa-calendar"></i>
                                                        </div>
                                                    </div>
                                                </td>
                                                <td style="width: 25%;">
                                                    <div class="dashboard-items">
                                                        <div>
                                                            <div class="h1-dashboard">
                                                                <?php echo $prescription_count ?>
                                                            </div><br>
                                                            <div class="h3-dashboard" style="font-size: 15px">
                                                                Prescriptions
                                                            </div>
                                                        </div>
                                                        <div class="btn-icon-back">
                                                            <i class="fas fa-prescription"></i>
                                                        </div>
                                                    </div>
                                                </td>
                                            </tr>
                                        </table>
                                    </center>
                                </td>
                                <td>
                                    <div class="prescriptions-card">
                                        <div class="prescriptions-header">
                                            <h3 class="prescriptions-title">Prescriptions Récentes</h3>
                                            <a href="dossier_medical.php" class="view-all-link">Voir tout</a>
                                        </div>
                                        
                                        <?php if(count($recent_prescriptions) > 0): ?>
                                            <table class="prescriptions-table">
                                                <thead>
                                                    <tr>
                                                        <th>Médicament</th>
                                                        <th>Patient</th>
                                                        <th>Date</th>
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                    <?php foreach ($recent_prescriptions as $prescription): ?>
                                                        <tr>
                                                            <td><?php echo substr($prescription['medication_name'], 0, 20); ?></td>
                                                            <td><?php echo substr($prescription['patient_name'], 0, 15); ?></td>
                                                            <td><?php echo $prescription['prescription_date']; ?></td>
                                                        </tr>
                                                    <?php endforeach; ?>
                                                </tbody>
                                            </table>
                                        <?php else: ?>
                                            <div class="empty-prescriptions">
                                                <i class="fas fa-prescription"></i>
                                                <p>Aucune prescription récente.</p>
                                                <a href="medical_record.php" class="non-style-link">
                                                    <button class="btn-primary-soft btn">Créer une prescription</button>
                                                </a>
                                            </div>
                                        <?php endif; ?>
                                    </div>
                                </td>
                            </tr>
                        </table>
                    </td>
                </tr>

                <tr>
                    <td colspan="4">
                        <p id="anim" style="font-size: 20px;font-weight:600;padding-left: 40px; margin-top: 20px;">Vos prochaines sessions jusqu'à la semaine prochaine</p>
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
                                        if ($database) {
                                            $nextweek = date("Y-m-d", strtotime("+1 week"));
                                            $sqlmain = "select schedule.scheduleid, schedule.title, doctor.docname, schedule.scheduledate, schedule.scheduletime, schedule.nop from schedule inner join doctor on schedule.docid=doctor.docid where schedule.docid='$userid' and schedule.scheduledate>='$today' and schedule.scheduledate<='$nextweek' order by schedule.scheduledate desc";
                                            $result = $database->query($sqlmain);

                                            if($result && $result->num_rows == 0){
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
                                            } else if ($result) {
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
                                            } else {
                                                echo '<tr>
                                                    <td colspan="4">
                                                    <br><br><br><br>
                                                    <center>
                                                    <img src="../img/notfound.svg" width="25%">
                                                    <br>
                                                    <p class="heading-main12" style="margin-left: 45px;font-size:20px;color:rgb(49, 49, 49)">Erreur de chargement des sessions!</p>
                                                    </center>
                                                    <br><br><br><br>
                                                    </td>
                                                </tr>';
                                            }
                                        } else {
                                            echo '<tr>
                                                <td colspan="4">
                                                <br><br><br><br>
                                                <center>
                                                <img src="../img/notfound.svg" width="25%">
                                                <br>
                                                <p class="heading-main12" style="margin-left: 45px;font-size:20px;color:rgb(49, 49, 49)">Erreur de connexion à la base de données!</p>
                                                </center>
                                                <br><br><br><br>
                                                </td>
                                            </tr>';
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

    <!-- Notification sound -->
    <audio id="notificationSound" preload="auto">
        <source src="../sound/notification.mp3" type="audio/mpeg">
    </audio>

    <!-- CHATBOT SIMPLE INLINE -->
    <div id="chat-widget" class="chat-widget hidden">
        <div class="chat-header">
            <h4>🏥 Assistant IA DOCTOLINK</h4>
            <button id="chat-close">×</button>
        </div>
        <div id="chat-messages" class="chat-messages">
            <div class="message ai-message">
                Bonjour Dr. <?php echo $username; ?> ! Je suis votre assistant DOCTOLINK. Comment puis-je vous aider aujourd'hui ?
            </div>
        </div>
        <div class="chat-input">
            <input type="text" id="chat-input" placeholder="Posez-moi une question..." />
            <button id="chat-send">Envoyer</button>
        </div>
    </div>

    <button id="chat-toggle" class="chat-toggle">💬 Aide IA</button>

    <script>
    // JavaScript for Notification System
    document.addEventListener('DOMContentLoaded', function() {
        const notificationBell = document.getElementById('notificationBell');
        const notificationDropdown = document.getElementById('notificationDropdown');
        const notificationCount = document.getElementById('notificationCount');
        const notificationList = document.getElementById('notificationList');
        const markAllRead = document.getElementById('markAllRead');
        const notificationSound = document.getElementById('notificationSound');
        
        let notificationCheckInterval;
        let currentNotifications = <?php echo json_encode($notifications); ?>;
        
        // Toggle notification dropdown
        if (notificationBell) {
            notificationBell.addEventListener('click', function(e) {
                e.stopPropagation();
                if (notificationDropdown) {
                    notificationDropdown.classList.toggle('active');
                }
            });
        }
        
        // Close dropdown when clicking outside
        document.addEventListener('click', function(e) {
            if (notificationBell && notificationDropdown && 
                !notificationBell.contains(e.target) && !notificationDropdown.contains(e.target)) {
                notificationDropdown.classList.remove('active');
            }
        });
        
        // Mark all notifications as read
        if (markAllRead) {
            markAllRead.addEventListener('click', function() {
                const unreadItems = notificationList.querySelectorAll('.unread');
                unreadItems.forEach(item => {
                    item.classList.remove('unread');
                });
                
                if (notificationCount) {
                    notificationCount.remove();
                }
            });
        }
        
        // Mark individual notification as read
        if (notificationList) {
            notificationList.addEventListener('click', function(e) {
                const item = e.target.closest('.notification-item');
                if (item && item.classList.contains('unread')) {
                    item.classList.remove('unread');
                    updateNotificationCount();
                }
            });
        }
        
        // Function to update notification count
        function updateNotificationCount() {
            if (!notificationList) return;
            
            const unreadCount = notificationList.querySelectorAll('.unread').length;
            
            if (unreadCount > 0) {
                if (notificationCount) {
                    notificationCount.textContent = unreadCount;
                } else if (notificationBell) {
                    const countBadge = document.createElement('span');
                    countBadge.className = 'notification-count';
                    countBadge.id = 'notificationCount';
                    countBadge.textContent = unreadCount;
                    notificationBell.appendChild(countBadge);
                }
            } else if (notificationCount) {
                notificationCount.remove();
            }
        }
        
        // Check for new appointments every minute
        function checkForNewAppointments() {
            fetch('check_appointments.php?doctor_id=<?php echo $userid; ?>')
                .then(response => {
                    if (!response.ok) {
                        throw new Error('Network response was not ok');
                    }
                    return response.json();
                })
                .then(data => {
                    if (data.success && data.appointments && data.appointments.length > 0) {
                        // Check if we have new notifications
                        const newAppointments = data.appointments.filter(newApp => 
                            !currentNotifications.some(existingApp => existingApp.appoid === newApp.appoid)
                        );
                        
                        if (newAppointments.length > 0) {
                            // Add new notifications
                            newAppointments.forEach(app => {
                                const appointmentTime = new Date(`${app.appodate} ${app.appotime}`);
                                const minutesUntil = Math.round((appointmentTime.getTime() - new Date().getTime()) / 60000);
                                
                                const notificationItem = document.createElement('div');
                                notificationItem.className = 'notification-item unread';
                                notificationItem.dataset.id = app.appoid;
                                notificationItem.innerHTML = `
                                    <strong>Rendez-vous imminent</strong><br>
                                    Patient: ${app.pname}<br>
                                    À: ${app.appotime.substring(0, 5)}
                                    <div class="notification-time">Dans ${minutesUntil} minutes</div>
                                `;
                                
                                if (notificationList) {
                                    notificationList.insertBefore(notificationItem, notificationList.firstChild);
                                    
                                    // Remove empty message if it exists
                                    const emptyMessage = notificationList.querySelector('.notification-empty');
                                    if (emptyMessage) {
                                        emptyMessage.remove();
                                    }
                                }
                            });
                            
                            // Play sound and animate bell
                            try {
                                if (notificationSound) {
                                    notificationSound.play();
                                }
                            } catch (e) {
                                console.log("Could not play notification sound:", e);
                            }
                            
                            if (notificationBell) {
                                notificationBell.classList.add('pulse');
                                setTimeout(() => {
                                    notificationBell.classList.remove('pulse');
                                }, 500);
                            }
                            
                            // Update current notifications and count
                            currentNotifications = [...newAppointments, ...currentNotifications];
                            updateNotificationCount();
                        }
                    }
                })
                .catch(error => {
                    console.error('Error checking appointments:", error);
                });
        }
        
        // Start checking for appointments every minute
        notificationCheckInterval = setInterval(checkForNewAppointments, 60000);
    });

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
        if (msg.includes('dossier') || msg.includes('medical') || msg.includes('prescription')) {
            return 'Vous pouvez gérer les dossiers médicaux et prescriptions dans la section "Dossier Médical".';
        }
        if (msg.includes('bonjour') || msg.includes('salut')) {
            return 'Bonjour Docteur ! Je suis là pour vous aider à naviguer dans DOCTOLINK et répondre à vos questions sur votre pratique.';
        }
        
        return 'Merci pour votre question ! Je peux vous aider avec les rendez-vous, la gestion des patients, les plannings, les dossiers médicaux et la navigation dans le système. Que souhaitez-vous savoir ?';
    }
    
    // Toggle sidebar on mobile
    document.querySelector('.menu-toggle').addEventListener('click', function() {
        document.querySelector('.menu').classList.toggle('active');
    });
    
    // Close sidebar when clicking outside on mobile
    document.addEventListener('click', function(event) {
        const sidebar = document.querySelector('.menu');
        const toggleBtn = document.querySelector('.menu-toggle');
        
        if (window.innerWidth < 768 && 
            !sidebar.contains(event.target) && 
            !toggleBtn.contains(event.target) &&
            sidebar.classList.contains('active')) {
            sidebar.classList.remove('active');
        }
    });
    
    // Handle window resize
    window.addEventListener('resize', function() {
        if (window.innerWidth >= 768) {
            document.querySelector('.menu').classList.remove('active');
        }
    });
    </script>
</body>
</html>