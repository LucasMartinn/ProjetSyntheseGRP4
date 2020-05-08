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

<?php
if (!isset($_POST["r"])){
    echo"
    <a href='create.php'>Créer une partie</a><br>
    ou<br>
    <b>Rejoindre une partie</b>
    <form method='get' action='round.php'>
    <input type='text' name='r'>
    <input type='submit'>
    </form>";
}
?>

</body>
</html>

