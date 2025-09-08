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
        
        /* Forms */
        .toggle-form-btn {
            background: var(--primary);
            color: white;
            border: none;
            padding: 12px 20px;
            border-radius: var(--border-radius);
            cursor: pointer;
            margin-bottom: 20px;
            display: inline-flex;
            align-items: center;
            transition: var(--transition);
        }
        
        .toggle-form-btn:hover {
            background: #1e4bb3;
        }
        
        .toggle-form-btn i {
            margin-right: 8px;
        }
        
        .hidden-form {
            display: none;
            animation: fadeIn 0.5s;
        }
        
        .form-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
            gap: 20px;
            margin-bottom: 20px;
        }
        
        .form-group {
            margin-bottom: 20px;
        }
        
        .form-label {
            display: block;
            margin-bottom: 8px;
            font-weight: 500;
            color: var(--dark);
        }
        
        .form-input, .form-select, .form-textarea {
            width: 100%;
            padding: 12px 15px;
            border: 1px solid #ddd;
            border-radius: var(--border-radius);
            font-size: 15px;
            transition: var(--transition);
        }
        
        .form-input:focus, .form-select:focus, .form-textarea:focus {
            outline: none;
            border-color: var(--primary);
            box-shadow: 0 0 0 3px rgba(44, 92, 199, 0.1);
        }
        
        .form-textarea {
            min-height: 120px;
            resize: vertical;
        }
        
        .btn {
            padding: 12px 25px;
            border: none;
            border-radius: var(--border-radius);
            cursor: pointer;
            font-size: 15px;
            font-weight: 500;
            transition: var(--transition);
            display: inline-flex;
            align-items: center;
        }
        
        .btn i {
            margin-right: 8px;
        }
        
        .btn-primary {
            background: var(--primary);
            color: white;
        }
        
        .btn-primary:hover {
            background: #1e4bb3;
        }
        
        .btn-secondary {
            background: var(--secondary);
            color: white;
        }
        
        .btn-secondary:hover {
            background: #5a6268;
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
        
        /* Alerts */
        .alert {
            padding: 15px;
            border-radius: var(--border-radius);
            margin-bottom: 20px;
            display: flex;
            align-items: center;
        }
        
        .alert i {
            margin-right: 10px;
            font-size: 18px;
        }
        
        .alert-success {
            background: #e0f8e9;
            color: #28a745;
            border: 1px solid #c3e6cb;
        }
        
        .alert-error {
            background: #ffebee;
            color: #f44336;
            border: 1px solid #f5c6cb;
        }
        
        /* Tabs */
        .tabs {
            display: flex;
            border-bottom: 2px solid #eee;
            margin-bottom: 20px;
        }
        
        .tab {
            padding: 12px 20px;
            cursor: pointer;
            font-weight: 500;
            color: var(--secondary);
            border-bottom: 2px solid transparent;
            transition: var(--transition);
        }
        
        .tab.active {
            color: var(--primary);
            border-bottom: 2px solid var(--primary);
        }
        
        .tab-content {
            display: none;
        }
        
        .tab-content.active {
            display: block;
            animation: fadeIn 0.5s;
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
            .form-grid {
                grid-template-columns: 1fr;
            }
            
            .patient-info {
                grid-template-columns: 1fr;
            }
            
            .search-form {
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
                                    <p class="profile-title">Test Doctor</p>
                                    <p class="profile-subtitle">doctor@edoc.com</p>
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
                        <a href="settings.php" class="non-style-link-menu">
                            <i class="fas fa-cog"></i>
                            <p class="menu-text">Paramètres</p>
                        </a>
                    </td>
                </tr>
                <tr class="menu-row">
                    <td class="menu-btn menu-active">
                        <a href="dossier_medical.php" class="non-style-link-menu">
                            <i class="fas fa-file-medical"></i>
                            <p class="menu-text">Dossier Médical</p>
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
                            <!-- Patient Search Section -->
                            <div class="patient-search">
                                <h3><i class="fas fa-search"></i> Rechercher un patient</h3>
                                <form method="POST" class="search-form" id="searchForm">
                                    <input type="text" name="search_term" class="search-input" placeholder="Nom, email ou téléphone du patient..." value="">
                                    <button type="submit" name="search_patient" class="search-btn">
                                        <i class="fas fa-search"></i> Rechercher
                                    </button>
                                </form>
                                
                                <div class="search-results" id="searchResults" style="display: none;">
                                    <!-- Search results will be displayed here -->
                                </div>
                            </div>
                            
                            <!-- Patient Header (initially hidden) -->
                            <div id="patientHeader" class="patient-header" style="display: none;">
                                <h2><i class="fas fa-user-injured"></i> Dossier Médical de <span id="patientName"></span></h2>
                                <div class="patient-info">
                                    <div class="info-item">
                                        <i class="fas fa-envelope"></i>
                                        <span id="patientEmail"></span>
                                    </div>
                                    <div class="info-item">
                                        <i class="fas fa-phone"></i>
                                        <span id="patientPhone"></span>
                                    </div>
                                    <div class="info-item">
                                        <i class="fas fa-birthday-cake"></i>
                                        <span id="patientDob"></span>
                                    </div>
                                    <div class="info-item">
                                        <i class="fas fa-venus-mars"></i>
                                        <span id="patientGender"></span>
                                    </div>
                                </div>
                            </div>
                            
                            <!-- Tabs (initially hidden) -->
                            <div id="tabsContainer" class="tabs" style="display: none;">
                                <div class="tab active" data-tab="prescriptions">Prescriptions</div>
                                <div class="tab" data-tab="notes">Notes Médicales</div>
                                <div class="tab" data-tab="appointments">Rendez-vous</div>
                                <div class="tab" data-tab="vitals">Signes Vitaux</div>
                            </div>
                            
                            <!-- Prescriptions Tab -->
                            <div class="tab-content active" id="prescriptions-tab">
                                <div class="section-card">
                                    <h3 class="section-title"><i class="fas fa-prescription"></i> Prescriptions</h3>
                                    
                                    <button class="toggle-form-btn" onclick="toggleForm('prescription-form')">
                                        <i class="fas fa-plus"></i> Nouvelle Prescription
                                    </button>
                                    
                                    <div id="prescription-form" class="hidden-form">
                                        <form method="POST">
                                            <input type="hidden" name="patient_id" value="">
                                            <div class="form-grid">
                                                <div class="form-group">
                                                    <label class="form-label">Médicament</label>
                                                    <input type="text" name="medication" class="form-input" required>
                                                </div>
                                                
                                                <div class="form-group">
                                                    <label class="form-label">Dosage</label>
                                                    <input type="text" name="dosage" class="form-input" required>
                                                </div>
                                                
                                                <div class="form-group">
                                                    <label class="form-label">Fréquence</label>
                                                    <select name="frequency" class="form-select" required>
                                                        <option value="">Sélectionner une fréquence</option>
                                                        <option value="Une fois par jour">Une fois par jour</option>
                                                        <option value="Deux fois par jour">Deux fois par jour</option>
                                                        <option value="Trois fois par jour">Trois fois par jour</option>
                                                        <option value="Quatre fois par jour">Quatre fois par jour</option>
                                                        <option value="Au besoin">Au besoin</option>
                                                        <option value="Autre">Autre</option>
                                                    </select>
                                                </div>
                                                
                                                <div class="form-group">
                                                    <label class="form-label">Durée</label>
                                                    <input type="text" name="duration" class="form-input" placeholder="Ex: 7 jours, 1 mois, etc." required>
                                                </div>
                                            </div>
                                            
                                            <div class="form-group">
                                                <label class="form-label">Instructions</label>
                                                <textarea name="instructions" class="form-textarea" placeholder="Instructions spécifiques..."></textarea>
                                            </div>
                                            
                                            <div class="form-group">
                                                <label class="form-label">Associer à un rendez-vous (optionnel)</label>
                                                <select name="appointment_id" class="form-select">
                                                    <option value="">Aucun rendez-vous</option>
                                                    <!-- Appointments will be populated here -->
                                                </select>
                                            </div>
                                            
                                            <button type="submit" name="add_prescription" class="btn btn-primary">
                                                <i class="fas fa-save"></i> Enregistrer
                                            </button>
                                        </form>
                                    </div>
                                    
                                    <div class="records-list" id="prescriptionsList">
                                        <div class="empty-state">
                                            <i class="fas fa-prescription"></i>
                                            <p>Aucune prescription enregistrée pour ce patient.</p>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            
                            <!-- Medical Notes Tab -->
                            <div class="tab-content" id="notes-tab">
                                <div class="section-card">
                                    <h3 class="section-title"><i class="fas fa-notes-medical"></i> Notes Médicales</h3>
                                    
                                    <button class="toggle-form-btn" onclick="toggleForm('note-form')">
                                        <i class="fas fa-plus"></i> Nouvelle Note
                                    </button>
                                    
                                    <div id="note-form" class="hidden-form">
                                        <form method="POST">
                                            <input type="hidden" name="patient_id" value="">
                                            <div class="form-grid">
                                                <div class="form-group">
                                                    <label class="form-label">Type de note</label>
                                                    <select name="note_type" class="form-select" required>
                                                        <option value="observation">Observation</option>
                                                        <option value="diagnosis">Diagnostic</option>
                                                        <option value="treatment">Traitement</option>
                                                        <option value="follow_up">Suivi</option>
                                                    </select>
                                                </div>
                                                
                                                <div class="form-group">
                                                    <label class="form-label">Associer à un rendez-vous (optionnel)</label>
                                                    <select name="appointment_id_note" class="form-select">
                                                        <option value="">Aucun rendez-vous</option>
                                                        <!-- Appointments will be populated here -->
                                                    </select>
                                                </div>
                                            </div>
                                            
                                            <div class="form-group">
                                                <label class="form-label">Note</label>
                                                <textarea name="note_text" class="form-textarea" required placeholder="Saisissez vos notes médicales..."></textarea>
                                            </div>
                                            
                                            <button type="submit" name="add_note" class="btn btn-primary">
                                                <i class="fas fa-save"></i> Enregistrer
                                            </button>
                                        </form>
                                    </div>
                                    
                                    <div class="records-list" id="notesList">
                                        <div class="empty-state">
                                            <i class="fas fa-notes-medical"></i>
                                            <p>Aucune note médicale enregistrée pour ce patient.</p>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            
                            <!-- Appointments Tab -->
                            <div class="tab-content" id="appointments-tab">
                                <div class="section-card">
                                    <h3 class="section-title"><i class="fas fa-calendar-check"></i> Historique des Rendez-vous</h3>
                                    
                                    <div class="records-list" id="appointmentsList">
                                        <div class="empty-state">
                                            <i class="fas fa-calendar-times"></i>
                                            <p>Aucun rendez-vous enregistré pour ce patient.</p>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            
                            <!-- Vitals Tab -->
                            <div class="tab-content" id="vitals-tab">
                                <div class="section-card">
                                    <h3 class="section-title"><i class="fas fa-heartbeat"></i> Signes Vitaux</h3>
                                    
                                    <div class="records-list" id="vitalsList">
                                        <div class="empty-state">
                                            <i class="fas fa-heartbeat"></i>
                                            <p>Aucune mesure de signes vitaux enregistrée pour ce patient.</p>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </td>
                </tr>
            </table>
        </div>
    </div>

    <script>
    // Sample patient data for demonstration
    const samplePatients = [
        {
            id: 1,
            name: "Jean Dupont",
            email: "jean.dupont@example.com",
            phone: "01 23 45 67 89",
            dob: "15/05/1980",
            gender: "Homme",
            prescriptions: [
                {
                    medication: "Paracétamol",
                    dosage: "500mg",
                    frequency: "3 fois par jour",
                    duration: "7 jours",
                    instructions: "Prendre après les repas",
                    date: "12/06/2023",
                    status: "active"
                },
                {
                    medication: "Ibuprofène",
                    dosage: "400mg",
                    frequency: "2 fois par jour",
                    duration: "5 jours",
                    instructions: "Prendre en cas de douleur",
                    date: "10/06/2023",
                    status: "completed"
                }
            ],
            notes: [
                {
                    type: "diagnosis",
                    text: "Patient présentant des symptômes de migraine chronique. Recommandation de consulter un neurologue pour examen plus approfondi.",
                    date: "12/06/2023"
                },
                {
                    type: "observation",
                    text: "Le patient semble répondre positivement au traitement. La fréquence des migraines a diminué de 50%.",
                    date: "15/06/2023"
                }
            ],
            appointments: [
                {
                    date: "15/06/2023",
                    time: "10:30",
                    status: "completed",
                    reason: "Contrôle suite à traitement migraine"
                },
                {
                    date: "20/05/2023",
                    time: "14:00",
                    status: "completed",
                    reason: "Consultation pour migraines persistantes"
                }
            ],
            vitals: [
                {
                    date: "15/06/2023",
                    systolic: 120,
                    diastolic: 80,
                    heartRate: 72,
                    temperature: 36.8,
                    oxygen: 98,
                    notes: "Signes vitaux normaux"
                }
            ]
        },
        {
            id: 2,
            name: "Marie Martin",
            email: "marie.martin@example.com",
            phone: "06 12 34 56 78",
            dob: "22/11/1992",
            gender: "Femme",
            prescriptions: [
                {
                    medication: "Vitamine D",
                    dosage: "1000 UI",
                    frequency: "1 fois par jour",
                    duration: "30 jours",
                    instructions: "Prendre le matin",
                    date: "05/06/2023",
                    status: "active"
                }
            ],
            notes: [
                {
                    type: "diagnosis",
                    text: "Carence en vitamine D détectée lors des derniers examens sanguins.",
                    date: "05/06/2023"
                }
            ],
            appointments: [
                {
                    date: "05/06/2023",
                    time: "11:15",
                    status: "completed",
                    reason: "Résultats d'analyses sanguines"
                }
            ],
            vitals: []
        },
        {
            id: 3,
            name: "Pierre Leroy",
            email: "p.leroy@example.com",
            phone: "07 89 01 23 45",
            dob: "03/03/1975",
            gender: "Homme",
            prescriptions: [],
            notes: [],
            appointments: [],
            vitals: []
        }
    ];

    // Function to search patients
    function searchPatients(term) {
        if (!term) return [];
        
        return samplePatients.filter(patient => 
            patient.name.toLowerCase().includes(term.toLowerCase()) ||
            patient.email.toLowerCase().includes(term.toLowerCase()) ||
            patient.phone.includes(term)
        );
    }

    // Function to display search results
    function displaySearchResults(results) {
        const resultsContainer = document.getElementById('searchResults');
        
        if (results.length === 0) {
            resultsContainer.innerHTML = '<div class="search-result-item">Aucun patient trouvé</div>';
            resultsContainer.style.display = 'block';
            return;
        }
        
        resultsContainer.innerHTML = '';
        results.forEach(patient => {
            const item = document.createElement('div');
            item.className = 'search-result-item';
            item.innerHTML = `
                <strong>${patient.name}</strong>
                <div style="font-size: 13px; color: #666;">
                    ${patient.email} | ${patient.phone}
                </div>
            `;
            item.onclick = () => selectPatient(patient);
            resultsContainer.appendChild(item);
        });
        
        resultsContainer.style.display = 'block';
    }

    // Function to select a patient
    function selectPatient(patient) {
        // Hide search results
        document.getElementById('searchResults').style.display = 'none';
        
        // Show patient header and populate data
        const patientHeader = document.getElementById('patientHeader');
        patientHeader.style.display = 'block';
        
        document.getElementById('patientName').textContent = patient.name;
        document.getElementById('patientEmail').textContent = patient.email;
        document.getElementById('patientPhone').textContent = patient.phone;
        document.getElementById('patientDob').textContent = patient.dob;
        document.getElementById('patientGender').textContent = patient.gender;
        
        // Show tabs
        document.getElementById('tabsContainer').style.display = 'flex';
        
        // Populate prescriptions
        populatePrescriptions(patient.prescriptions);
        
        // Populate notes
        populateNotes(patient.notes);
        
        // Populate appointments
        populateAppointments(patient.appointments);
        
        // Populate vitals
        populateVitals(patient.vitals);
    }

    // Function to populate prescriptions
    function populatePrescriptions(prescriptions) {
        const container = document.getElementById('prescriptionsList');
        
        if (prescriptions.length === 0) {
            container.innerHTML = `
                <div class="empty-state">
                    <i class="fas fa-prescription"></i>
                    <p>Aucune prescription enregistrée pour ce patient.</p>
                </div>
            `;
            return;
        }
        
        container.innerHTML = '';
        prescriptions.forEach(prescription => {
            const item = document.createElement('div');
            item.className = 'record-item';
            item.innerHTML = `
                <div class="record-header">
                    <span class="record-title">${prescription.medication}</span>
                    <span class="record-date">${prescription.date}</span>
                </div>
                
                <div class="record-details">
                    <div class="record-detail">
                        <span class="detail-label">Dosage:</span>
                        <span>${prescription.dosage}</span>
                    </div>
                    <div class="record-detail">
                        <span class="detail-label">Fréquence:</span>
                        <span>${prescription.frequency}</span>
                    </div>
                    <div class="record-detail">
                        <span class="detail-label">Durée:</span>
                        <span>${prescription.duration}</span>
                    </div>
                    <div class="record-detail">
                        <span class="detail-label">Instructions:</span>
                        <span>${prescription.instructions}</span>
                    </div>
                </div>
                
                <div class="record-footer">
                    <div>
                        <span class="status-badge status-${prescription.status}">
                            ${prescription.status === 'active' ? 'Actif' : 'Terminé'}
                        </span>
                    </div>
                    <div>
                        Prescrit par Dr. Test Doctor
                    </div>
                </div>
            `;
            container.appendChild(item);
        });
    }

    // Function to populate notes
    function populateNotes(notes) {
        const container = document.getElementById('notesList');
        
        if (notes.length === 0) {
            container.innerHTML = `
                <div class="empty-state">
                    <i class="fas fa-notes-medical"></i>
                    <p>Aucune note médicale enregistrée pour ce patient.</p>
                </div>
            `;
            return;
        }
        
        container.innerHTML = '';
        notes.forEach(note => {
            let noteClass = '';
            let noteTypeText = '';
            
            switch (note.type) {
                case 'diagnosis':
                    noteClass = 'note-type-diagnosis';
                    noteTypeText = 'Diagnostic';
                    break;
                case 'observation':
                    noteClass = 'note-type-observation';
                    noteTypeText = 'Observation';
                    break;
                case 'treatment':
                    noteClass = 'note-type-treatment';
                    noteTypeText = 'Traitement';
                    break;
                case 'follow_up':
                    noteClass = 'note-type-follow_up';
                    noteTypeText = 'Suivi';
                    break;
            }
            
            const item = document.createElement('div');
            item.className = 'record-item';
            item.innerHTML = `
                <div class="record-header">
                    <div>
                        <span class="note-type-badge ${noteClass}">${noteTypeText}</span>
                        <span class="record-title">Note Médicale</span>
                    </div>
                    <span class="record-date">${note.date}</span>
                </div>
                
                <div class="record-details">
                    <p>${note.text}</p>
                </div>
                
                <div class="record-footer">
                    <span>Ajoutée par Dr. Test Doctor</span>
                </div>
            `;
            container.appendChild(item);
        });
    }

    // Function to populate appointments
    function populateAppointments(appointments) {
        const container = document.getElementById('appointmentsList');
        
        if (appointments.length === 0) {
            container.innerHTML = `
                <div class="empty-state">
                    <i class="fas fa-calendar-times"></i>
                    <p>Aucun rendez-vous enregistré pour ce patient.</p>
                </div>
            `;
            return;
        }
        
        container.innerHTML = '';
        appointments.forEach(appointment => {
            const item = document.createElement('div');
            item.className = 'record-item';
            item.innerHTML = `
                <div class="record-header">
                    <span class="record-title">Rendez-vous</span>
                    <span class="record-date">${appointment.date} ${appointment.time}</span>
                </div>
                
                <div class="record-details">
                    <div class="record-detail">
                        <span class="detail-label">Statut:</span>
                        <span>${appointment.status === 'completed' ? 'Terminé' : 'Planifié'}</span>
                    </div>
                    <div class="record-detail">
                        <span class="detail-label">Motif:</span>
                        <span>${appointment.reason}</span>
                    </div>
                </div>
            `;
            container.appendChild(item);
        });
    }

    // Function to populate vitals
    function populateVitals(vitals) {
        const container = document.getElementById('vitalsList');
        
        if (vitals.length === 0) {
            container.innerHTML = `
                <div class="empty-state">
                    <i class="fas fa-heartbeat"></i>
                    <p>Aucune mesure de signes vitaux enregistrée pour ce patient.</p>
                </div>
            `;
            return;
        }
        
        container.innerHTML = '';
        vitals.forEach(vital => {
            const item = document.createElement('div');
            item.className = 'record-item';
            item.innerHTML = `
                <div class="record-header">
                    <span class="record-title">Mesure des signes vitaux</span>
                    <span class="record-date">${vital.date}</span>
                </div>
                
                <div class="record-details">
                    <div class="record-detail">
                        <span class="detail-label">Tension artérielle:</span>
                        <span>${vital.systolic}/${vital.diastolic} mmHg</span>
                    </div>
                    <div class="record-detail">
                        <span class="detail-label">Rythme cardiaque:</span>
                        <span>${vital.heartRate} bpm</span>
                    </div>
                    <div class="record-detail">
                        <span class="detail-label">Température:</span>
                        <span>${vital.temperature} °C</span>
                    </div>
                    <div class="record-detail">
                        <span class="detail-label">Saturation O2:</span>
                        <span>${vital.oxygen}%</span>
                    </div>
                    <div class="record-detail">
                        <span class="detail-label">Notes:</span>
                        <span>${vital.notes}</span>
                    </div>
                </div>
            `;
            container.appendChild(item);
        });
    }

    // Function to toggle forms
    function toggleForm(formId) {
        const form = document.getElementById(formId);
        if (form.style.display === 'block') {
            form.style.display = 'none';
        } else {
            form.style.display = 'block';
        }
    }
    
    // Tab functionality
    document.addEventListener('DOMContentLoaded', function() {
        const tabs = document.querySelectorAll('.tab');
        const tabContents = document.querySelectorAll('.tab-content');
        
        tabs.forEach(tab => {
            tab.addEventListener('click', function() {
                const tabId = this.getAttribute('data-tab');
                
                // Remove active class from all tabs and contents
                tabs.forEach(t => t.classList.remove('active'));
                tabContents.forEach(c => c.classList.remove('active'));
                
                // Add active class to current tab and content
                this.classList.add('active');
                document.getElementById(tabId + '-tab').classList.add('active');
            });
        });
        
        // Handle search form submission
        document.getElementById('searchForm').addEventListener('submit', function(e) {
            e.preventDefault();
            const searchTerm = this.search_term.value;
            const results = searchPatients(searchTerm);
            displaySearchResults(results);
        });
    });
    </script>
</body>
</html>