<?php
require_once("tpl/userbar.php");
$msg="";
if (isset($_POST['reg_login']) && isset($_POST['reg_pw'])){
    $u=new User($_POST['reg_login'],$_POST['reg_pw'],$_POST['reg_email'],$_POST['reg_firstname'],$_POST['reg_lastname']);
        if($u->getLogin()!=Null){
            $_SESSION['message']="Votre inscription a été prise en compte. Nous vous souhaitons la bienvenue!";
            header("Location: http://".$_SERVER['SERVER_NAME'].pathinfo ( $_SERVER["PHP_SELF"] ,  PATHINFO_DIRNAME ));
        }
        elseif($u->getStatus()==4){
            $msg="<p class='information'>Ce login est déjà utilisé, merci d'en choisir un autre.</p>";
        }
        elseif($u->getStatus()==5){
            $msg="<p class='information'>Une erreur a empêché la prise en compte de votre inscription.</p>";
        }
}
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, user-scalable=yes, initial-scale=1.0">
    <title>Minotaure</title>
    <link rel="icon" href="images/favicon.png" />
    <!--<link rel="stylesheet" href="" media="screen"/>-->
</head>
<body>
<h1>Minotaure</h1>
<h2>Créer un compte</h2>
    <?= $msg ?>
    <form method='post'>
    
    <br>
    <label for="reg_login">Login:</label><br>
    <input type='text' id='reg_login' name='reg_login'>
    <br>
    
    <br>
    <label for="reg_pw">Mot de passe:</label><br>
    <input type='password' id='reg_pw' name='reg_pw'>
    <br>

    <br>
    <label for="reg_firstname">Prénom:</label><br>
    <input type='text' id='reg_firstname' name='reg_firstname'>
    <br>

    <br>
    <label for="reg_lastname">Nom:</label><br>
    <input type='text' id='reg_lastname' name='reg_lastname'>
    <br>
    
    <br>
    <label for="reg_email">Adresse e-mail:</label><br>
    <input type='text' id='reg_email' name='reg_email'>
    <br>
    
    <input type='submit'>
    </form>

</body>
</html>

