<?php
require_once("tpl/userbar.php");
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
$guestbar="<input type='hidden' name='guest' value='no_value'>";
if ($user->getStatus()!=1){
$guestbar="    <p>Vous n'êtes pas identifié! Connectez vous ou indiquez un nom d'invité ici:</p>
    <input type='text' name='guest' placeholder='Nom'>
    <hr>";
}
?>
<html>
    <head>
        <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.4.1/css/bootstrap.min.css" integrity="sha384-Vkoo8x4CGsO3+Hhxv8T/Q5PaXtkKtu6ug5TOeNV6gBiFeWPGFN9MuhOf23Q9Ifjh" crossorigin="anonymous">
        <link rel="stylesheet" media="screen" type="text/css" title="style" href="css/styleForm.css"/>
        <meta charset="UTF-8" />
        <title>FC - <?= $round->getGamename() ?></title>
    </head>

    <body>
    <?= $userbar ?>
    <h1>Feuille de calcul - <?= $round->getGamename() ?></h1>
    <hr>
    <div class="container">
        <form method="post">
        <?= $guestbar ?>
            <div class="form-group row">

                <div class="col-sm-6 text-center">
                    <label for="pt_victoire">Points de victoire</label>
                    <input type="number" name="pt_victoire" id="pt_victoire" min="0" value="<?php if (isset($_POST['pt_victoire'])){echo $_POST['pt_victoire'];} else {echo '0';}  ?>"/>
                </div>

                <div class="col-sm-6 text-center">
                </div>

                <div class="col-sm-6 text-center">  
                    <label for="c_armee">Cartes armées</label>
                    <input type="number" name="c_armee" id="c_armee" min="0" value="<?php if (isset($_POST['c_armee'])){echo $_POST['c_armee'];} else {echo $_POST['c_armee']=0;} ?>"/>
                </div>

                <div class="col-sm-6 text-center"> 
                    <label for="m_armee">X armée</label>
                    <input type="number" name="m_armee" id="m_armee" min="1" value="<?php if (isset($_POST['m_armee'])){echo $_POST['m_armee'];} else {echo $_POST['m_armee']=1;}  ?>"/>
                </div>

                <div class="col-sm-6 text-center">  
                    <label for="c_science">Cartes sciences</label>
                    <input type="number" name="c_science" id="c_science" min="0" value="<?php if (isset($_POST['c_science'])){echo $_POST['c_science'];} else {echo $_POST['c_science']=0;}  ?>"/>
                </div>

                <div class="col-sm-6 text-center"> 
                    <label for="m_science">X science</label>
                    <input type="number" name="m_science" id="m_science" min="1" value="<?php if (isset($_POST['m_science'])){echo $_POST['m_science'];} else {echo $_POST['m_science']=1;}  ?>"/>
                </div>

                <div class="col-sm-6 text-center">
                    <label for="c_economie">Cartes économies</label>
                    <input type="number" name="c_economie" id="c_economie" min="0" value="<?php if (isset($_POST['c_economie'])){echo $_POST['c_economie'];} else {echo $_POST['c_economie']=0;}  ?>"/>
                </div>

                <div class="col-sm-6 text-center"> 
                    <label for="m_economie">X economie</label>
                    <input type="number" name="m_economie" id="m_economie" min="1" value="<?php if (isset($_POST['m_economie'])){echo $_POST['m_economie'];} else {echo $_POST['m_economie']=1;}  ?>"/>
                </div>

                <div class="col-sm-6 text-center">
                    <label for="c_merveille">Cartes merveilles</label>
                    <input type="number" name="c_merveille" id="c_merveille" min="0" value="<?php if (isset($_POST['c_merveille'])){echo $_POST['c_merveille'];} else {echo $_POST['c_merveille']=0;}  ?>"/>
                </div>

                <div class="col-sm-6 text-center"> 
                    <label for="m_merveille">X merveille</label>
                    <input type="number" name="m_merveille" id="m_merveille" min="1" value="<?php if (isset($_POST['m_merveille'])){echo $_POST['m_merveille'];} else {echo $_POST['m_merveille']=1;}  ?>"/>
                </div>

                <div class="col-sm-6 text-center">
                    <label for="j_trader">Jetons traders</label>
                    <input type="number" name="j_trader" id="j_trader" min="0" value="<?php if (isset($_POST['j_trader'])){echo $_POST['j_trader'];} else {echo $_POST['j_trader']=0;}  ?>"/>
                </div>

                <div class="col-sm-6 text-center"> 
                    <label for="m_trader">X trader</label>
                    <input type="number" name="m_trader" id="m_trader" min="1" value="<?php if (isset($_POST['m_trader'])){echo $_POST['m_trader'];} else {echo $_POST['m_trader']=1;}  ?>"/>
                </div>

                <div class="col-sm-6 text-center">
                    <label for="j_militaire">Jetons militaires</label>
                    <input type="number" name="j_militaire" id="j_militaire" min="0" value="<?php if (isset($_POST['j_militaire'])){echo $_POST['j_militaire'];} else {echo $_POST['j_militaire']=0;}  ?>"/>
                </div>

                <div class="col-sm-6 text-center"> 
                    <label for="m_militaire">X militaire</label>
                    <input type="number" name="m_militaire" id="m_militaire" min="1" value="<?php if (isset($_POST['m_militaire'])){echo $_POST['m_militaire'];} else {echo $_POST['m_militaire']=1;}  ?>"/>
                </div>
                <div class="col-sm-6 text-center">
                </div>

                <div class="col-sm-6 text-center">
                    <button type="submit" name="calculer" class="btn btn-primary mt-4">Calculer score</button>
                </div>

            </div>

        </form>
          
    </div>

    <?php
			

            if(isset($_POST['calculer'])){
              
              if ($_POST['guest']!="" && $_POST['pt_victoire']!="" && $_POST['c_armee']!="" && $_POST['c_science']!="" && $_POST['c_economie']!="" && $_POST['c_merveille']!="" && $_POST['j_trader']!="" && $_POST['j_militaire']!="" 
                    && $_POST['m_armee']!="" && $_POST['m_science']!="" && $_POST['m_economie']!="" && $_POST['m_merveille']!="" && $_POST['m_trader']!="" && $_POST['m_militaire']!="") {

                $resultat = intval($_POST['pt_victoire']) + 
                      intval($_POST['c_armee']) * intval($_POST['m_armee']) +
                      intval($_POST['c_science']) * intval($_POST['m_science']) +
                      intval($_POST['c_economie']) * intval($_POST['m_economie']) +
                      intval($_POST['c_merveille']) * intval($_POST['m_merveille']) +
                      intval($_POST['j_trader']) * intval($_POST['m_trader']) +
                      intval($_POST['j_militaire']) * intval($_POST['m_militaire']);
                
                // Enregistrement des 
                $guestname = ($_POST['guest']=="") ? Null : $_POST['guest'];
                $round->setPoint(1,$_POST['pt_victoire'], 1,                     $user->getId(), $guestname);
                $round->setPoint(2,$_POST['c_armee'],     $_POST['m_armee'],     $user->getId(), $guestname);
                $round->setPoint(3,$_POST['c_science'],   $_POST['m_science'],   $user->getId(), $guestname);
                $round->setPoint(4,$_POST['c_economie'],  $_POST['m_economie'],  $user->getId(), $guestname);
                $round->setPoint(5,$_POST['c_merveille'], $_POST['m_merveille'], $user->getId(), $guestname);
                $round->setPoint(6,$_POST['j_trader'],    $_POST['m_trader'],    $user->getId(), $guestname);
                $round->setPoint(7,$_POST['j_militaire'], $_POST['m_militaire'], $user->getId(), $guestname);

                echo '<br><p id="score">Votre score est de '. $resultat. ' !</p><br>';

                echo '<p id="recap" class="indentation" style="text-decoration: underline;">Récapitulatif :</p>';

                echo '<p id="recap" class="indentation">Points de victoire : ' . $_POST['pt_victoire'] .'</p>';

                echo '<p id="recap" class="indentation">Armée : ' . $_POST['c_armee'] . 'x' . $_POST['m_armee'] . '=' . $_POST['c_armee']*$_POST['m_armee'] . '</p>';

                echo '<p id="recap" class="indentation">Science : ' . $_POST['c_science'] . 'x' . $_POST['m_science'] . '=' . $_POST['c_science']*$_POST['m_science'] . '</p>';

                echo '<p id="recap" class="indentation">Économie : ' . $_POST['c_economie'] . 'x' . $_POST['m_economie'] . '=' . $_POST['c_economie']*$_POST['m_economie'] . '</p>';

                echo '<p id="recap" class="indentation">Merveille : ' . $_POST['c_merveille'] . 'x' . $_POST['m_merveille'] . '=' . $_POST['c_merveille']*$_POST['m_merveille'] . '</p>';

                echo '<p id="recap" class="indentation">Trader : ' . $_POST['j_trader'] . 'x' . $_POST['m_trader'] . '=' . $_POST['j_trader']*$_POST['m_trader'] . '</p>';

                echo '<p id="recap" class="indentation">Militaire : ' . $_POST['j_militaire'] . 'x' . $_POST['m_militaire'] . '=' . $_POST['j_militaire']*$_POST['m_militaire'] . '</p>';

                echo '<p id="recap" class="indentation">Total : ' . $_POST['pt_victoire'] . '+'
                                                          . $_POST['c_armee']*$_POST['m_armee'] . '+'
                                                          . $_POST['c_science']*$_POST['m_science'] . '+'
                                                          . $_POST['c_economie']*$_POST['m_economie'] . '+'
                                                          . $_POST['c_merveille']*$_POST['m_merveille'] . '+'
                                                          . $_POST['j_trader']*$_POST['m_trader'] . '+'
                                                          . $_POST['j_militaire']*$_POST['m_militaire'] . '=' . $resultat . '</p>';
														  
				echo '<button style="display: block; margin : auto;" type="submit" name="valider" class="btn btn-success mt-4">Valider score</button>';

              }
              elseif ($_POST['guest']==""){
                ?>
                <div class="alert alert-danger" role="alert">
                  Vous avez oublié de préciser un nom de joueur&nbsp;!
                </div>
                <?php
              }
              else{
                ?>
                <div class="alert alert-danger" role="alert">
                  Vous n'avez pas rempli tous les champs&nbsp;!
                </div>
                <?php
              }
            }
  
        ?>


  </body>
</html>

