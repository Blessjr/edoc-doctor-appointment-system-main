<!DOCTYPE html>
<html lang="fr">
<head>
<meta charset="UTF-8">
<meta http-equiv="X-UA-Compatible" content="IE=edge">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<link rel="stylesheet" href="css/animations.css">  
<link rel="stylesheet" href="css/main.css">  
<link rel="stylesheet" href="css/login.css">
<title>Connexion</title>
</head>
<body>
<?php
session_start();

$_SESSION["user"] = "";
$_SESSION["usertype"] = "";

// Définir le fuseau horaire
date_default_timezone_set('Africa/Douala');
$date = date('Y-m-d');
$_SESSION["date"] = $date;

// Importer la base de données
include("connection.php");

if($_POST){

    $email = $_POST['useremail'];
    $password = $_POST['userpassword'];
    
    $error = '<label for="promter" class="form-label"></label>';

    $result = $database->query("SELECT * FROM webuser WHERE email='$email'");
    if($result->num_rows == 1){
        $utype = $result->fetch_assoc()['usertype'];
        if ($utype == 'p'){
            $checker = $database->query("SELECT * FROM patient WHERE pemail='$email' AND ppassword='$password'");
            if ($checker->num_rows == 1){
                $_SESSION['user'] = $email;
                $_SESSION['usertype'] = 'p';
                header('location: patient/index.php');
            } else {
                $error = '<label for="promter" class="form-label" style="color:rgb(255, 62, 62);text-align:center;">Identifiants incorrects : email ou mot de passe invalide</label>';
            }
        } elseif($utype == 'a'){
            $checker = $database->query("SELECT * FROM admin WHERE aemail='$email' AND apassword='$password'");
            if ($checker->num_rows == 1){
                $_SESSION['user'] = $email;
                $_SESSION['usertype'] = 'a';
                header('location: admin/index.php');
            } else {
                $error = '<label for="promter" class="form-label" style="color:rgb(255, 62, 62);text-align:center;">Identifiants incorrects : email ou mot de passe invalide</label>';
            }
        } elseif($utype == 'd'){
            $checker = $database->query("SELECT * FROM doctor WHERE docemail='$email' AND docpassword='$password'");
            if ($checker->num_rows == 1){
                $_SESSION['user'] = $email;
                $_SESSION['usertype'] = 'd';
                header('location: doctor/index.php');
            } else {
                $error = '<label for="promter" class="form-label" style="color:rgb(255, 62, 62);text-align:center;">Identifiants incorrects : email ou mot de passe invalide</label>';
            }
        }
    } else {
        $error = '<label for="promter" class="form-label" style="color:rgb(255, 62, 62);text-align:center;">Aucun compte trouvé pour cet email.</label>';
    }

} else {
    $error = '<label for="promter" class="form-label">&nbsp;</label>';
}
?>

<center>
<div class="container">
    <table border="0" style="margin: 0; padding: 0; width: 60%;">
        <tr>
            <td>
                <p class="header-text">Bienvenue !</p>
            </td>
        </tr>
    <div class="form-body">
        <tr>
            <td>
                <p class="sub-text">Connectez-vous avec vos informations pour continuer</p>
            </td>
        </tr>
        <tr>
            <form action="" method="POST">
            <td class="label-td">
                <label for="useremail" class="form-label">Email : </label>
            </td>
        </tr>
        <tr>
            <td class="label-td">
                <input type="email" name="useremail" class="input-text" placeholder="Adresse email" required>
            </td>
        </tr>
        <tr>
            <td class="label-td">
                <label for="userpassword" class="form-label">Mot de passe : </label>
            </td>
        </tr>
        <tr>
            <td class="label-td">
                <input type="password" name="userpassword" class="input-text" placeholder="Mot de passe" required>
            </td>
        </tr>
        <tr>
            <td><br>
                <?php echo $error ?>
            </td>
        </tr>
        <tr>
            <td>
                <input type="submit" value="SE CONNECTER" class="login-btn btn-primary btn">
            </td>
        </tr>
    </div>
        <tr>
            <td>
                <br>
                <label for="" class="sub-text" style="font-weight: 280;">Vous n'avez pas de compte ? </label>
                <a href="signup.php" class="hover-link1 non-style-link">S'inscrire</a>
                <br><br><br>
                <a href="index.html" class="hover-link1 non-style-link">Retour à l'accueil</a>
                <br><br><br>
            </td>
        </tr>            
            </form>
    </table>
</div>
</center>
</body>
</html>
