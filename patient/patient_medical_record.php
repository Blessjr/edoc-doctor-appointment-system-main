<?php
session_start();
if(!isset($_SESSION["user"]) || $_SESSION["usertype"] != 'p'){
    header("location: ../login.php");
    exit();
}

include("../connection.php");

$useremail = $_SESSION["user"];
$userrow = $database->query("SELECT * FROM patient WHERE pemail='$useremail'");
$userfetch = $userrow->fetch_assoc();
$userid = $userfetch["pid"];
$username = $userfetch["pname"];

// Fetch prescriptions for this patient
$prescriptions = [];
$prescription_result = $database->query("
    SELECT p.*, d.docname 
    FROM prescriptions p 
    INNER JOIN doctor d ON p.doctor_id = d.docid 
    WHERE p.patient_id = '$userid' 
    ORDER BY p.prescription_date DESC
");

if($prescription_result && $prescription_result->num_rows > 0) {
    while($row = $prescription_result->fetch_assoc()) {
        $prescriptions[] = $row;
    }
}

// Fetch medical notes for this patient
$medical_notes = [];
$notes_result = $database->query("
    SELECT n.*, d.docname 
    FROM medical_notes n 
    INNER JOIN doctor d ON n.doctor_id = d.docid 
    WHERE n.patient_id = '$userid' 
    ORDER BY n.created_at DESC
");

if($notes_result && $notes_result->num_rows > 0) {
    while($row = $notes_result->fetch_assoc()) {
        $medical_notes[] = $row;
    }
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dossier Médical | Docto Link</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        :root {
            --primary: #2c5cc7;
            --primary-light: #e0e8ff;
            --secondary: #6c757d;
            --success: #28a745;
            --danger: #dc3545;
            --warning: #ffc107;
            --info: #17a2b8;
            --light: #f8f9fa;
            --dark: #343a40;
            --border-radius: 8px;
            --box-shadow: 0 4px 12px rgba(0,0,0,0.1);
            --transition: all 0.3s ease;
        }
        
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        }
        
        body {
            background-color: #f5f7fb;
            color: #333;
            line-height: 1.6;
        }
        
        .container {
            display: flex;
            min-height: 100vh;
        }
        
        /* Menu Styles */
        .menu {
            width: 250px;
            background: linear-gradient(135deg, #2c5cc7 0%, #3a6fe0 100%);
            z-index: 100;
            height: 100vh;
            position: fixed;
            overflow-y: auto;
            padding: 20px 0;
        }
        
        .menu-container {
            width: 100%;
            border-collapse: collapse;
        }
        
        .profile-container {
            padding: 20px;
            text-align: center;
            border-bottom: 1px solid rgba(255,255,255,0.2);
            margin-bottom: 20px;
        }
        
        .profile-container img {
            width: 80px;
            height: 80px;
            border-radius: 50%;
            object-fit: cover;
            border: 3px solid rgba(255,255,255,0.3);
        }
        
        .profile-title {
            font-weight: 600;
            margin-top: 10px;
            color: white;
            font-size: 16px;
        }
        
        .profile-subtitle {
            font-size: 12px;
            color: rgba(255,255,255,0.8);
        }
        
        .logout-btn {
            width: 100%;
            padding: 10px;
            background: rgba(255,255,255,0.2);
            color: white;
            border: none;
            border-radius: var(--border-radius);
            cursor: pointer;
            margin-top: 10px;
            transition: var(--transition);
        }
        
        .logout-btn:hover {
            background: rgba(255,255,255,0.3);
        }
        
        .menu-row {
            border-bottom: none;
        }
        
        .menu-btn {
            padding: 15px 20px;
            display: flex;
            align-items: center;
            color: rgba(255,255,255,0.8);
            text-decoration: none;
            transition: var(--transition);
            border-left: 4px solid transparent;
        }
        
        .menu-btn:hover {
            background: rgba(255,255,255,0.1);
            color: white;
            border-left-color: rgba(255,255,255,0.3);
        }
        
        .menu-active {
            background: rgba(255,255,255,0.15);
            color: white;
            border-left: 4px solid white;
        }
        
        .menu-btn i {
            width: 20px;
            text-align: center;
            margin-right: 15px;
        }
        
        .menu-text {
            font-weight: 500;
        }
        
        .non-style-link-menu {
            text-decoration: none;
            display: flex;
            align-items: center;
            color: inherit;
            width: 100%;
        }
        
        /* Main Content Styles */
        .dash-body {
            flex: 1;
            margin-left: 250px;
            padding: 20px;
        }
        
        .nav-bar {
            background: white;
            padding: 15px 25px;
            border-radius: var(--border-radius);
            box-shadow: var(--box-shadow);
            margin-bottom: 20px;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }
        
        .nav-bar p {
            font-size: 22px;
            font-weight: 600;
            color: var(--primary);
            margin: 0;
        }
        
        .date-display {
            display: flex;
            align-items: center;
            gap: 10px;
            color: var(--secondary);
        }
        
        .date-display .heading-sub12 {
            font-weight: 600;
            color: var(--dark);
        }
        
        .btn-label {
            background: var(--primary-light);
            border: none;
            border-radius: var(--border-radius);
            padding: 10px;
            cursor: pointer;
            display: flex;
            align-items: center;
            justify-content: center;
        }
        
        /* Medical Container */
        .medical-container {
            background: white;
            border-radius: var(--border-radius);
            box-shadow: var(--box-shadow);
            padding: 25px;
            margin-bottom: 20px;
        }
        
        /* Section Cards */
        .section-card {
            background: white;
            border-radius: var(--border-radius);
            box-shadow: var(--box-shadow);
            padding: 25px;
            margin-bottom: 25px;
            border-top: 4px solid var(--primary);
        }
        
        .section-title {
            font-size: 20px;
            font-weight: 600;
            color: var(--primary);
            margin-bottom: 20px;
            padding-bottom: 10px;
            border-bottom: 2px solid #f1f1f1;
            display: flex;
            align-items: center;
        }
        
        .section-title i {
            margin-right: 10px;
        }
        
        /* Records List */
        .records-list {
            margin-top: 20px;
        }
        
        .record-item {
            padding: 20px;
            border-bottom: 1px solid #eee;
            transition: var(--transition);
        }
        
        .record-item:hover {
            background: #f9fafc;
        }
        
        .record-item:last-child {
            border-bottom: none;
        }
        
        .record-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 10px;
        }
        
        .record-title {
            font-weight: 600;
            color: var(--primary);
            font-size: 17px;
        }
        
        .record-date {
            font-size: 13px;
            color: var(--secondary);
        }
        
        .record-details {
            margin-bottom: 10px;
        }
        
        .record-detail {
            margin-bottom: 5px;
            display: flex;
        }
        
        .detail-label {
            font-weight: 500;
            min-width: 100px;
            color: var(--dark);
        }
        
        .record-footer {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-top: 10px;
            font-size: 13px;
            color: var(--secondary);
        }
        
        .status-badge {
            padding: 4px 10px;
            border-radius: 20px;
            font-size: 12px;
            font-weight: 500;
        }
        
        .status-active {
            background: #e0f8e9;
            color: #28a745;
        }
        
        .status-completed {
            background: #e8eaf6;
            color: #3f51b5;
        }
        
        .status-cancelled {
            background: #ffebee;
            color: #f44336;
        }
        
        .note-type-badge {
            padding: 4px 10px;
            border-radius: 20px;
            font-size: 12px;
            font-weight: 500;
            margin-right: 8px;
        }
        
        .note-type-diagnosis {
            background: #ffebee;
            color: #f44336;
        }
        
        .note-type-observation {
            background: #fff8e1;
            color: #ffa000;
        }
        
        .note-type-treatment {
            background: #e8f5e9;
            color: #4caf50;
        }
        
        .note-type-follow_up {
            background: #e3f2fd;
            color: #2196f3;
        }
        
        /* Empty State */
        .empty-state {
            text-align: center;
            padding: 40px 20px;
            color: var(--secondary);
        }
        
        .empty-state i {
            font-size: 50px;
            margin-bottom: 15px;
            color: #ddd;
        }
        
        .empty-state p {
            margin-bottom: 20px;
        }
        
        /* Animations */
        @keyframes fadeIn {
            from { opacity: 0; }
            to { opacity: 1; }
        }
        
        /* Responsive */
        @media (max-width: 992px) {
            .menu {
                width: 70px;
            }
            
            .menu-text, .profile-container td:last-child, .logout-btn {
                display: none;
            }
            
            .profile-container {
                padding: 15px 10px;
            }
            
            .profile-container img {
                width: 40px;
                height: 40px;
            }
            
            .menu-btn {
                justify-content: center;
                padding: 15px 10px;
            }
            
            .menu-btn i {
                margin-right: 0;
            }
            
            .dash-body {
                margin-left: 70px;
            }
        }
        
        @media (max-width: 768px) {
            .record-header {
                flex-direction: column;
                align-items: flex-start;
            }
            
            .record-date {
                margin-top: 5px;
            }
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
                                    <a href="../logout.php"><input type="button" value="Se déconnecter" class="logout-btn btn"></a>
                                </td>
                            </tr>
                        </table>
                    </td>
                </tr>
                <tr class="menu-row">
                    <td class="menu-btn">
                        <a href="index.php" class="non-style-link-menu">
                            <i class="fas fa-home"></i>
                            <p class="menu-text">Accueil</p>
                        </a>
                    </td>
                </tr>
                <tr class="menu-row">
                    <td class="menu-btn">
                        <a href="doctors.php" class="non-style-link-menu">
                            <i class="fas fa-user-md"></i>
                            <p class="menu-text">Tous les médecins</p>
                        </a>
                    </td>
                </tr>
                <tr class="menu-row">
                    <td class="menu-btn">
                        <a href="schedule.php" class="non-style-link-menu">
                            <i class="fas fa-calendar"></i>
                            <p class="menu-text">Séances programmées</p>
                        </a>
                    </td>
                </tr>
                <tr class="menu-row">
                    <td class="menu-btn">
                        <a href="appointment.php" class="non-style-link-menu">
                            <i class="fas fa-calendar-check"></i>
                            <p class="menu-text">Mes réservations</p>
                        </a>
                    </td>
                </tr>
                <tr class="menu-row">
                    <td class="menu-btn menu-active">
                        <a href="patient_medical_record.php" class="non-style-link-menu">
                            <i class="fas fa-file-medical"></i>
                            <p class="menu-text">Dossier Médical</p>
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
        
        <div class="dash-body">
            <table border="0" width="100%" style="border-spacing: 0;margin:0;padding:0;">
                <tr>
                    <td colspan="1" class="nav-bar">
                        <p><i class="fas fa-file-medical"></i> Dossier Médical</p>
                        <div class="date-display">
                            <p style="font-size: 14px;color: rgb(119, 119, 119);padding: 0;margin: 0;text-align: right;">
                                Date du jour
                            </p>
                            <p class="heading-sub12" style="padding: 0;margin: 0;">
                                <?php echo date('d/m/Y'); ?>
                            </p>
                            <button class="btn-label">
                                <img src="../img/calendar.svg" width="20">
                            </button>
                        </div>
                    </td>
                </tr>
                
                <tr>
                    <td colspan="4">
                        <div class="medical-container">
                            <!-- Patient Header -->
                            <div class="patient-header" style="background: linear-gradient(135deg, var(--primary) 0%, #3a6fe0 100%); color: white; padding: 25px; border-radius: var(--border-radius); margin-bottom: 25px;">
                                <h2><i class="fas fa-user-injured"></i> Dossier Médical de <?php echo $username; ?></h2>
                                <div class="patient-info" style="display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 15px; margin-top: 15px;">
                                    <div class="info-item">
                                        <i class="fas fa-envelope"></i>
                                        <span><?php echo $useremail; ?></span>
                                    </div>
                                    <div class="info-item">
                                        <i class="fas fa-id-card"></i>
                                        <span>ID: <?php echo $userid; ?></span>
                                    </div>
                                </div>
                            </div>
                            
                            <!-- Prescriptions Section -->
                            <div class="section-card">
                                <h3 class="section-title"><i class="fas fa-prescription"></i> Prescriptions</h3>
                                
                                <div class="records-list" id="prescriptionsList">
                                    <?php if (count($prescriptions) > 0): ?>
                                        <?php foreach ($prescriptions as $prescription): ?>
                                            <div class="record-item">
                                                <div class="record-header">
                                                    <span class="record-title"><?php echo htmlspecialchars($prescription['medication_name']); ?></span>
                                                    <span class="record-date"><?php echo date('d/m/Y', strtotime($prescription['prescription_date'])); ?></span>
                                                </div>
                                                
                                                <div class="record-details">
                                                    <div class="record-detail">
                                                        <span class="detail-label">Dosage:</span>
                                                        <span><?php echo htmlspecialchars($prescription['dosage']); ?></span>
                                                    </div>
                                                    <div class="record-detail">
                                                        <span class="detail-label">Fréquence:</span>
                                                        <span><?php echo htmlspecialchars($prescription['frequency']); ?></span>
                                                    </div>
                                                    <div class="record-detail">
                                                        <span class="detail-label">Durée:</span>
                                                        <span><?php echo htmlspecialchars($prescription['duration']); ?></span>
                                                    </div>
                                                    <div class="record-detail">
                                                        <span class="detail-label">Instructions:</span>
                                                        <span><?php echo htmlspecialchars($prescription['instructions']); ?></span>
                                                    </div>
                                                </div>
                                                
                                                <div class="record-footer">
                                                    <div>
                                                        <span class="status-badge status-<?php echo $prescription['status']; ?>">
                                                            <?php echo $prescription['status'] === 'active' ? 'Actif' : 'Terminé'; ?>
                                                        </span>
                                                    </div>
                                                    <div>
                                                        Prescrit par Dr. <?php echo htmlspecialchars($prescription['docname']); ?>
                                                    </div>
                                                </div>
                                            </div>
                                        <?php endforeach; ?>
                                    <?php else: ?>
                                        <div class="empty-state">
                                            <i class="fas fa-prescription"></i>
                                            <p>Aucune prescription enregistrée.</p>
                                        </div>
                                    <?php endif; ?>
                                </div>
                            </div>
                            
                            <!-- Medical Notes Section -->
                            <div class="section-card">
                                <h3 class="section-title"><i class="fas fa-notes-medical"></i> Notes Médicales</h3>
                                
                                <div class="records-list" id="notesList">
                                    <?php if (count($medical_notes) > 0): ?>
                                        <?php foreach ($medical_notes as $note): ?>
                                            <div class="record-item">
                                                <div class="record-header">
                                                    <div>
                                                        <span class="note-type-badge note-type-<?php echo $note['note_type']; ?>">
                                                            <?php 
                                                            switch($note['note_type']) {
                                                                case 'diagnosis': echo 'Diagnostic'; break;
                                                                case 'observation': echo 'Observation'; break;
                                                                case 'treatment': echo 'Traitement'; break;
                                                                case 'follow_up': echo 'Suivi'; break;
                                                                default: echo $note['note_type'];
                                                            }
                                                            ?>
                                                        </span>
                                                        <span class="record-title">Note Médicale</span>
                                                    </div>
                                                    <span class="record-date"><?php echo date('d/m/Y', strtotime($note['created_at'])); ?></span>
                                                </div>
                                                
                                                <div class="record-details">
                                                    <p><?php echo nl2br(htmlspecialchars($note['note_text'])); ?></p>
                                                </div>
                                                
                                                <div class="record-footer">
                                                    <span>Ajoutée par Dr. <?php echo htmlspecialchars($note['docname']); ?></span>
                                                </div>
                                            </div>
                                        <?php endforeach; ?>
                                    <?php else: ?>
                                        <div class="empty-state">
                                            <i class="fas fa-notes-medical"></i>
                                            <p>Aucune note médicale enregistrée.</p>
                                        </div>
                                    <?php endif; ?>
                                </div>
                            </div>
                        </div>
                    </td>
                </tr>
            </table>
        </div>
    </div>
</body>
</html>