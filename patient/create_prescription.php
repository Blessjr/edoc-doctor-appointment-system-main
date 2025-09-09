<?php
session_start();
if(!isset($_SESSION["user"]) || $_SESSION["usertype"] != 'd'){
    header("location: ../login.php");
    exit();
}

include("../connection.php");

$useremail = $_SESSION["user"];
$userrow = $database->query("SELECT * FROM doctor WHERE docemail='$useremail'");
$userfetch = $userrow->fetch_assoc();
$userid = $userfetch["docid"];
$username = $userfetch["docname"];

// Fetch patients for autocomplete
$patients = [];
$patient_result = $database->query("SELECT * FROM patient");
if($patient_result){
    while($row = $patient_result->fetch_assoc()){
        $patients[] = $row;
    }
}

// Fetch appointments for the selected patient
$appointments = [];
if(isset($_GET['patient_id']) && !empty($_GET['patient_id'])) {
    $patient_id = $_GET['patient_id'];
    $appointment_result = $database->query("
        SELECT a.*, s.title 
        FROM appointment a 
        LEFT JOIN schedule s ON a.scheduleid = s.scheduleid 
        WHERE a.pid = '$patient_id' AND a.docid = '$userid'
        ORDER BY a.appodate DESC
    ");
    if($appointment_result){
        while($row = $appointment_result->fetch_assoc()){
            $appointments[] = $row;
        }
    }
}

// Handle form submission
$message = "";
if(isset($_POST['submit'])){
    $patient_id = $_POST['patient_id'];
    $appointment_id = !empty($_POST['appointment_id']) ? $_POST['appointment_id'] : NULL;
    $date = date('Y-m-d');

    // Medications arrays
    $medications = $_POST['medication_name'];
    $dosages = $_POST['dosage'];
    $frequencies = $_POST['frequency'];
    $durations = $_POST['duration'];
    $instructions_list = $_POST['instructions'];

    $errors = [];
    $stmt = $database->prepare("INSERT INTO prescriptions (patient_id, doctor_id, appointment_id, prescription_date, medication_name, dosage, frequency, duration, instructions, status) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, 'active')");

    for($i=0; $i<count($medications); $i++){
        if(trim($medications[$i]) != ""){
            $stmt->bind_param("iiissssss", $patient_id, $userid, $appointment_id, $date, $medications[$i], $dosages[$i], $frequencies[$i], $durations[$i], $instructions_list[$i]);
            if(!$stmt->execute()){
                $errors[] = $stmt->error;
            }
        }
    }

    if(empty($errors)){
        $message = "Prescription(s) créée(s) avec succès !";
    } else {
        $message = "Erreur(s) lors de la création : " . implode(", ", $errors);
    }
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Créer une Prescription</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        * {
            box-sizing: border-box;
            margin: 0;
            padding: 0;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        }
        
        body {
            background-color: #f0f5ff;
            color: #333;
            line-height: 1.6;
        }
        
        .container {
            max-width: 1000px;
            margin: 20px auto;
            background: #fff;
            padding: 30px;
            border-radius: 12px;
            box-shadow: 0 8px 30px rgba(0, 0, 0, 0.1);
        }
        
        .header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 30px;
            padding-bottom: 20px;
            border-bottom: 1px solid #eaeaea;
        }
        
        .header h2 {
            color: #2c5cc7;
            font-weight: 600;
        }
        
        .doctor-info {
            text-align: right;
            font-size: 14px;
            color: #666;
        }
        
        .form-section {
            margin-bottom: 25px;
        }
        
        .section-title {
            font-size: 18px;
            color: #2c5cc7;
            margin-bottom: 15px;
            padding-bottom: 8px;
            border-bottom: 2px solid #eaeaea;
            display: flex;
            align-items: center;
        }
        
        .section-title i {
            margin-right: 10px;
        }
        
        .form-group {
            margin-bottom: 20px;
        }
        
        label {
            display: block;
            margin-bottom: 8px;
            font-weight: 500;
            color: #444;
        }
        
        input, select, textarea {
            width: 100%;
            padding: 12px 15px;
            border: 1px solid #ddd;
            border-radius: 6px;
            font-size: 15px;
            transition: all 0.3s;
        }
        
        input:focus, select:focus, textarea:focus {
            outline: none;
            border-color: #2c5cc7;
            box-shadow: 0 0 0 3px rgba(44, 92, 199, 0.1);
        }
        
        .patient-search-container {
            position: relative;
        }
        
        .patient-results {
            position: absolute;
            top: 100%;
            left: 0;
            right: 0;
            background: white;
            border: 1px solid #ddd;
            border-top: none;
            border-radius: 0 0 6px 6px;
            max-height: 200px;
            overflow-y: auto;
            z-index: 100;
            display: none;
            box-shadow: 0 4px 10px rgba(0, 0, 0, 0.1);
        }
        
        .patient-result {
            padding: 10px 15px;
            cursor: pointer;
            border-bottom: 1px solid #eee;
        }
        
        .patient-result:hover {
            background-color: #f0f5ff;
        }
        
        .appointment-select {
            display: none;
            margin-top: 15px;
        }
        
        .medication-row {
            display: flex;
            gap: 15px;
            margin-bottom: 15px;
            padding: 15px;
            background: #f9fafc;
            border-radius: 8px;
            border: 1px solid #eee;
            transition: all 0.3s;
        }
        
        .medication-row:hover {
            background: #f0f5ff;
            border-color: #d0d9f0;
        }
        
        .medication-name {
            flex: 2;
        }
        
        .medication-dosage, .medication-frequency, .medication-duration {
            flex: 1;
        }
        
        .medication-instructions {
            flex: 2;
        }
        
        .btn-row {
            display: flex;
            justify-content: space-between;
            margin-top: 30px;
        }
        
        .btn {
            padding: 12px 25px;
            border: none;
            border-radius: 6px;
            cursor: pointer;
            font-size: 15px;
            font-weight: 500;
            transition: all 0.3s;
        }
        
        .btn-primary {
            background: #2c5cc7;
            color: white;
        }
        
        .btn-primary:hover {
            background: #1e4bb3;
        }
        
        .btn-secondary {
            background: #6c757d;
            color: white;
        }
        
        .btn-secondary:hover {
            background: #5a6268;
        }
        
        .btn-danger {
            background: #dc3545;
            color: white;
        }
        
        .btn-danger:hover {
            background: #c82333;
        }
        
        .btn-success {
            background: #28a745;
            color: white;
        }
        
        .btn-success:hover {
            background: #218838;
        }
        
        .message {
            padding: 15px;
            margin-bottom: 20px;
            border-radius: 6px;
            font-weight: 500;
        }
        
        .success {
            background: #d4edda;
            color: #155724;
            border: 1px solid #c3e6cb;
        }
        
        .error {
            background: #f8d7da;
            color: #721c24;
            border: 1px solid #f5c6cb;
        }
        
        .form-actions {
            display: flex;
            gap: 10px;
        }
        
        .remove-btn {
            background: none;
            border: none;
            color: #dc3545;
            cursor: pointer;
            font-size: 18px;
            align-self: center;
            padding: 5px 10px;
        }
        
        .remove-btn:hover {
            color: #bd2130;
        }
        
        @media (max-width: 768px) {
            .medication-row {
                flex-direction: column;
            }
            
            .btn-row {
                flex-direction: column;
                gap: 10px;
            }
            
            .form-actions {
                flex-direction: column;
            }
        }
        
        .signature-pad {
            border: 1px solid #ddd;
            border-radius: 6px;
            margin-top: 10px;
            background: white;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h2><i class="fas fa-prescription"></i> Créer une Prescription</h2>
            <div class="doctor-info">
                <div>Dr. <?php echo $username; ?></div>
                <div><?php echo date('d/m/Y'); ?></div>
            </div>
        </div>
        
        <?php if($message): ?>
            <div class="message <?php echo strpos($message, 'succès') !== false ? 'success' : 'error'; ?>">
                <?php echo $message; ?>
            </div>
        <?php endif; ?>
        
        <form method="POST" id="prescriptionForm">
            <div class="form-section">
                <div class="section-title">
                    <i class="fas fa-user-injured"></i> Informations Patient
                </div>
                
                <div class="form-group">
                    <label for="patientInput">Rechercher un patient :</label>
                    <div class="patient-search-container">
                        <input type="text" id="patientInput" name="patient_name" placeholder="Nom du patient..." autocomplete="off" required>
                        <div class="patient-results" id="patientResults"></div>
                    </div>
                    <input type="hidden" name="patient_id" id="patient_id">
                </div>
                
                <div class="form-group" id="patientInfo" style="display: none;">
                    <div style="background: #f9fafc; padding: 15px; border-radius: 8px;">
                        <h4>Informations du patient sélectionné</h4>
                        <div id="patientDetails"></div>
                    </div>
                </div>
                
                <div class="form-group appointment-select" id="appointmentSelect">
                    <label for="appointment_id">Associer à un rendez-vous (optionnel) :</label>
                    <select name="appointment_id" id="appointment_id">
                        <option value="">Aucun rendez-vous</option>
                        <!-- Appointments will be populated via JavaScript -->
                    </select>
                </div>
            </div>
            
            <div class="form-section">
                <div class="section-title">
                    <i class="fas fa-pills"></i> Médicaments Prescrits
                </div>
                
                <div id="medicationsContainer">
                    <div class="medication-row">
                        <div class="medication-name">
                            <label>Médicament</label>
                            <input type="text" name="medication_name[]" placeholder="Nom du médicament" required>
                        </div>
                        
                        <div class="medication-dosage">
                            <label>Dosage</label>
                            <input type="text" name="dosage[]" placeholder="ex: 500mg" required>
                        </div>
                        
                        <div class="medication-frequency">
                            <label>Fréquence</label>
                            <select name="frequency[]" required>
                                <option value="">Sélectionner</option>
                                <option value="1 fois par jour">1 fois par jour</option>
                                <option value="2 fois par jour">2 fois par jour</option>
                                <option value="3 fois par jour">3 fois par jour</option>
                                <option value="4 fois par jour">4 fois par jour</option>
                                <option value="Au besoin">Au besoin</option>
                                <option value="Autre">Autre</option>
                            </select>
                        </div>
                        
                        <div class="medication-duration">
                            <label>Durée</label>
                            <input type="text" name="duration[]" placeholder="ex: 7 jours" required>
                        </div>
                        
                        <div class="medication-instructions">
                            <label>Instructions</label>
                            <textarea name="instructions[]" placeholder="Instructions spécifiques..." rows="1"></textarea>
                        </div>
                        
                        <button type="button" class="remove-btn" title="Supprimer ce médicament">
                            <i class="fas fa-trash"></i>
                        </button>
                    </div>
                </div>
                
                <button type="button" class="btn btn-success" id="addMedication">
                    <i class="fas fa-plus"></i> Ajouter un médicament
                </button>
            </div>
            
            <div class="form-section">
                <div class="section-title">
                    <i class="fas fa-stethoscope"></i> Instructions Additionnelles
                </div>
                
                <div class="form-group">
                    <label for="additional_instructions">Instructions générales et recommandations :</label>
                    <textarea id="additional_instructions" name="additional_instructions" rows="3" placeholder="Instructions générales, recommandations alimentaires, activités à éviter..."></textarea>
                </div>
            </div>
            
            <div class="form-section">
                <div class="section-title">
                    <i class="fas fa-file-signature"></i> Signature
                </div>
                
                <div class="form-group">
                    <label>Signature électronique</label>
                    <div>Dr. <?php echo $username; ?></div>
                    <div class="signature-pad" style="padding: 15px; margin-top: 10px;">
                        <div style="border-bottom: 1px solid #ddd; padding-bottom: 10px; margin-bottom: 10px;">
                            Signature numérique
                        </div>
                        <div><?php echo date('d/m/Y à H:i'); ?></div>
                    </div>
                </div>
            </div>
            
            <div class="btn-row">
                <div>
                    <a href="index.php" class="btn btn-secondary">
                        <i class="fas fa-arrow-left"></i> Retour
                    </a>
                </div>
                
                <div class="form-actions">
                    <button type="reset" class="btn btn-danger">
                        <i class="fas fa-times"></i> Annuler
                    </button>
                    
                    <button type="submit" name="submit" class="btn btn-primary">
                        <i class="fas fa-check"></i> Enregistrer la prescription
                    </button>
                </div>
            </div>
        </form>
    </div>

    <script>
        // Patients data from PHP
        const patients = <?php echo json_encode($patients); ?>;
        const patientInput = document.getElementById('patientInput');
        const patientResults = document.getElementById('patientResults');
        const patientIdInput = document.getElementById('patient_id');
        const patientInfo = document.getElementById('patientInfo');
        const patientDetails = document.getElementById('patientDetails');
        const appointmentSelect = document.getElementById('appointmentSelect');
        const appointmentDropdown = document.getElementById('appointment_id');
        
        // Patient search functionality
        patientInput.addEventListener('input', function() {
            const val = this.value.toLowerCase().trim();
            patientResults.innerHTML = '';
            
            if (val.length < 2) {
                patientResults.style.display = 'none';
                patientInfo.style.display = 'none';
                appointmentSelect.style.display = 'none';
                return;
            }
            
            const matches = patients.filter(p => 
                p.pname.toLowerCase().includes(val) || 
                (p.pemail && p.pemail.toLowerCase().includes(val)) ||
                (p.tel && p.tel.includes(val))
            );
            
            if (matches.length > 0) {
                patientResults.style.display = 'block';
                matches.forEach(patient => {
                    const div = document.createElement('div');
                    div.className = 'patient-result';
                    div.innerHTML = `
                        <strong>${patient.pname}</strong><br>
                        <small>${patient.pemail || 'Email non renseigné'} | ${patient.tel || 'Téléphone non renseigné'}</small>
                    `;
                    div.addEventListener('click', () => {
                        selectPatient(patient);
                    });
                    patientResults.appendChild(div);
                });
            } else {
                patientResults.style.display = 'none';
            }
        });
        
        function selectPatient(patient) {
            patientInput.value = patient.pname;
            patientIdInput.value = patient.pid;
            patientResults.style.display = 'none';
            
            // Display patient info
            patientDetails.innerHTML = `
                <p><strong>Nom:</strong> ${patient.pname}</p>
                <p><strong>Email:</strong> ${patient.pemail || 'Non renseigné'}</p>
                <p><strong>Téléphone:</strong> ${patient.tel || 'Non renseigné'}</p>
            `;
            patientInfo.style.display = 'block';
            
            // Load appointments for this patient
            loadAppointments(patient.pid);
        }
        
        function loadAppointments(patientId) {
            // Show loading state
            appointmentDropdown.innerHTML = '<option value="">Chargement des rendez-vous...</option>';
            appointmentSelect.style.display = 'block';
            
            // Fetch appointments via AJAX
            fetch(`?patient_id=${patientId}`)
                .then(response => response.text())
                .then(html => {
                    // Create a temporary DOM element to parse the PHP response
                    const parser = new DOMParser();
                    const doc = parser.parseFromString(html, 'text/html');
                    
                    // Extract the appointments from the PHP-generated JavaScript variable
                    const appointmentsScript = doc.querySelector('script');
                    if (appointmentsScript) {
                        // This is a simplified approach - in a real application, you'd use a proper API endpoint
                        eval(appointmentsScript.innerHTML);
                        
                        // Populate the dropdown
                        appointmentDropdown.innerHTML = '<option value="">Aucun rendez-vous</option>';
                        
                        if (window.appointments && window.appointments.length > 0) {
                            window.appointments.forEach(app => {
                                const option = document.createElement('option');
                                option.value = app.appoid;
                                const date = new Date(app.appodate).toLocaleDateString();
                                option.textContent = `${date} - ${app.appotime} ${app.title ? `(${app.title})` : ''}`;
                                appointmentDropdown.appendChild(option);
                            });
                        } else {
                            appointmentDropdown.innerHTML = '<option value="">Aucun rendez-vous trouvé</option>';
                        }
                    }
                })
                .catch(error => {
                    console.error('Error loading appointments:', error);
                    appointmentDropdown.innerHTML = '<option value="">Erreur de chargement</option>';
                });
        }
        
        // Close patient results when clicking outside
        document.addEventListener('click', function(e) {
            if (!patientInput.contains(e.target) && !patientResults.contains(e.target)) {
                patientResults.style.display = 'none';
            }
        });
        
        // Add medication row
        document.getElementById('addMedication').addEventListener('click', function() {
            const container = document.getElementById('medicationsContainer');
            const newRow = document.createElement('div');
            newRow.className = 'medication-row';
            newRow.innerHTML = `
                <div class="medication-name">
                    <label>Médicament</label>
                    <input type="text" name="medication_name[]" placeholder="Nom du médicament" required>
                </div>
                
                <div class="medication-dosage">
                    <label>Dosage</label>
                    <input type="text" name="dosage[]" placeholder="ex: 500mg" required>
                </div>
                
                <div class="medication-frequency">
                    <label>Fréquence</label>
                    <select name="frequency[]" required>
                        <option value="">Sélectionner</option>
                        <option value="1 fois par jour">1 fois par jour</option>
                        <option value="2 fois par jour">2 fois par jour</option>
                        <option value="3 fois par jour">3 fois par jour</option>
                        <option value="4 fois par jour">4 fois par jour</option>
                        <option value="Au besoin">Au besoin</option>
                        <option value="Autre">Autre</option>
                    </select>
                </div>
                
                <div class="medication-duration">
                    <label>Durée</label>
                    <input type="text" name="duration[]" placeholder="ex: 7 jours" required>
                </div>
                
                <div class="medication-instructions">
                    <label>Instructions</label>
                    <textarea name="instructions[]" placeholder="Instructions spécifiques..." rows="1"></textarea>
                </div>
                
                <button type="button" class="remove-btn" title="Supprimer ce médicament">
                    <i class="fas fa-trash"></i>
                </button>
            `;
            
            container.appendChild(newRow);
            
            // Add event listener to the new remove button
            newRow.querySelector('.remove-btn').addEventListener('click', function() {
                if (document.querySelectorAll('.medication-row').length > 1) {
                    newRow.remove();
                } else {
                    alert('Vous devez avoir au moins un médicament dans la prescription.');
                }
            });
        });
        
        // Add event listeners to existing remove buttons
        document.querySelectorAll('.remove-btn').forEach(btn => {
            btn.addEventListener('click', function() {
                if (document.querySelectorAll('.medication-row').length > 1) {
                    this.closest('.medication-row').remove();
                } else {
                    alert('Vous devez avoir au moins un médicament dans la prescription.');
                }
            });
        });
        
        // Form validation
        document.getElementById('prescriptionForm').addEventListener('submit', function(e) {
            let valid = true;
            const patientId = document.getElementById('patient_id').value;
            
            if (!patientId) {
                alert('Veuillez sélectionner un patient valide.');
                valid = false;
            }
            
            // Check if at least one medication is filled
            const medInputs = document.querySelectorAll('input[name="medication_name[]"]');
            let hasMedication = false;
            
            medInputs.forEach(input => {
                if (input.value.trim() !== '') {
                    hasMedication = true;
                }
            });
            
            if (!hasMedication) {
                alert('Veuillez ajouter au moins un médicament à la prescription.');
                valid = false;
            }
            
            if (!valid) {
                e.preventDefault();
            }
        });
    </script>
    
    <script>
        // Pass PHP appointments data to JavaScript
        const appointments = <?php echo json_encode($appointments); ?>;
    </script>
</body>
</html>