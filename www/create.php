<?php
require_once("tpl/userbar.php");
require_once("inc/Round.php");
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
<?= $userbar ?>
<?php
if (!isset($_POST['game']) || !isset($_POST['pw'])){
?>
<h2>Créer une partie</h2>
    <form id = 'form_create' method='post'>

    <select name="game">
        <option value="1">It's a Wonderful World</option>
        <option value="2">Autre jeu</option>
    </select>
    <br>
    <label for="pw">Mot de passe:</label>
    <input type='password' id='pw' name='pw'>
    <br>
    <input type='submit'>
    </form>
<?php
}
else{
$r=new Round($_POST['game'],$_POST['pw']);
?>
<h2>Créer une partie</h2>
<div id = "texte_creer">
  <p>Une nouvelle partie a été créée avec le code <strong><?= strtoupper($r) ?></strong><p>
  <p>Vous pouvez maintenant <a href='form.php?r=<?= $r ?>'>renseigner vos scores</a> ou partager ce code avec d'autres joueurs!</p>
  <p>Le mot de passe de la partie sera demandé aux joueurs pour remplir leurs scores.</p>
</div>
<?php
}
?>


</body>
</html>
