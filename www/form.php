<?php
require_once("tpl/header.php");
require_once("inc/Round.php");

if (isset($_GET['r'])){
    $round = new Round("","",$_GET['r']);
}
else{
    $round = new Round();
}
if ($round->getStatus() != 1) {
    // Si on ne parvient pas à charger la partie, redirection vers la page principale
    header("Location: http://".$_SERVER['SERVER_NAME'].pathinfo ( $_SERVER["PHP_SELF"] ,  PATHINFO_DIRNAME ));
}
$guestbar="<input type='hidden' name='guest' value=''>";
if ($user->getStatus()!=1){
    $guest =  isset($_POST['guest']) ? $_POST['guest'] : "";
    $guestbar="    <p>Vous n'êtes pas identifié! Connectez vous ou indiquez un nom d'invité ici:</p>
    <input type='text' name='guest' placeholder='Nom' value='".$guest."'>
    <hr>";
}

$points_message="";
if (isset($_POST['record'])){
    // Si tous les champs sont remplis
    if (($_POST['guest']!="" || $user->getStatus()==1)
        &&  $_POST['round_pw']!=""
        &&  $_POST['pt_victoire']!=""
        &&  $_POST['c_armee']!=""
        &&  $_POST['c_science']!=""
        &&  $_POST['c_economie']!=""
        &&  $_POST['c_merveille']!=""
        &&  $_POST['j_trader']!=""
        &&  $_POST['j_militaire']!=""
        &&  $_POST['m_armee']!=""
        &&  $_POST['m_science']!=""
        &&  $_POST['m_economie']!=""
        &&  $_POST['m_merveille']!=""
        &&  $_POST['m_trader']!=""
        &&  $_POST['m_militaire']!="") {
        // Enregistrement des points
        $guestname = ($_POST['guest']=="") ? Null : $_POST['guest'];
        $ret=array();
        $ret[0]=$round->setPoint(1,$_POST['pt_victoire'], 1,                     $user->getId(), $guestname, $_POST['round_pw']);
        $ret[1]=$round->setPoint(2,$_POST['c_armee'],     $_POST['m_armee'],     $user->getId(), $guestname, $_POST['round_pw']);
        $ret[2]=$round->setPoint(3,$_POST['c_science'],   $_POST['m_science'],   $user->getId(), $guestname, $_POST['round_pw']);
        $ret[3]=$round->setPoint(4,$_POST['c_economie'],  $_POST['m_economie'],  $user->getId(), $guestname, $_POST['round_pw']);
        $ret[4]=$round->setPoint(5,$_POST['c_merveille'], $_POST['m_merveille'], $user->getId(), $guestname, $_POST['round_pw']);
        $ret[5]=$round->setPoint(6,$_POST['j_trader'],    $_POST['m_trader'],    $user->getId(), $guestname, $_POST['round_pw']);
        $ret[6]=$round->setPoint(7,$_POST['j_militaire'], $_POST['m_militaire'], $user->getId(), $guestname, $_POST['round_pw']);

        $erreur=0;
        foreach ($ret as $value){
            if ($value==0){
                $points_message= "<div class='alert alert-danger' role='alert'>L'enregistrement a rencontré une erreur: paramètres invalides.</div>";
                $erreur=1;
                break;
            }
            if ($value==2){
                $points_message= "<div class='alert alert-danger' role='alert'>L'enregistrement a rencontré une erreur: le mot de passe est incorrect.</div>";
                $erreur=1;
                break;
            }
            if ($value==3){
                $points_message= "<div class='alert alert-danger' role='alert'>L'enregistrement a rencontré une erreur: les données existent déjà.</div>";
                $erreur=1;
                break;
            }
        }
        if($erreur==0){
            // L'enregistrement s'est bien passé, on retourne à la page de la partie
            header("Location: http://".$_SERVER['SERVER_NAME'].pathinfo ( $_SERVER["PHP_SELF"] ,  PATHINFO_DIRNAME )."/round.php?r=".$round->getCode());
        }
    }
}
?><!DOCTYPE html>
<html lang="fr">
<head>
    <link rel="stylesheet" media="screen" type="text/css" title="style" href="css/styleForm.css"/>
    <link rel="stylesheet" media="screen" type="text/css" title="style" href="css/style.css"/>
    <meta charset="UTF-8" />
    <title>FC - <?= $round->getGamename() ?></title>
</head>
<body>
<?= $header ?>
<h1 id = 'feuille_calcul'>Feuille de calcul - <?= $round->getGamename() ?><br><a href="round.php?r=<?= $round->getCode() ?>">Partie <?= strtoupper($round->getCode()) ?></a></h1>
<?php 
echo $points_message;
// On vérifie que le joueur n'a pas déjà enregistré son score

if(!$round->getPlayer($user->getId())){
?>
<div class="container">
    <form id = 'feuille_remplir' method="post">
        <?= $guestbar ?>

        <br>
        <label for="pt_victoire">Points de victoire</label>
        <input type="number" name="pt_victoire" id="pt_victoire" min="0" value="<?php if (isset($_POST['pt_victoire'])){echo $_POST['pt_victoire'];} else {echo '0';}  ?>"/>
        <br>

        <label for="c_armee">Cartes armées</label>
        <input type="number" name="c_armee" id="c_armee" min="0" value="<?php if (isset($_POST['c_armee'])){echo $_POST['c_armee'];} else {echo '0';} ?>"/>

        <label for="m_armee">X armée</label>
        <input type="number" name="m_armee" id="m_armee" min="0" value="<?php if (isset($_POST['m_armee'])){echo $_POST['m_armee'];} else {echo '0';}  ?>"/>

        <label for="c_science">Cartes sciences</label>
        <input type="number" name="c_science" id="c_science" min="0" value="<?php if (isset($_POST['c_science'])){echo $_POST['c_science'];} else {echo '0';}  ?>"/>

        <label for="m_science">X science</label>
        <input type="number" name="m_science" id="m_science" min="0" value="<?php if (isset($_POST['m_science'])){echo $_POST['m_science'];} else {echo '0';}  ?>"/>

        <label for="c_economie">Cartes économies</label>
        <input type="number" name="c_economie" id="c_economie" min="0" value="<?php if (isset($_POST['c_economie'])){echo $_POST['c_economie'];} else {echo '0';}  ?>"/>

        <label for="m_economie">X economie</label>
        <input type="number" name="m_economie" id="m_economie" min="0" value="<?php if (isset($_POST['m_economie'])){echo $_POST['m_economie'];} else {echo '0';}  ?>"/>

        <label for="c_merveille">Cartes merveilles</label>
        <input type="number" name="c_merveille" id="c_merveille" min="0" value="<?php if (isset($_POST['c_merveille'])){echo $_POST['c_merveille'];} else {echo '0';}  ?>"/>

        <label for="m_merveille">X merveille</label>
        <input type="number" name="m_merveille" id="m_merveille" min="0" value="<?php if (isset($_POST['m_merveille'])){echo $_POST['m_merveille'];} else {echo '0';}  ?>"/>

        <label for="j_trader">Jetons traders</label>
        <input type="number" name="j_trader" id="j_trader" min="0" value="<?php if (isset($_POST['j_trader'])){echo $_POST['j_trader'];} else {echo '0';}  ?>"/>

        <label for="m_trader">X trader</label>
        <input type="number" name="m_trader" id="m_trader" min="0" value="<?php if (isset($_POST['m_trader'])){echo $_POST['m_trader'];} else {echo '0';}  ?>"/>

        <label for="j_militaire">Jetons militaires</label>
        <input type="number" name="j_militaire" id="j_militaire" min="0" value="<?php if (isset($_POST['j_militaire'])){echo $_POST['j_militaire'];} else {echo '0';}  ?>"/>

        <label for="m_militaire">X militaire</label>
        <input type="number" name="m_militaire" id="m_militaire" min="0" value="<?php if (isset($_POST['m_militaire'])){echo $_POST['m_militaire'];} else {echo '0';}  ?>"/>

        <button type="button" name="calculer" onclick="calculerScore()" class="btn btn-primary mt-4">Calculer score</button>

            <div>
                <span id="erreur"></span>
            </div>

            <div id="score"></div>

            <div id="recap"></div>

            <div id="enregistrer"></div>

        </div>
    </form>
</div>
<?php
}
//Si le joueur a déjà entré ses scores
else{
?>
<div class="container">
    <p>Vous avez déjà entré vos scores!</p>
</div>
<?php
}
?>

<script type="text/javascript">

    function calculerScore(){

        let pt_victoire = document.getElementById('pt_victoire').value;

        let c_armee = document.getElementById('c_armee').value;
        let m_armee = document.getElementById('m_armee').value;

        let c_science = document.getElementById('c_science').value;
        let m_science = document.getElementById('m_science').value;

        let c_economie = document.getElementById('c_economie').value;
        let m_economie = document.getElementById('m_economie').value;

        let c_merveille = document.getElementById('c_merveille').value;
        let m_merveille = document.getElementById('m_merveille').value;

        let j_trader = document.getElementById('j_trader').value;
        let m_trader = document.getElementById('m_trader').value;

        let j_militaire = document.getElementById('j_militaire').value;
        let m_militaire = document.getElementById('m_militaire').value;

        if (pt_victoire !== "" && c_armee !== "" && m_armee !== "" && c_science !== "" && m_science !== "" && c_economie !== "" && m_economie !== "" && c_merveille !== "" &&
            m_merveille !== "" && j_trader !== "" && m_trader !== "" && j_militaire !== "" && m_militaire !== "") {

           var resultat = parseInt(pt_victoire) +
                          parseInt(c_armee)*parseInt(m_armee) +
                          parseInt(c_science)*parseInt(m_science) +
                          parseInt(c_economie)*parseInt(m_economie) +
                          parseInt(c_merveille)*parseInt(m_merveille) +
                          parseInt(j_trader)*parseInt(m_trader) +
                          parseInt(j_militaire)*parseInt(m_militaire);

           document.getElementById('score').innerHTML = '<p>Score = ' + resultat + '<\p>';
           document.getElementById('recap').innerHTML = /*'<p>Points de victoire = ' + pt_victoire + '<\p>' +
                                                        '<p>Armée = ' + c_armee*m_armee + '<\p>' +
                                                        '<p>Science = ' + c_science*m_science + '<\p>' +
                                                        '<p>Économie = ' + c_economie*m_economie + '<\p>' +
                                                        '<p>Merveille = ' + c_merveille*m_merveille + '<\p>' +
                                                        '<p>Trader = ' + j_trader*m_trader + '<\p>' +
                                                        '<p>Militaire = ' + j_militaire*m_militaire + '<\p>' +*/
                                                        '<p>Récapitulatif : ' + pt_victoire + '+'
                                                                      + c_armee*m_armee + '+'
                                                                      + c_science*m_science + '+'
                                                                      + c_economie*m_economie + '+'
                                                                      + c_merveille*m_merveille + '+'
                                                                      + j_trader*m_trader + '+'
                                                                      + j_militaire*m_militaire + '='
                                                                      + resultat + '<\p>';

           document.getElementById('enregistrer').innerHTML = '<input type="password" name="round_pw" placeholder="Mot de passe de la partie">' +
                                                              '<button style="display: block; margin : auto;" type="submit" name="record" class="btn btn-success mt-4">Enregistrer score</button>'

        }

    }
</script>
</body>
</html>
