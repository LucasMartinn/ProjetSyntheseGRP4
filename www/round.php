<?php
require_once("tpl/userbar.php");
require_once("inc/Round.php");
?><!doctype html>
<html lang="fr">
    <head>
        <meta charset="UTF-8">  
        <title>Récapitulatif partie</title>
        <link rel="stylesheet" href="css/round.css" type="text/css" media="screen"/>
        <link rel="icon" type="image/png" href="images/favicon.png"/>
    </head>
    <body>
    <header>
        <?= $userbar ?>
    </header>

<?php
if (@$_GET['r']=="" || !isset($_GET['r'])){
?>
            <div id = 'jeu'>
            <h2>Entrez le code de votre partie</h2>
        </div>
        <form method='get' action='round.php'>
            <input type='text' name='r'>
            <input type='submit'>
        </form>
<?php
}
else{
    $r=new Round("","",$_GET['r']);?>

        <div id = "jeu">
            <h2><?= $r->getGamename() ?></h2>
            <h3>Partie <?= strtoupper($r->getCode()) ?></h3>
        </div>
        
        <div id = "resultats">

<?php
    $i=1;
    foreach ($r->getPlayers() as $player){
        if ($player['login']!=Null){
            $class="registered";
        }
        else {
            $class="guest";
        }
        ?>
            <a class = "joueurs <?= $class ?>" onclick="details(this)" id="joueur<?= $i ?>">
                #<?= $i ?> <?= $player['guestname'].$player['login'] ?> <?= $player['score'] ?>
            </a>
        <?php
        $i++;
    }
?>
            <a href="form.php?r=<?= $r->getCode() ?>" class = "joueurs">
                + AJOUTER SES POINTS
            </a>
        </div>
        <?php
    }
    ?>
        
    </body>
    
</html>
