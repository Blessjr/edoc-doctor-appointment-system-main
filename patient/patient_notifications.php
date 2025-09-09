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

// Fetch all notifications for this patient
$notifications = array();
$notification_query = $database->query("
    SELECT * FROM notifications 
    WHERE user_id = '$userid' OR user_type = 'p' OR user_type = 'all'
    ORDER BY created_at DESC
");

if($notification_query && $notification_query->num_rows > 0) {
    while($row = $notification_query->fetch_assoc()) {
        $notifications[] = $row;
    }
}

// Mark all as read when viewing all notifications
$database->query("UPDATE notifications SET is_read = 1 WHERE (user_id = '$userid' OR user_type = 'p' OR user_type = 'all') AND is_read = 0");
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Notifications | Docto Link</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        /* Add the same styles as in index.php for consistency */
        .notification-container {
            max-width: 800px;
            margin: 20px auto;
            background: white;
            border-radius: 8px;
            box-shadow: 0 5px 15px rgba(0,0,0,0.1);
            overflow: hidden;
        }
        
        .notification-header {
            background: #2c5cc7;
            color: white;
            padding: 20px;
            text-align: center;
        }
        
        .notification-list {
            padding: 0;
            margin: 0;
            list-style: none;
        }
        
        .notification-item {
            padding: 20px;
            border-bottom: 1px solid #f1f1f1;
            display: flex;
            align-items: flex-start;
        }
        
        .notification-icon {
            margin-right: 15px;
            font-size: 20px;
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
            padding: 40px 20px;
            text-align: center;
            color: #999;
        }
        
        .back-button {
            display: inline-block;
            margin: 20px;
            padding: 10px 20px;
            background: #2c5cc7;
            color: white;
            text-decoration: none;
            border-radius: 5px;
        }
    </style>
</head>
<body>
    <a href="index.php" class="back-button"><i class="fas fa-arrow-left"></i> Retour à l'accueil</a>
    
    <div class="notification-container">
        <div class="notification-header">
            <h1><i class="fas fa-bell"></i> Toutes vos notifications</h1>
        </div>
        
        <ul class="notification-list">
            <?php if (count($notifications) > 0): ?>
                <?php foreach ($notifications as $notification): ?>
                    <li class="notification-item">
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
                <li class="notification-empty">
                    <i class="fas fa-bell-slash" style="font-size: 50px; margin-bottom: 15px;"></i>
                    <p>Aucune notification</p>
                </li>
            <?php endif; ?>
        </ul>
    </div>
</body>
</html>