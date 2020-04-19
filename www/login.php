<?php
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

echo $u;
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

<?php
if ($u->getStatus()!=1){
    // Si non connecté
?>
<h2>Se connecter</h2>
    <form method='post'>
    
    <br>
    <label for="login">Login:</label><br>
    <input type='text' id='login' name='login'>
    <br>
    
    <br>
    <label for="pw">Mot de passe:</label><br>
    <input type='password' id='pw' name='pw'>
    <br>
    
    <input type='submit'>
    </form>
<?php
}
else{
    //Si connecté
?>

<h2>Bonjour <?= $u->getLogin(); ?></h2>

<a href="login.php?logout=1">Me déconnecter</a>


<?php
}
?>

</body>
</html>

