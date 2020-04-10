<?php
require_once("inc/Round.php");
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
if (!isset($_POST['game']) || !isset($_POST['pw'])){
?>
<h2>Créer une partie</h2>
    <form method='post'>
    
    <select name="game">
        <option value="1">It's a Wonderful World</option>
        <option value="2">Autre jeu</option>
    </select>
    <br>
    <label for="pw">Mot de passe:</label><br>
    <input type='password' id='pw' name='pw'>
    <br>
    <input type='submit'>
    </form>
<?php
}
else{
$r=new Round($_POST['game'],$_POST['pw']);
echo $r;

}
?>


</body>
</html>

