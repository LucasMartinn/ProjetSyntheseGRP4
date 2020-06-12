<?php
require_once("tpl/header.php");
require_once("inc/Round.php");
?><!doctype html>
<html lang="fr">
    <head>
        <meta charset="UTF-8">
        <title>Récapitulatif partie</title>
        <link rel="stylesheet" href="css/round.css" type="text/css" media="screen"/>
        <link rel="stylesheet" href="css/style.css" type="text/css" media="screen"/>
        <link rel="icon" type="image/png" href="images/favicon.png"/>
    </head>
    <body>
<?= $header ?>


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
    $url="http://".$_SERVER['SERVER_NAME'].pathinfo ( $_SERVER["PHP_SELF"] ,  PATHINFO_DIRNAME )."/round.php?r=".$r->getCode();
?>
            <a href="form.php?r=<?= $r->getCode() ?>" class = "joueurs">
                + AJOUTER SES POINTS
            </a>
            <p>Scannez le qrcode ci-dessous pour rejoindre la partie</p>
        <img src="#" id="outputimg" alt="qrcode pour rejoindre la partie">
        </div>

        <script type="text/javascript" src="lib/qr.js"></script>
<script>

var options = {
    ecclevel: "M",
    margin: "3",
    modulesize: "6"
};
var url = QRCode.generatePNG("<?= $url ?>", options);
document.getElementById('outputimg').src = url;

</script>
        <?php
    }
    ?>

    </body>

</html>
