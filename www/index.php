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
    <a style = 'text-decoration:none' class = 'partie' href='create.php'>Créer une partie</a>
    <p class = 'partie'>ou</p>
    <p class = 'partie'>Rejoindre une partie</p>
    <form id = 'num_partie' method='get' action='round.php'>
    <input type='text' name='r' placeholder='Numéro partie'></br>
    <input type='submit'>
    </form>";
}
?>

</body>
</html>
