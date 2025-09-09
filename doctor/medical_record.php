<?php
session_start();
if(!isset($_SESSION["user"]) || $_SESSION['usertype']!='d'){
    header("location: ../login.php");
    exit();
}

include("../connection.php");

$useremail = $_SESSION["user"];
$userrow = $database->query("select * from doctor where docemail='$useremail'");
$userfetch = $userrow->fetch_assoc();
$userid = $userfetch["docid"];
$username = $userfetch["docname"];

// Check for prescription success message
if(isset($_GET['prescription_success']) && $_GET['prescription_success'] == 1) {
    $successMessage = "Prescription créée avec succès!";
}

// Initialize variables
$selectedPatient = null;
$patientPrescriptions = [];
$patientNotes = [];
$patientAppointments = [];
$searchResults = [];

// Check if a patient is selected via search or direct link
if((isset($_GET['patient_id']) && !empty($_GET['patient_id'])) || (isset($_POST['patient_id']) && !empty($_POST['patient_id']))) {
    $patient_id = isset($_GET['patient_id']) ? $_GET['patient_id'] : $_POST['patient_id'];
    
    // Fetch patient details
    $patientQuery = $database->query("SELECT * FROM patient WHERE pid = '$patient_id'");
    if($patientQuery && $patientQuery->num_rows > 0) {
        $selectedPatient = $patientQuery->fetch_assoc();
        
        // Fetch prescriptions for this patient by this doctor
        $prescriptionQuery = $database->query("
            SELECT p.*, d.docname 
            FROM prescriptions p 
            INNER JOIN doctor d ON p.doctor_id = d.docid 
            WHERE p.patient_id = '$patient_id' AND p.doctor_id = '$userid'
            ORDER BY p.prescription_date DESC
        ");
        
        if($prescriptionQuery && $prescriptionQuery->num_rows > 0) {
            while($row = $prescriptionQuery->fetch_assoc()) {
                $patientPrescriptions[] = $row;
            }
        }
        
        // Fetch medical notes for this patient by this doctor
        $notesQuery = $database->query("
            SELECT n.*, d.docname 
            FROM medical_notes n 
            INNER JOIN doctor d ON n.doctor_id = d.docid 
            WHERE n.patient_id = '$patient_id' AND n.doctor_id = '$userid'
            ORDER BY n.created_at DESC
        ");
        
        if($notesQuery && $notesQuery->num_rows > 0) {
            while($row = $notesQuery->fetch_assoc()) {
                $patientNotes[] = $row;
            }
        }
        
        // Fetch appointments for this patient with this doctor
        $appointmentQuery = $database->query("
            SELECT a.*, s.title, d.docname 
            FROM appointment a 
            INNER JOIN schedule s ON a.scheduleid = s.scheduleid 
            INNER JOIN doctor d ON s.docid = d.docid 
            WHERE a.pid = '$patient_id' AND s.docid = '$userid'
            ORDER BY a.appodate DESC, a.appotime DESC
        ");
        
        if($appointmentQuery && $appointmentQuery->num_rows > 0) {
            while($row = $appointmentQuery->fetch_assoc()) {
                $patientAppointments[] = $row;
            }
        }
    }
}

// Handle search form submission
if(isset($_POST['search_patient'])) {
    $search_term = $_POST['search_term'];
    
    // Search for patients by name, email, or phone
    $searchQuery = $database->query("
        SELECT * FROM patient 
        WHERE pname LIKE '%$search_term%' 
        OR pemail LIKE '%$search_term%' 
        OR tel LIKE '%$search_term%'
        LIMIT 10
    ");
    
    if($searchQuery && $searchQuery->num_rows > 0) {
        while($row = $searchQuery->fetch_assoc()) {
            $searchResults[] = $row;
        }
    }
}

// Handle direct patient selection from search results
if(isset($_POST['select_patient'])) {
    $patient_id = $_POST['patient_id'];
    // The rest of the processing will happen in the section above
}
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dossier Médical | Doctolink</title>
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
        
        /* Alert Styles */
        .alert {
            padding: 15px;
            margin: 15px 0;
            border-radius: var(--border-radius);
            font-weight: 500;
            display: flex;
            align-items: center;
        }
        
        .alert-success {
            background: #d4edda;
            color: #155724;
            border: 1px solid #c3e6cb;
        }
        
        .alert i {
            margin-right: 10px;
        }
        
        /* Medical Container */
        .medical-container {
            background: white;
            border-radius: var(--border-radius);
            box-shadow: var(--box-shadow);
            padding: 25px;
            margin-bottom: 20px;
        }
        
        .patient-search {
            background: var(--light);
            padding: 20px;
            border-radius: var(--border-radius);
            margin-bottom: 25px;
        }
        
        .search-form {
            display: flex;
            gap: 10px;
        }
        
        .search-input {
            flex: 1;
            padding: 12px 15px;
            border: 1px solid #ddd;
            border-radius: var(--border-radius);
            font-size: 15px;
        }
        
        .search-btn {
            padding: 12px 20px;
            background: var(--primary);
            color: white;
            border: none;
            border-radius: var(--border-radius);
            cursor: pointer;
            transition: var(--transition);
        }
        
        .search-btn:hover {
            background: #1e4bb3;
        }
        
        .search-results {
            margin-top: 15px;
            max-height: 300px;
            overflow-y: auto;
            border: 1px solid #eee;
            border-radius: var(--border-radius);
            background: white;
            display: none;
        }
        
        .search-result-item {
            padding: 12px 15px;
            border-bottom: 1px solid #eee;
            cursor: pointer;
            transition: var(--transition);
        }
        
        .search-result-item:hover {
            background: var(--primary-light);
        }
        
        .search-result-item:last-child {
            border-bottom: none;
        }
        
        /* Patient Header */
        .patient-header {
            background: linear-gradient(135deg, var(--primary) 0%, #3a6fe0 100%);
            color: white;
            padding: 25px;
            border-radius: var(--border-radius);
            margin-bottom: 25px;
            position: relative;
            overflow: hidden;
        }
        
        .patient-header::before {
            content: '';
            position: absolute;
            top: -50px;
            right: -50px;
            width: 200px;
            height: 200px;
            background: rgba(255,255,255,0.1);
            border-radius: 50%;
        }
        
        .patient-header h2 {
            font-size: 24px;
            margin-bottom: 10px;
            position: relative;
            z-index: 1;
        }
        
        .patient-info {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 15px;
            margin-top: 15px;
            position: relative;
            z-index: 1;
        }
        
        .info-item {
            display: flex;
            align-items: center;
        }
        
        .info-item i {
            margin-right: 10px;
            font-size: 18px;
        }
        
        /* Tabs */
        .tabs {
            display: flex;
            border-bottom: 1px solid #ddd;
            margin-bottom: 25px;
        }
        
        .tab {
            padding: 12px 20px;
            cursor: pointer;
            border-bottom: 3px solid transparent;
            transition: var(--transition);
            font-weight: 500;
        }
        
        .tab:hover {
            background: #f5f7fb;
        }
        
        .tab.active {
            border-bottom-color: var(--primary);
            color: var(--primary);
        }
        
        .tab-content {
            display: none;
        }
        
        .tab-content.active {
            display: block;
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
            font-size: 24px;
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
            transform: translateY(-2px);
            box-shadow: 0 2px 8px rgba(0,0,0,0.05);
        }
        
        .record-item:last-child {
            border-bottom: none;
        }
        
        .record-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 10px;
            flex-wrap: wrap;
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
            flex-wrap: wrap;
        }
        
        .detail-label {
            font-weight: 500;
            min-width: 120px;
            color: var(--dark);
        }
        
        .record-footer {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-top: 10px;
            font-size: 13px;
            color: var(--secondary);
            flex-wrap: wrap;
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
        
        /* Prescription Search */
        .prescription-search {
            margin-bottom: 20px;
        }
        
        .prescription-search-input {
            width: 100%;
            padding: 12px 15px;
            border: 1px solid #ddd;
            border-radius: var(--border-radius);
            font-size: 15px;
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
        
        /* Action Buttons */
        .action-buttons {
            display: flex;
            gap: 15px;
            margin-top: 25px;
        }
        
        .action-buttons .btn {
            padding: 12px 20px;
            border-radius: var(--border-radius);
            text-decoration: none;
            font-weight: 500;
            display: inline-flex;
            align-items: center;
            gap: 8px;
            transition: var(--transition);
        }
        
        .action-buttons .btn-primary {
            background: var(--primary);
            color: white;
        }
        
        .action-buttons .btn-primary:hover {
            background: #1e4bb3;
        }
        
        .action-buttons .btn-secondary {
            background: var(--secondary);
            color: white;
        }
        
        .action-buttons .btn-secondary:hover {
            background: #5a6268;
        }
        
        /* No Patient Selected */
        .no-patient-selected {
            text-align: center;
            padding: 60px 20px;
            color: var(--secondary);
        }
        
        .no-patient-selected i {
            font-size: 60px;
            margin-bottom: 20px;
            color: #ddd;
        }
        
        .no-patient-selected h3 {
            margin-bottom: 10px;
            color: var(--dark);
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
            .patient-info {
                grid-template-columns: 1fr;
            }
            
            .search-form {
                flex-direction: column;
            }
            
            .record-header {
                flex-direction: column;
                align-items: flex-start;
            }
            
            .record-date {
                margin-top: 5px;
            }
            
            .record-detail {
                flex-direction: column;
            }
            
            .detail-label {
                min-width: auto;
                margin-bottom: 5px;
            }
            
            .record-footer {
                flex-direction: column;
                align-items: flex-start;
                gap: 10px;
            }
            
            .tabs {
                flex-wrap: wrap;
            }
            
            .tab {
                flex: 1;
                text-align: center;
                min-width: 120px;
            }
            
            .action-buttons {
                flex-direction: column;
            }
        }
        
        @media (max-width: 576px) {
            .menu {
                width: 100%;
                height: auto;
                position: relative;
            }
            
            .dash-body {
                margin-left: 0;
            }
            
            .container {
                flex-direction: column;
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
                            <p class="menu-text">Mes Sessions</p>
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
                        <a href="create_prescription.php" class="non-style-link-menu">
                            <i class="fas fa-prescription"></i>
                            <p class="menu-text">Créer Ordonnance</p>
                        </a>
                    </td>
                </tr>
                <tr class="menu-row">
                    <td class="menu-btn menu-active">
                        <a href="medical_record.php" class="non-style-link-menu">
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
                
                <?php if(isset($successMessage)): ?>
                <tr>
                    <td>
                        <div class="alert alert-success">
                            <i class="fas fa-check-circle"></i> <?php echo $successMessage; ?>
                        </div>
                    </td>
                </tr>
                <?php endif; ?>
                
                <tr>
                    <td colspan="4">
                        <div class="medical-container">
                            <!-- Patient Search Section -->
                            <div class="patient-search">
                                <h3><i class="fas fa-search"></i> Rechercher un patient</h3>
                                <form method="POST" class="search-form" id="searchForm">
                                    <input type="text" name="search_term" class="search-input" placeholder="Nom, email ou téléphone du patient..." value="<?php echo isset($_POST['search_term']) ? htmlspecialchars($_POST['search_term']) : ''; ?>" required>
                                    <button type="submit" name="search_patient" class="search-btn">
                                        <i class="fas fa-search"></i> Rechercher
                                    </button>
                                </form>
                                
                                <?php if(isset($searchResults) && !empty($searchResults)): ?>
                                <div class="search-results" id="searchResults" style="display: block;">
                                    <?php foreach($searchResults as $patient): ?>
                                        <div class="search-result-item">
                                            <form method="POST" style="display: flex; justify-content: space-between; align-items: center; width: 100%;">
                                                <div>
                                                    <strong><?php echo htmlspecialchars($patient['pname']); ?></strong>
                                                    <div style="font-size: 13px; color: #666;">
                                                        <?php echo htmlspecialchars($patient['pemail']); ?> | <?php echo isset($patient['tel']) ? htmlspecialchars($patient['tel']) : 'Non renseigné'; ?>
                                                    </div>
                                                </div>
                                                <input type="hidden" name="patient_id" value="<?php echo $patient['pid']; ?>">
                                                <button type="submit" name="select_patient" class="btn btn-primary" style="padding: 8px 15px;">
                                                    <i class="fas fa-folder-open"></i> Voir dossier
                                                </button>
                                            </form>
                                        </div>
                                    <?php endforeach; ?>
                                </div>
                                <?php elseif(isset($_POST['search_patient'])): ?>
                                <div class="search-results" id="searchResults" style="display: block;">
                                    <div class="search-result-item">Aucun patient trouvé</div>
                                </div>
                                <?php endif; ?>
                            </div>
                            
                            <?php if($selectedPatient): ?>
                            <!-- Patient Header -->
                            <div id="patientHeader" class="patient-header" style="display: block;">
                                <h2><i class="fas fa-user-injured"></i> Dossier Médical de <span id="patientName"><?php echo htmlspecialchars($selectedPatient['pname']); ?></span></h2>
                                <div class="patient-info">
                                    <div class="info-item">
                                        <i class="fas fa-envelope"></i>
                                        <span id="patientEmail"><?php echo htmlspecialchars($selectedPatient['pemail']); ?></span>
                                    </div>
                                    <div class="info-item">
                                        <i class="fas fa-phone"></i>
                                        <span id="patientPhone"><?php echo isset($selectedPatient['tel']) ? htmlspecialchars($selectedPatient['tel']) : 'Non renseigné'; ?></span>
                                    </div>
                                    <div class="info-item">
                                        <i class="fas fa-id-card"></i>
                                        <span>ID: <?php echo htmlspecialchars($selectedPatient['pid']); ?></span>
                                    </div>
                                </div>
                            </div>
                            
                            <!-- Tabs -->
                            <div id="tabsContainer" class="tabs" style="display: flex;">
                                <div class="tab active" data-tab="prescriptions">Prescriptions</div>
                                <div class="tab" data-tab="notes">Notes Médicales</div>
                                <div class="tab" data-tab="appointments">Rendez-vous</div>
                            </div>
                            
                            <!-- Prescriptions Tab -->
                            <div class="tab-content active" id="prescriptions-tab">
                                <div class="section-card">
                                    <h3 class="section-title"><i class="fas fa-prescription"></i> Prescriptions</h3>
                                    
                                    <!-- Prescription Search -->
                                    <div class="prescription-search">
                                        <input type="text" id="prescriptionSearch" class="prescription-search-input" placeholder="Rechercher dans les prescriptions..." onkeyup="filterPrescriptions()">
                                    </div>
                                    
                                    <div class="records-list" id="prescriptionsList">
                                        <?php if (!empty($patientPrescriptions)): ?>
                                            <?php 
                                            // Group prescriptions by date
                                            $groupedPrescriptions = [];
                                            foreach($patientPrescriptions as $prescription) {
                                                $date = $prescription['prescription_date'];
                                                if (!isset($groupedPrescriptions[$date])) {
                                                    $groupedPrescriptions[$date] = [];
                                                }
                                                $groupedPrescriptions[$date][] = $prescription;
                                            }
                                            ?>
                                            
                                            <?php foreach($groupedPrescriptions as $date => $prescriptions): ?>
                                                <h4 style="margin: 20px 0 10px; color: var(--primary); border-bottom: 1px solid #eee; padding-bottom: 5px;">
                                                    <?php echo date('d/m/Y', strtotime($date)); ?>
                                                </h4>
                                                
                                                <?php foreach($prescriptions as $prescription): ?>
                                                    <div class="record-item prescription-item">
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
                                                            <?php if(!empty($prescription['instructions'])): ?>
                                                            <div class="record-detail">
                                                                <span class="detail-label">Instructions:</span>
                                                                <span><?php echo htmlspecialchars($prescription['instructions']); ?></span>
                                                            </div>
                                                            <?php endif; ?>
                                                            <?php if(!empty($prescription['doctor_notes'])): ?>
                                                            <div class="record-detail">
                                                                <span class="detail-label">Notes du médecin:</span>
                                                                <span><?php echo nl2br(htmlspecialchars($prescription['doctor_notes'])); ?></span>
                                                            </div>
                                                            <?php endif; ?>
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
                                            <?php endforeach; ?>
                                        <?php else: ?>
                                            <div class="empty-state">
                                                <i class="fas fa-prescription"></i>
                                                <p>Aucune prescription enregistrée pour ce patient.</p>
                                            </div>
                                        <?php endif; ?>
                                    </div>
                                </div>
                            </div>
                            
                            <!-- Medical Notes Tab -->
                            <div class="tab-content" id="notes-tab">
                                <div class="section-card">
                                    <h3 class="section-title"><i class="fas fa-notes-medical"></i> Notes Médicales</h3>
                                    
                                    <div class="records-list" id="notesList">
                                        <?php if (!empty($patientNotes)): ?>
                                            <?php foreach($patientNotes as $note): ?>
                                                <?php 
                                                $noteClass = '';
                                                $noteTypeText = '';
                                                
                                                switch($note['note_type']) {
                                                    case 'diagnosis':
                                                        $noteClass = 'note-type-diagnosis';
                                                        $noteTypeText = 'Diagnostic';
                                                        break;
                                                    case 'observation':
                                                        $noteClass = 'note-type-observation';
                                                        $noteTypeText = 'Observation';
                                                        break;
                                                    case 'treatment':
                                                        $noteClass = 'note-type-treatment';
                                                        $noteTypeText = 'Traitement';
                                                        break;
                                                    case 'follow_up':
                                                        $noteClass = 'note-type-follow_up';
                                                        $noteTypeText = 'Suivi';
                                                        break;
                                                    default:
                                                        $noteClass = 'note-type-observation';
                                                        $noteTypeText = $note['note_type'];
                                                }
                                                ?>
                                                
                                                <div class="record-item">
                                                    <div class="record-header">
                                                        <div>
                                                            <span class="note-type-badge <?php echo $noteClass; ?>"><?php echo $noteTypeText; ?></span>
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
                                                <p>Aucune note médicale enregistrée pour ce patient.</p>
                                            </div>
                                        <?php endif; ?>
                                    </div>
                                </div>
                            </div>
                            
                            <!-- Appointments Tab -->
                            <div class="tab-content" id="appointments-tab">
                                <div class="section-card">
                                    <h3 class="section-title"><i class="fas fa-calendar-check"></i> Historique des Rendez-vous</h3>
                                    
                                    <div class="records-list" id="appointmentsList">
                                        <?php if (!empty($patientAppointments)): ?>
                                            <?php foreach($patientAppointments as $appointment): ?>
                                                <div class="record-item">
                                                    <div class="record-header">
                                                        <span class="record-title">Rendez-vous</span>
                                                        <span class="record-date"><?php echo date('d/m/Y', strtotime($appointment['appodate'])); ?> <?php echo substr($appointment['appotime'], 0, 5); ?></span>
                                                    </div>
                                                    
                                                    <div class="record-details">
                                                        <div class="record-detail">
                                                            <span class="detail-label">Statut:</span>
                                                            <span>
                                                                <?php 
                                                                switch($appointment['status']) {
                                                                    case 'Complete': echo 'Terminé'; break;
                                                                    case 'Pending': echo 'En attente'; break;
                                                                    case 'Cancelled': echo 'Annulé'; break;
                                                                    default: echo $appointment['status'];
                                                                }
                                                                ?>
                                                            </span>
                                                        </div>
                                                        <?php if(!empty($appointment['title'])): ?>
                                                        <div class="record-detail">
                                                            <span class="detail-label">Séance:</span>
                                                            <span><?php echo htmlspecialchars($appointment['title']); ?></span>
                                                        </div>
                                                        <?php endif; ?>
                                                    </div>
                                                    
                                                    <div class="record-footer">
                                                        <span>Médecin: Dr. <?php echo htmlspecialchars($appointment['docname']); ?></span>
                                                    </div>
                                                </div>
                                            <?php endforeach; ?>
                                        <?php else: ?>
                                            <div class="empty-state">
                                                <i class="fas fa-calendar-check"></i>
                                                <p>Aucun rendez-vous enregistré pour ce patient.</p>
                                            </div>
                                        <?php endif; ?>
                                    </div>
                                </div>
                            </div>
                            
                            <!-- Action Buttons -->
                            <div class="action-buttons">
                                <a href="create_prescription.php?patient_id=<?php echo $selectedPatient['pid']; ?>" class="btn btn-primary">
                                    <i class="fas fa-prescription"></i> Nouvelle Prescription
                                </a>
                                <button class="btn btn-secondary" onclick="window.print()">
                                    <i class="fas fa-print"></i> Imprimer
                                </button>
                            </div>
                            <?php else: ?>
                            <!-- No Patient Selected -->
                            <div class="no-patient-selected">
                                <i class="fas fa-folder-open"></i>
                                <h3>Sélectionnez un patient pour voir son dossier médical</h3>
                                <p>Utilisez la barre de recherche ci-dessus pour trouver un patient.</p>
                            </div>
                            <?php endif; ?>
                        </div>
                    </td>
                </tr>
            </table>
        </div>
    </div>

    <script>
        // Tab functionality
        document.querySelectorAll('.tab').forEach(tab => {
            tab.addEventListener('click', () => {
                // Remove active class from all tabs and content
                document.querySelectorAll('.tab').forEach(t => t.classList.remove('active'));
                document.querySelectorAll('.tab-content').forEach(c => c.classList.remove('active'));
                
                // Add active class to clicked tab
                tab.classList.add('active');
                
                // Show corresponding content
                const tabName = tab.getAttribute('data-tab');
                document.getElementById(`${tabName}-tab`).classList.add('active');
            });
        });
        
        // Prescription search/filter functionality
        function filterPrescriptions() {
            const searchTerm = document.getElementById('prescriptionSearch').value.toLowerCase();
            const prescriptionItems = document.querySelectorAll('.prescription-item');
            
            prescriptionItems.forEach(item => {
                const text = item.textContent.toLowerCase();
                if (text.includes(searchTerm)) {
                    item.style.display = 'block';
                } else {
                    item.style.display = 'none';
                }
            });
        }
        
        // Auto-submit form when patient is selected from search results
        document.querySelectorAll('.search-result-item form').forEach(form => {
            form.addEventListener('submit', function(e) {
                // Add loading indicator
                const button = this.querySelector('button[type="submit"]');
                const originalText = button.innerHTML;
                button.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Chargement...';
                button.disabled = true;
                
                // The form will submit normally
            });
        });
        
        // Preserve search term after page reload
        document.addEventListener('DOMContentLoaded', function() {
            <?php if(isset($_POST['search_term'])): ?>
                document.getElementById('searchForm').scrollIntoView({ behavior: 'smooth' });
            <?php endif; ?>
        });
    </script>
</body>
</html>