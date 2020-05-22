<?php
require_once("tpl/header.php");
?><!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="utf-8">
    <link rel="stylesheet" media="screen" type="text/css" title="style" href="css/style.css"/>
    <link rel="icon" href="images/favicon.png" />
    <title>Mes parties</title>
</head>
<body>
<?= $header ?>
<h1>Mes parties</h1>
<section id="mes_parties">
<?php
    $i=0;
    foreach ($user->getRounds() as $round){
?>
    <a class = "parties" href="round.php?r=<?= $round['round'] ?>" id="partie_<?= $round['round'] ?>">
        <p>
        <?= $round['game'] ?><br>
        <?= strtoupper($round['round']) ?> <?= $round['score'] ?>pts
        </p>
    </a>
<?php
        $i++;
    }
    
?>
</section>

</body>
</html>


