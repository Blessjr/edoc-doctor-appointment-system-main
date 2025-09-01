<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="css/animations.css">  
    <link rel="stylesheet" href="css/main.css">  
    <link rel="stylesheet" href="css/signup.css">
        
    <title>Créer un compte</title>
    <style>
        .container{
            animation: transitionIn-X 0.5s;
        }
    </style>
</head>
<body>
<?php

// Apprendre depuis w3schools.com
// Réinitialiser toutes les variables côté serveur

session_start();

$_SESSION["user"]="";
$_SESSION["usertype"]="";

// Définir le nouveau fuseau horaire
date_default_timezone_set('Africa/Douala');
$date = date('Y-m-d');

// Stocker la date actuelle dans la session
$_SESSION["date"]=$date;

// Importer la connexion à la base de données
include("connection.php");

// Vérifier si le formulaire a été soumis
if($_POST){

    // Sélectionner tous les utilisateurs existants
    $result= $database->query("select * from webuser");

    // Récupérer les informations personnelles de la session
    $fname=$_SESSION['personal']['fname'];
    $lname=$_SESSION['personal']['lname'];
    $name=$fname." ".$lname;
    $address=$_SESSION['personal']['address'];
    $nic=$_SESSION['personal']['nic'];
    $dob=$_SESSION['personal']['dob'];
    $email=$_POST['newemail'];
    $tele=$_POST['tele'];
    $newpassword=$_POST['newpassword'];
    $cpassword=$_POST['cpassword'];
    
    // Vérifier si le mot de passe et la confirmation correspondent
    if ($newpassword==$cpassword){
        // Vérifier si un compte existe déjà avec cet e-mail
        $sqlmain= "select * from webuser where email=?;";
        $stmt = $database->prepare($sqlmain);
        $stmt->bind_param("s",$email);
        $stmt->execute();
        $result = $stmt->get_result();
        if($result->num_rows==1){
            $error='<label for="promter" class="form-label" style="color:rgb(255, 62, 62);text-align:center;">Un compte existe déjà pour cette adresse e-mail.</label>';
        }else{
            // Insérer le nouvel utilisateur dans la base de données
            $database->query("insert into patient(pemail,pname,ppassword, paddress, pnic,pdob,ptel) values('$email','$name','$newpassword','$address','$nic','$dob','$tele');");
            $database->query("insert into webuser values('$email','p')");

            // Enregistrer les informations de session pour l'utilisateur connecté
            $_SESSION["user"]=$email;
            $_SESSION["usertype"]="p";
            $_SESSION["username"]=$fname;

            // Rediriger vers le tableau de bord du patient
            header('Location: patient/index.php');
            $error='<label for="promter" class="form-label" style="color:rgb(255, 62, 62);text-align:center;"></label>';
        }
        
    }else{
        // Message d'erreur si les mots de passe ne correspondent pas
        $error='<label for="promter" class="form-label" style="color:rgb(255, 62, 62);text-align:center;">Erreur de confirmation du mot de passe ! Veuillez confirmer à nouveau.</label>';
    }

}else{
    // Formulaire non soumis
    $error='<label for="promter" class="form-label"></label>';
}

?>

<center>
<div class="container">
    <table border="0" style="width: 69%;">
        <tr>
            <td colspan="2">
                <p class="header-text">Commençons</p>
                <p class="sub-text">C’est parti, créez maintenant votre compte utilisateur.</p>
            </td>
        </tr>
        <tr>
            <form action="" method="POST" >
            <td class="label-td" colspan="2">
                <label for="newemail" class="form-label">E-mail : </label>
            </td>
        </tr>
        <tr>
            <td class="label-td" colspan="2">
                <input type="email" name="newemail" class="input-text" placeholder="Adresse e-mail" required>
            </td>
        </tr>
        <tr>
            <td class="label-td" colspan="2">
                <label for="tele" class="form-label">Numéro de téléphone : </label>
            </td>
        </tr>
        <tr>
            <td class="label-td" colspan="2">
                <input type="tel" name="tele" class="input-text"  placeholder="ex: 0712345678" pattern="[0]{1}[0-9]{9}" >
            </td>
        </tr>
        <tr>
            <td class="label-td" colspan="2">
                <label for="newpassword" class="form-label">Créer un nouveau mot de passe : </label>
            </td>
        </tr>
        <tr>
            <td class="label-td" colspan="2">
                <input type="password" name="newpassword" class="input-text" placeholder="Nouveau mot de passe" required>
            </td>
        </tr>
        <tr>
            <td class="label-td" colspan="2">
                <label for="cpassword" class="form-label">Confirmer le mot de passe : </label>
            </td>
        </tr>
        <tr>
            <td class="label-td" colspan="2">
                <input type="password" name="cpassword" class="input-text" placeholder="Confirmer le mot de passe" required>
            </td>
        </tr>
        <tr>
            <td colspan="2">
                <?php echo $error ?>
            </td>
        </tr>
        <tr>
            <td>
                <input type="reset" value="Réinitialiser" class="login-btn btn-primary-soft btn" >
            </td>
            <td>
                <input type="submit" value="S’inscrire" class="login-btn btn-primary btn">
            </td>
        </tr>
        <tr>
            <td colspan="2">
                <br>
                <label for="" class="sub-text" style="font-weight: 280;">Vous avez déjà un compte&#63; </label>
                <a href="login.php" class="hover-link1 non-style-link">Se connecter</a>
                <br><br><br>
            </td>
        </tr>
                </form>
        </tr>
    </table>
</div>
</center>
</body>
</html>
