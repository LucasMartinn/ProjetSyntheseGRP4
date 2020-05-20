<?php
require_once("tpl/header.php");
?><!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, user-scalable=yes, initial-scale=1.0">
    <title>Minotaure</title>
    <link rel="icon" href="images/favicon.png" />
    <link rel="stylesheet" href="css/style.css" media="screen"/>
</head>
<body>
<?= $header ?>

<h1>Mot de passe oublié</h1>
<?php

//Envoi du 2e mail avec le nouveau mdp
if(isset($_GET['l']) && isset($_GET['c'])) {
    $lostpw_user = new User();
    $lostpw_user->getUserByLogin($_GET['l']);
    if ($lostpw_user->sendNewPw($_GET['c'])){
        echo"
        <p>Un e-mail contenant votre nouveau mot de passe vous a été envoyé.</p>";
    }
    else {
        echo"
        <p>Le code fourni est invalide, peut être l'avez-vous déjà utilisé?</p>";
    }
    
}

//Envoi du premier email avec le code
elseif(isset($_POST["lostpw_login"])) {
    $lostpw_user = new User();
    $lostpw_user->getUserByLogin($_POST["lostpw_login"]);
    $lostpw_user->sendCode();
    echo"
    <p>Un e-mail vous a été envoyé à l'adresse mail associée au compte. Si vous ne l'avez pas reçu, pensez à vérifier dans le dossier des 
    courriers indésirables.</p>";
}


// Demande du login
else {
    echo"
    <p>Veuillez entrer ici le nom de votre compte.
    Un e-mail contenant les indications pour réinitialiser votre mot de passe vous sera envoyé.</p>

    <form method='post' action='lostpw.php'>
    <input type='text' name='lostpw_login' placeholder='Login'>
    <input type='submit' value='Envoyer'>
    </form>";
}
?>

</body>
</html>

