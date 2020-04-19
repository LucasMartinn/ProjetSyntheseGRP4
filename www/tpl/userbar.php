<?php
/**
 * À inclure en haut des pages pour afficher le bandeau de connexion
 */
session_start();
require_once("inc/Database.php");
require_once("inc/User.php");

if (isset($_POST['login']) && isset($_POST['pw'])){
    $u = new User($_POST['login'],$_POST['pw']);
}
else{
    $u = new User;
}
if (isset($_GET["logout"])){
    $u->logout();
}


// à afficher si l'utilisateur est connecté
if ($u->getLogin()!=Null){
    $userbar="<a class='login' href='profile.php'>".$u->getLogin()."</a> <span><a href='".$_SERVER["PHP_SELF"]."?logout=1'>Déconnexion</a></span>";
}

// à afficher si l'utilisateur est déconnecté
else{
    $userbar="    <form method='post'>
    <input type='text'     name='login' placeholder='Identifiant'>
    <input type='password' name='pw'    placeholder='Mot de passe'>
    <input type='submit'   value='OK'>
    </form>";
}

$userbar = "<div id='userbar'>
$userbar
</div>";
?>
