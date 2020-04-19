<?php
require_once("inc/Database.php");
require_once("inc/User.php");
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
if (!isset($_POST['login']) || !isset($_POST['pw'])){
?>
<h2>Créer une compte</h2>
    <form method='post'>
    
    <br>
    <label for="login">Login:</label><br>
    <input type='text' id='login' name='login'>
    <br>
    
    <br>
    <label for="pw">Mot de passe:</label><br>
    <input type='password' id='pw' name='pw'>
    <br>

    <br>
    <label for="firstname">Prénom:</label><br>
    <input type='text' id='firstname' name='firstname'>
    <br>

    <br>
    <label for="lastname">Nom:</label><br>
    <input type='text' id='lastname' name='lastname'>
    <br>
    
    <br>
    <label for="email">Adresse e-mail:</label><br>
    <input type='text' id='email' name='email'>
    <br>
    
    <input type='submit'>
    </form>
<?php
}
else{
$u=new User($_POST['login'],$_POST['pw'],$_POST['email'],$_POST['firstname'],$_POST['lastname']);
echo $u;

}
?>


</body>
</html>

