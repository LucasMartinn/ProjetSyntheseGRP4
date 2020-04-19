<html>
  <head>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.4.1/css/bootstrap.min.css" integrity="sha384-Vkoo8x4CGsO3+Hhxv8T/Q5PaXtkKtu6ug5TOeNV6gBiFeWPGFN9MuhOf23Q9Ifjh" crossorigin="anonymous">
    <meta charset="UTF-8" />
    <title>FC - It's a Wonderful World</title>
    <style>    

      div label{
        color: lightgray;
        display:block;
        float:top;

      }

      body{
        background-color: #56514E;
      }

      h1{
        text-align: center;
        color: #F07329;
        margin: 20 0 20 0;
      }

      input[type=number] {
        border: 1px solid;
        border-radius: 2px;
        height: 20px;
      }

      .alert{
        width: 35%;
        margin: auto;
        text-align: center;
      }

      #score{
        color: #F07329;
        text-align: center;
        font-weight: bold;
        font-size: 1.5em;
      }

      #recap{
        color: #F07329;
        text-align: center;
        font-style: italic;
        opacity: 0.75;
      }

      hr{
        border-color: lightgray;
      }

      .indentation{
        text-indent: 20px;
      }


    </style>
  </head>
  <body>

    <h1>Feuille de calcul - It's a Wonderful World</h1>
    <hr>

    <div class="container">
      
        <form method="post">

            <div class="form-group row">

              <div class="col-sm-6 text-center">
                <label for="pt_victoire">Points de victoire</label>
                <input type="number" name="pt_victoire" id="pt_victoire" min="0" value="<?php if (isset($_POST['pt_victoire'])){echo $_POST['pt_victoire'];} ?>"/>
              </div>

              <div class="col-sm-6 text-center">
              </div>

              <div class="col-sm-6 text-center">  
                <label for="c_armee">Cartes armées</label>
                <input type="number" name="c_armee" id="c_armee" min="0" value="<?php if (isset($_POST['c_armee'])){echo $_POST['c_armee'];} ?>"/>
              </div>

              <div class="col-sm-6 text-center"> 
                <label for="m_armee">X armée</label>
                <input type="number" name="m_armee" id="m_armee" min="1" value="<?php if (isset($_POST['m_armee'])){echo $_POST['m_armee'];} ?>"/>
              </div>

              <div class="col-sm-6 text-center">  
                <label for="c_science">Cartes sciences</label>
                <input type="number" name="c_science" id="c_science" min="0" value="<?php if (isset($_POST['c_science'])){echo $_POST['c_science'];} ?>"/>
              </div>

              <div class="col-sm-6 text-center"> 
                <label for="m_science">X science</label>
                <input type="number" name="m_science" id="m_science" min="1" value="<?php if (isset($_POST['m_science'])){echo $_POST['m_science'];} ?>"/>
              </div>

              <div class="col-sm-6 text-center">
                <label for="c_economie">Cartes économies</label>
                <input type="number" name="c_economie" id="c_economie" min="0" value="<?php if (isset($_POST['c_economie'])){echo $_POST['c_economie'];} ?>"/>
              </div>

              <div class="col-sm-6 text-center"> 
                <label for="m_economie">X economie</label>
                <input type="number" name="m_economie" id="m_economie" min="1" value="<?php if (isset($_POST['m_economie'])){echo $_POST['m_economie'];} ?>"/>
              </div>

              <div class="col-sm-6 text-center">
                <label for="c_merveille">Cartes merveilles</label>
                <input type="number" name="c_merveille" id="c_merveille" min="0" value="<?php if (isset($_POST['c_merveille'])){echo $_POST['c_merveille'];} ?>"/>
              </div>

              <div class="col-sm-6 text-center"> 
                <label for="m_merveille">X merveille</label>
                <input type="number" name="m_merveille" id="m_merveille" min="1" value="<?php if (isset($_POST['m_merveille'])){echo $_POST['m_merveille'];} ?>"/>
              </div>

              <div class="col-sm-6 text-center">
                <label for="j_trader">Jetons traders</label>
                <input type="number" name="j_trader" id="j_trader" min="0" value="<?php if (isset($_POST['j_trader'])){echo $_POST['j_trader'];} ?>"/>
              </div>

              <div class="col-sm-6 text-center"> 
                <label for="m_trader">X trader</label>
                <input type="number" name="m_trader" id="m_trader" min="1" value="<?php if (isset($_POST['m_trader'])){echo $_POST['m_trader'];} ?>"/>
              </div>

              <div class="col-sm-6 text-center">
                <label for="j_militaire">Jetons militaires</label>
                <input type="number" name="j_militaire" id="j_militaire" min="0" value="<?php if (isset($_POST['j_militaire'])){echo $_POST['j_militaire'];} ?>"/>
              </div>

              <div class="col-sm-6 text-center"> 
                <label for="m_militaire">X militaire</label>
                <input type="number" name="m_militaire" id="m_militaire" min="1" value="<?php if (isset($_POST['m_militaire'])){echo $_POST['m_militaire'];} ?>"/>
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
              
              if ($_POST['pt_victoire']!="" && $_POST['c_armee']!="" && $_POST['c_science']!="" && $_POST['c_economie']!="" && $_POST['c_merveille']!="" && $_POST['j_trader']!="" && $_POST['j_militaire']!="" 
                    && $_POST['m_armee']!="" && $_POST['m_science']!="" && $_POST['m_economie']!="" && $_POST['m_merveille']!="" && $_POST['m_trader']!="" && $_POST['m_militaire']!="") {

                $resultat = intval($_POST['pt_victoire']) + 
                      intval($_POST['c_armee']) * intval($_POST['m_armee']) +
                      intval($_POST['c_science']) * intval($_POST['m_science']) +
                      intval($_POST['c_economie']) * intval($_POST['m_economie']) +
                      intval($_POST['c_merveille']) * intval($_POST['m_merveille']) +
                      intval($_POST['j_trader']) * intval($_POST['m_trader']) +
                      intval($_POST['j_militaire']) * intval($_POST['m_militaire']);

                echo '<br><p id="score">Votre score est de '. $resultat. ' !</p><br>';

                echo '<p id="recap" class="indentation" style="text-decoration: underline;">Récapitulatif :</p>';

                echo '<p id="recap" class="indentation">Points de victoire : ' . $_POST['pt_victoire'] .'</p>';

                echo '<p id="recap" class="indentation">Armée : ' . $_POST['c_armee'] . 'x' . $_POST['m_armee'] . '=' . $_POST['c_armee']*$_POST['m_armee'] .'</p>';

                echo '<p id="recap" class="indentation">Science : ' . $_POST['c_science'] . 'x' . $_POST['m_science'] . '=' . $_POST['c_science']*$_POST['m_science'] .'</p>';

                echo '<p id="recap" class="indentation">Économie : ' . $_POST['c_economie'] . 'x' . $_POST['m_economie'] . '=' . $_POST['c_economie']*$_POST['m_economie'] .'</p>';

                echo '<p id="recap" class="indentation">Merveille : ' . $_POST['c_merveille'] . 'x' . $_POST['m_merveille'] . '=' . $_POST['c_merveille']*$_POST['m_merveille'] .'</p>';

                echo '<p id="recap" class="indentation">Trader : ' . $_POST['j_trader'] . 'x' . $_POST['m_trader'] . '=' . $_POST['j_trader']*$_POST['m_trader'] .'</p>';

                echo '<p id="recap" class="indentation">Militaire : ' . $_POST['j_militaire'] . 'x' . $_POST['m_militaire'] . '=' . $_POST['j_militaire']*$_POST['m_militaire'] .'</p>';

                echo '<p id="recap" class="indentation">Total : ' . $_POST['pt_victoire'] . '+'
                                                          . $_POST['c_armee']*$_POST['m_armee'] . '+'
                                                          . $_POST['c_science']*$_POST['m_science'] . '+'
                                                          . $_POST['c_economie']*$_POST['m_economie'] . '+'
                                                          . $_POST['c_merveille']*$_POST['m_merveille'] . '+'
                                                          . $_POST['j_trader']*$_POST['m_trader'] . '+'
                                                          . $_POST['j_militaire']*$_POST['m_militaire'] . '=' . $resultat .'</p>';

              }
              else{

                ?>

                <div class="alert alert-danger" role="alert">
                  Vous n'avez pas rempli tous les champs !
                </div>

                <?php
              }
            }
  
        ?>


  </body>
</html>

