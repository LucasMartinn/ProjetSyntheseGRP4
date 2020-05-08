<?php
/**
 * À inclure en haut des pages pour gérer les utilisateurs.
 * Pour afficher le bandeau de connexion, afficher la variable $userbar
 * à l'endroit souhaité.
 */
session_start();
require_once("inc/Database.php");
require_once("inc/User.php");

if (isset($_POST['login']) && isset($_POST['pw'])){
    $user = new User($_POST['login'],$_POST['pw']);
}
else{
    $user = new User;
}
if (isset($_GET["logout"])){
    $user->logout();
    // On redirige l'utilisateur vers la même page en retirant la variable logout de l'URL
    if (empty($_SERVER['HTTPS']) or $_SERVER['HTTPS']=="off"){
        //header("Location : http://localhost/php/minotaure/index.php");
        header("Location: http://".$_SERVER['SERVER_NAME'].$_SERVER["PHP_SELF"]);
    }
    else{
        header("Location: https://".$_SERVER['SERVER_NAME'].$_SERVER["PHP_SELF"]);
    }
    die();
}

// à afficher si l'utilisateur est connecté
if ($user->getLogin()!=Null){
    $userbar="    <a class='login' href='profile.php'>".$user->getLogin()."</a> <a href='".$_SERVER["PHP_SELF"]."?logout=1'>Déconnexion</a>";
}

// à afficher si l'utilisateur est déconnecté
else{
    $userbar="    <form method='post'>
    <input type='text'     name='login' placeholder='Identifiant'>
    <input type='password' name='pw'    placeholder='Mot de passe'>
    <input type='submit'   value='OK'>
    </form>
    <p>Pas encore de compte? <a href='register.php'>S'inscrire</a></p>";
}

$userbar = "    <div id='userbar'>
$userbar
    </div>";
