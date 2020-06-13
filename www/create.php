<?php
require_once("tpl/header.php");
require_once("inc/Round.php");

if(isset($_POST['game']) && isset($_POST['pw']) && $_POST['pw']!=""){
    $r=new Round($_POST['game'],$_POST['pw']);
    if ($r->getCode()){
        header("Location: http://" . $_SERVER['SERVER_NAME'] . pathinfo($_SERVER["PHP_SELF"],PATHINFO_DIRNAME) . "/round.php?r=" . $r->getCode());
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
    <link rel="stylesheet" href="css/style.css" media="screen"/>
</head>
<body>
<?= $header ?>

<h2>Créer une partie</h2>
    <form id = 'form_create' method='post'>
    <select name="game">
        <option value="1">It's a Wonderful World</option>
        <option value="2">Autre jeu</option>
    </select>
    <br>
    <label for="pw">Mot de passe de la partie:</label>
    <input type='password' id='pw' name='pw'>
    <br>
    Partagez ce mot de passe avec les joueurs pour qu'ils entrent leurs points!<br><br>
    <input type='submit'>
    </form>
</body>
</html>
