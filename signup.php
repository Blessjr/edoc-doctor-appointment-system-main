<!DOCTYPE html>
<html lang="fr">
<head>
<meta charset="UTF-8">
<meta http-equiv="X-UA-Compatible" content="IE=edge">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<link rel="stylesheet" href="css/animations.css">  
<link rel="stylesheet" href="css/main.css">  
<link rel="stylesheet" href="css/signup.css">
<title>Inscription</title>
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

if($_POST){
    $_SESSION["personal"] = array(
        'fname' => $_POST['fname'],
        'lname' => $_POST['lname'],
        'address' => $_POST['address'],
        'nic' => $_POST['nic'],
        'dob' => $_POST['dob']
    );

    print_r($_SESSION["personal"]);
    header("location: create-account.php");
}
?>

<center>
<div class="container">
    <table border="0">
        <tr>
            <td colspan="2">
                <p class="header-text">Commençons !</p>
                <p class="sub-text">Ajoutez vos informations personnelles pour continuer</p>
            </td>
        </tr>
        <tr>
            <form action="" method="POST">
            <td class="label-td" colspan="2">
                <label for="name" class="form-label">Nom : </label>
            </td>
        </tr>
        <tr>
            <td class="label-td">
                <input type="text" name="fname" class="input-text" placeholder="Prénom" required>
            </td>
            <td class="label-td">
                <input type="text" name="lname" class="input-text" placeholder="Nom de famille" required>
            </td>
        </tr>
        <tr>
            <td class="label-td" colspan="2">
                <label for="address" class="form-label">Adresse : </label>
            </td>
        </tr>
        <tr>
            <td class="label-td" colspan="2">
                <input type="text" name="address" class="input-text" placeholder="Adresse" required>
            </td>
        </tr>
        <tr>
            <td class="label-td" colspan="2">
                <label for="nic" class="form-label">Numéro d'identité : </label>
            </td>
        </tr>
        <tr>
            <td class="label-td" colspan="2">
                <input type="text" name="nic" class="input-text" placeholder="Numéro d'identité" required>
            </td>
        </tr>
        <tr>
            <td class="label-td" colspan="2">
                <label for="dob" class="form-label">Date de naissance : </label>
            </td>
        </tr>
        <tr>
            <td class="label-td" colspan="2">
                <input type="date" name="dob" class="input-text" required>
            </td>
        </tr>
        <tr>
            <td class="label-td" colspan="2">
            </td>
        </tr>

        <tr>
            <td>
                <input type="reset" value="Réinitialiser" class="login-btn btn-primary-soft btn">
            </td>
            <td>
                <input type="submit" value="Suivant" class="login-btn btn-primary btn">
            </td>
        </tr>
        <tr>
            <td colspan="2">
                <br>
                <label for="" class="sub-text" style="font-weight: 280;">Vous avez déjà un compte ? </label>
                <a href="login.php" class="hover-link1 non-style-link">Connexion</a>
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
