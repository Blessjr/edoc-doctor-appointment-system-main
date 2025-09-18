<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Mes Rendez-vous - Docteur</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        }
        
        :root {
            --main-color: #4a6bdf;
            --secondary-color: #3a5bc7;
            --light-color: #f8f9fa;
            --dark-color: #343a40;
            --success-color: #28a745;
            --warning-color: #ffc107;
            --danger-color: #dc3545;
            --gray-color: #6c757d;
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
            background: var(--main-color);
            color: white;
            padding: 20px 0;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
        }
        
        .menu-container {
            width: 100%;
        }
        
        .profile-container {
            width: 100%;
            padding: 10px 20px;
            text-align: center;
            border-bottom: 1px solid rgba(255, 255, 255, 0.2);
            margin-bottom: 20px;
        }
        
        .profile-container img {
            width: 80px;
            height: 80px;
            border-radius: 50%;
            object-fit: cover;
            border: 3px solid white;
        }
        
        .profile-title {
            font-weight: 600;
            font-size: 16px;
            margin-top: 10px;
        }
        
        .profile-subtitle {
            font-size: 13px;
            opacity: 0.8;
        }
        
        .logout-btn {
            width: 100%;
            padding: 10px;
            background: rgba(255, 255, 255, 0.1);
            border: none;
            color: white;
            border-radius: 5px;
            cursor: pointer;
            margin-top: 10px;
            transition: all 0.3s;
        }
        
        .logout-btn:hover {
            background: rgba(255, 255, 255, 0.2);
        }
        
        .menu-row {
            border-bottom: 1px solid rgba(255, 255, 255, 0.1);
        }
        
        .menu-btn {
            padding: 15px 20px;
            display: block;
            color: white;
            text-decoration: none;
            transition: all 0.3s;
        }
        
        .menu-btn:hover {
            background: rgba(255, 255, 255, 0.1);
        }
        
        .menu-active {
            background: var(--secondary-color);
        }
        
        .menu-text {
            margin-left: 10px;
        }
        
        /* Main Content Styles */
        .dash-body {
            flex: 1;
            padding: 20px;
            overflow-y: auto;
        }
        
        .heading-main12 {
            font-size: 24px;
            font-weight: 600;
            color: var(--dark-color);
            margin-bottom: 20px;
        }
        
        .heading-sub12 {
            font-size: 14px;
            color: var(--gray-color);
        }
        
        .filter-container {
            background: white;
            border-radius: 10px;
            padding: 20px;
            margin-bottom: 20px;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.05);
        }
        
        .input-text {
            padding: 10px 15px;
            border: 1px solid #ddd;
            border-radius: 5px;
            width: 100%;
            font-size: 14px;
        }
        
        .btn-primary {
            background: var(--main-color);
            color: white;
            border: none;
            padding: 10px 20px;
            border-radius: 5px;
            cursor: pointer;
            font-weight: 500;
            transition: all 0.3s;
        }
        
        .btn-primary:hover {
            background: var(--secondary-color);
        }
        
        .btn-primary-soft {
            background: rgba(74, 107, 223, 0.1);
            color: var(--main-color);
            border: none;
            padding: 10px 20px;
            border-radius: 5px;
            cursor: pointer;
            font-weight: 500;
            transition: all 0.3s;
        }
        
        .btn-primary-soft:hover {
            background: rgba(74, 107, 223, 0.2);
        }
        
        /* Appointment Cards */
        .appointment-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(300px, 1fr));
            gap: 20px;
            margin-top: 20px;
        }
        
        .appointment-card {
            background: white;
            border-radius: 10px;
            padding: 20px;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.05);
            transition: transform 0.3s;
        }
        
        .appointment-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.1);
        }
        
        .appointment-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 15px;
            padding-bottom: 15px;
            border-bottom: 1px solid #eee;
        }
        
        .appointment-id {
            font-size: 14px;
            color: var(--gray-color);
        }
        
        .appointment-status {
            padding: 5px 10px;
            border-radius: 20px;
            font-size: 12px;
            font-weight: 600;
        }
        
        .status-scheduled {
            background-color: #e8f4ff;
            color: #0066cc;
        }
        
        .status-in-progress {
            background-color: #fff8e1;
            color: #ff8f00;
        }
        
        .status-completed {
            background-color: #e6f7ee;
            color: #00a65c;
        }
        
        .status-cancelled {
            background-color: #ffe9e9;
            color: #ff3b3b;
        }
        
        .appointment-patient {
            font-size: 18px;
            font-weight: 600;
            margin-bottom: 10px;
        }
        
        .appointment-details {
            margin-bottom: 15px;
        }
        
        .appointment-detail {
            display: flex;
            margin-bottom: 8px;
            font-size: 14px;
        }
        
        .detail-label {
            width: 120px;
            color: var(--gray-color);
        }
        
        .detail-value {
            font-weight: 500;
        }
        
        .appointment-actions {
            display: flex;
            gap: 10px;
        }
        
        /* Call Button */
        .call-button-container {
            text-align: center;
            margin: 30px 0;
        }
        
        .call-button {
            padding: 15px 30px;
            background: var(--main-color);
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
            background: var(--secondary-color);
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
        
        /* Video Call Modal */
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
            background: var(--main-color);
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
        
        /* Table Styles */
        .sub-table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
            background: white;
            border-radius: 10px;
            overflow: hidden;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.05);
        }
        
        .sub-table th {
            background: #f8f9fa;
            padding: 15px;
            text-align: left;
            font-weight: 600;
            color: var(--dark-color);
            border-bottom: 1px solid #eee;
        }
        
        .sub-table td {
            padding: 15px;
            border-bottom: 1px solid #eee;
        }
        
        .sub-table tr:last-child td {
            border-bottom: none;
        }
        
        .btn-icon-back {
            display: flex;
            align-items: center;
            gap: 8px;
        }
        
        /* Responsive */
        @media (max-width: 992px) {
            .container {
                flex-direction: column;
            }
            
            .menu {
                width: 100%;
                padding: 10px;
            }
            
            .appointment-grid {
                grid-template-columns: 1fr;
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
                                    <p class="profile-title">Dr. Dupont</p>
                                    <p class="profile-subtitle">dr.dupont@edoc.com</p>
                                </td>
                            </tr>
                            <tr>
                                <td colspan="2">
                                    <a href="../logout.php"><input type="button" value="Déconnexion" class="logout-btn btn-primary-soft btn"></a>
                                </td>
                            </tr>
                        </table>
                    </td>
                </tr>
                <tr class="menu-row">
                    <td class="menu-btn menu-icon-dashbord">
                        <a href="index.php" class="non-style-link-menu"><div><p class="menu-text">Tableau de bord</p></a></div>
                    </td>
                </tr>
                <tr class="menu-row">
                    <td class="menu-btn menu-icon-appoinment menu-active menu-icon-appoinment-active">
                        <a href="appointment.php" class="non-style-link-menu non-style-link-menu-active"><div><p class="menu-text">Mes Rendez-vous</p></a></div>
                    </td>
                </tr>
                <tr class="menu-row">
                    <td class="menu-btn menu-icon-session">
                        <a href="schedule.php" class="non-style-link-menu"><div><p class="menu-text">Mes Séances</p></div></a>
                    </td>
                </tr>
                <tr class="menu-row">
                    <td class="menu-btn menu-icon-patient">
                        <a href="patient.php" class="non-style-link-menu"><div><p class="menu-text">Mes Patients</p></a></div>
                    </td>
                </tr>
                <tr class="menu-row">
                    <td class="menu-btn menu-icon-settings">
                        <a href="settings.php" class="non-style-link-menu"><div><p class="menu-text">Paramètres</p></a></div>
                    </td>
                </tr>
            </table>
        </div>

        <div class="dash-body">
            <table border="0" width="100%" style="border-spacing: 0;margin:0;padding:0;margin-top:25px;">
                <tr>
                    <td width="13%">
                        <a href="appointment.php"><button class="login-btn btn-primary-soft btn btn-icon-back" style="padding-top:11px;padding-bottom:11px;margin-left:20px;width:125px"><font class="tn-in-text">Retour</font></button></a>
                    </td>
                    <td>
                        <p style="font-size: 23px;padding-left:12px;font-weight: 600;">Gestionnaire de Rendez-vous</p>
                    </td>
                    <td width="15%">
                        <p style="font-size: 14px;color: rgb(119, 119, 119);padding: 0;margin: 0;text-align: right;">
                            Date d'aujourd'hui
                        </p>
                        <p class="heading-sub12" style="padding: 0;margin: 0;">
                            <?php 
                            date_default_timezone_set('Africa/Douala');
                            echo date('d/m/Y');
                            ?>
                        </p>
                    </td>
                    <td width="10%">
                        <button class="btn-label" style="display: flex;justify-content: center;align-items: center;"><img src="../img/calendar.svg" width="100%"></button>
                    </td>
                </tr>
                
                <tr>
                    <td colspan="4" style="padding-top:10px;width: 100%;">
                        <p class="heading-main12" style="margin-left: 45px;font-size:18px;color:rgb(49, 49, 49)">Mes Rendez-vous (3)</p>
                    </td>
                </tr>
                
                <tr>
                    <td colspan="4" style="padding-top:0px;width: 100%;">
                        <center>
                            <table class="filter-container" border="0">
                                <tr>
                                    <td width="10%"></td>
                                    <td width="5%" style="text-align: center;">Date:</td>
                                    <td width="30%">
                                        <form action="" method="post">
                                            <input type="date" name="sheduledate" id="date" class="input-text filter-container-items" style="margin: 0;width: 95%;">
                                    </td>
                                    <td width="12%">
                                        <input type="submit" name="filter" value="Filtrer" class="btn-primary-soft btn button-icon btn-filter" style="padding: 15px; margin :0;width:100%">
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
                            <button class="call-button" onclick="openVideoCall()" id="videoCallButton" disabled>
                                <i class="fas fa-video"></i> Démarrer un Appel Vidéo
                            </button>
                            <p id="callStatusMessage" style="margin-top: 10px; color: #666;">
                                <i class="fas fa-info-circle"></i> L'appel vidéo sera disponible pendant vos rendez-vous
                            </p>
                        </div>
                    </td>
                </tr>
                
                <tr>
                    <td colspan="4">
                        <center>
                            <div class="abc scroll">
                                <table width="100%" class="sub-table scrolldown" border="0">
                                    <thead>
                                        <tr>
                                            <th class="table-headin">Nom du patient</th>
                                            <th class="table-headin">Numéro de rendez-vous</th>
                                            <th class="table-headin">Titre de la séance</th>
                                            <th class="table-headin">Date et heure de la séance</th>
                                            <th class="table-headin">Date du rendez-vous</th>
                                            <th class="table-headin">Actions</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <tr>
                                            <td style="font-weight:600;">Marie Lambert</td>
                                            <td style="text-align:center;font-size:23px;font-weight:500; color: var(--btnnicetext);">1</td>
                                            <td>Consultation générale</td>
                                            <td style="text-align:center;">
                                                19/06/2023 @09:00
                                            </td>
                                            <td style="text-align:center;">
                                                15/06/2023
                                            </td>
                                            <td>
                                                <div style="display:flex;justify-content: center;">
                                                    <a href="?action=drop&id=1&name=Marie Lambert&session=Consultation générale&apponum=1" class="non-style-link"><button class="btn-primary-soft btn button-icon btn-delete" style="padding-left: 40px;padding-top: 12px;padding-bottom: 12px;margin-top: 10px;"><font class="tn-in-text">Annuler</font></button></a>
                                                </div>
                                            </td>
                                        </tr>
                                        <tr>
                                            <td style="font-weight:600;">Jean Petit</td>
                                            <td style="text-align:center;font-size:23px;font-weight:500; color: var(--btnnicetext);">2</td>
                                            <td>Suivi médical</td>
                                            <td style="text-align:center;">
                                                19/06/2023 @10:30
                                            </td>
                                            <td style="text-align:center;">
                                                16/06/2023
                                            </td>
                                            <td>
                                                <div style="display:flex;justify-content: center;">
                                                    <a href="?action=drop&id=2&name=Jean Petit&session=Suivi médical&apponum=2" class="non-style-link"><button class="btn-primary-soft btn button-icon btn-delete" style="padding-left: 40px;padding-top: 12px;padding-bottom: 12px;margin-top: 10px;"><font class="tn-in-text">Annuler</font></button></a>
                                                </div>
                                            </td>
                                        </tr>
                                        <tr>
                                            <td style="font-weight:600;">Sophie Martin</td>
                                            <td style="text-align:center;font-size:23px;font-weight:500; color: var(--btnnicetext);">3</td>
                                            <td>Consultation spécialisée</td>
                                            <td style="text-align:center;">
                                                19/06/2023 @14:00
                                            </td>
                                            <td style="text-align:center;">
                                                17/06/2023
                                            </td>
                                            <td>
                                                <div style="display:flex;justify-content: center;">
                                                    <a href="?action=drop&id=3&name=Sophie Martin&session=Consultation spécialisée&apponum=3" class="non-style-link"><button class="btn-primary-soft btn button-icon btn-delete" style="padding-left: 40px;padding-top: 12px;padding-bottom: 12px;margin-top: 10px;"><font class="tn-in-text">Annuler</font></button></a>
                                                </div>
                                            </td>
                                        </tr>
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

    <script>
        // Set Cameroon timezone
        const cameroonTimeOffset = 1; // UTC+1 for Cameroon
        
        // Function to get current Cameroon time
        function getCameroonTime() {
            const now = new Date();
            const utc = now.getTime() + (now.getTimezoneOffset() * 60000);
            return new Date(utc + (3600000 * cameroonTimeOffset));
        }
        
        // Function to check if current time is within appointment time
        function checkAppointmentTime() {
            const now = getCameroonTime();
            const currentTime = now.getHours() * 60 + now.getMinutes(); // Convert to minutes
            const currentDate = now.toISOString().split('T')[0]; // Format as YYYY-MM-DD
            
            // Sample appointment data - in a real application, this would come from the server
            const appointments = [
                { date: '2023-06-19', startTime: '09:00', endTime: '09:30', patient: 'Marie Lambert' },
                { date: '2023-06-19', startTime: '10:30', endTime: '11:00', patient: 'Jean Petit' },
                { date: '2023-06-19', startTime: '14:00', endTime: '14:45', patient: 'Sophie Martin' }
            ];
            
            let activeAppointment = null;
            
            // Check if any appointment is currently active
            for (const appointment of appointments) {
                if (appointment.date === currentDate) {
                    const start = appointment.startTime.split(':');
                    const end = appointment.endTime.split(':');
                    const startMinutes = parseInt(start[0]) * 60 + parseInt(start[1]);
                    const endMinutes = parseInt(end[0]) * 60 + parseInt(end[1]);
                    
                    if (currentTime >= startMinutes - 5 && currentTime <= endMinutes + 10) {
                        activeAppointment = appointment;
                        break;
                    }
                }
            }
            
            // Update UI based on appointment status
            const videoCallButton = document.getElementById('videoCallButton');
            const callStatusMessage = document.getElementById('callStatusMessage');
            
            if (activeAppointment) {
                videoCallButton.disabled = false;
                callStatusMessage.innerHTML = `<i class="fas fa-info-circle"></i> Consultation avec ${activeAppointment.patient} en cours (${activeAppointment.startTime} - ${activeAppointment.endTime})`;
                callStatusMessage.style.color = '#28a745';
            } else {
                videoCallButton.disabled = true;
                
                // Check if there's an upcoming appointment today
                const upcomingAppointments = appointments.filter(app => {
                    if (app.date === currentDate) {
                        const start = app.startTime.split(':');
                        const startMinutes = parseInt(start[0]) * 60 + parseInt(start[1]);
                        return startMinutes > currentTime;
                    }
                    return false;
                });
                
                if (upcomingAppointments.length > 0) {
                    const nextAppointment = upcomingAppointments[0];
                    callStatusMessage.innerHTML = `<i class="fas fa-info-circle"></i> Prochain rendez-vous à ${nextAppointment.startTime} avec ${nextAppointment.patient}`;
                } else {
                    callStatusMessage.innerHTML = `<i class="fas fa-info-circle"></i> Aucun rendez-vous programmé pour aujourd'hui`;
                }
            }
        }
        
        // Check appointment time on page load
        document.addEventListener('DOMContentLoaded', function() {
            checkAppointmentTime();
            
            // Update every minute
            setInterval(checkAppointmentTime, 60000);
            
            // Display current Cameroon time
            function updateClock() {
                const now = getCameroonTime();
                const timeString = now.toLocaleTimeString('fr-FR', { hour: '2-digit', minute: '2-digit' });
                const dateString = now.toLocaleDateString('fr-FR', { weekday: 'long', year: 'numeric', month: 'long', day: 'numeric' });
                
                document.getElementById('current-time').textContent = timeString;
                document.getElementById('current-date').textContent = dateString;
            }
            
            updateClock();
            setInterval(updateClock, 1000);
        });
        
        // MiroTalk Video Call Functions
        function openVideoCall() {
            const fullscreenDiv = document.getElementById('mirotalk-fullscreen');
            const iframe = document.getElementById('mirotalk-frame');
            
            // Set the iframe source to the direct join URL
            iframe.src = 'https://c2c.mirotalk.com/join?room=consultation&name=Dr.%20Dupont';
            
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