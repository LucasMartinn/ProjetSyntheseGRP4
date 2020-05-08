<?php
require_once("tpl/userbar.php");
if ($user->getStatus()==1){
    $img_user="images/usercon.svg";
    $login_frame="    <p href='profile.php'>".$user->getLogin()."</p>
    <a href='profile.php'>Mon profil</a><br>
    <a href='".$_SERVER["PHP_SELF"]."?logout=1'>Déconnexion</a>";
}
else{
    $img_user="images/user.svg";
    $login_frame="    <form method='post' id='header_login'>
    <h1>Se connecter</h1>
    <input type='text'     name='login' placeholder='Identifiant'><br>
    <input type='password' name='pw'    placeholder='Mot de passe'><br>
    <input type='submit'   value='OK'>
    </form>
    <div id='header_register'>
    <h1>Pas encore de compte?</h1>
    <a href='register.php'>S'inscrire</a></p>
    </div>";
}
$header="<header id='headermenu'>
<img src='images/menu.svg' onclick='show(\"mainMenu\")'><h1><a href='index.php'>Minotaure</a></h1><img src='$img_user' onclick='show(\"mainLogin\")'>
</header>
<div id='mainMenu'>
<a href='index.php'>Accueil</a><br>
<a href='#'>À propos</a>
</div>
<div id='mainLogin'>
$login_frame
</div>
<script type='text/javascript'>
function show(id){
    if (document.getElementById(id).style.display === 'block'){
        document.getElementById(id).style.display = 'none';
    }
    else{
        document.getElementById('mainMenu').style.display = 'none';
        document.getElementById('mainLogin').style.display = 'none';
        document.getElementById(id).style.display = 'block';
    }
}
</script>
";
