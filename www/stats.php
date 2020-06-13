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
<h1 id='titre_stats'>Mes parties</h1>
<section id="mes_parties">
<?php
if ($user->getStatus() != 1){
    echo"<p>Veuillez vous connecter pour afficher vos parties</p>";
}

// Parties créées et vides
$i=0;
$liste=$user->getEmptyRounds();

if ($liste !== Null){
    foreach ($liste as $round){
?>
    <a class = "parties" href="round.php?r=<?= $round['round'] ?>" id="partie_<?= $round['round'] ?>">
        <p>
        <?= $round['game'] ?><br>
        <?= strtoupper($round['round']) ?> Ø
        </p>
    </a>
<?php
        $i++;
    }
    if ($i==0){
        echo "<p>Vous n'avez participé à aucune partie</p>";
    }
}

// Parties jouées
$i=0;
$liste=$user->getRounds();
if ($liste !== Null){
    foreach ($liste as $round){
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
    if ($i==0){
        echo "<p>Vous n'avez participé à aucune partie</p>";
    }
}
?>
</section>

</body>
</html>
