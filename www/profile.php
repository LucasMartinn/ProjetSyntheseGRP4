<?php
require_once("tpl/userbar.php");
require_once("inc/User.php");
require_once("inc/Database.php");

if(isset($_POST['enregistrer'])){

    if ($_POST['lastname'] != $_SESSION['lastname']) {
        $user->setLastname($user->getId(), $_POST['lastname']);
    }
    if ($_POST['firstname'] != $_SESSION['firstname']) {
        $user->setFirstname($user->getId(), $_POST['firstname']);
    }
    if ($_POST['email'] != $_SESSION['email']) {
        $user->setEmail($user->getId(), $_POST['email']);
    }
    if ($_POST['new_password']!="" && $_POST['conf_password']!="" && $_POST['password']=="" ||
        $_POST['new_password']!="" && $_POST['password']!="" && $_POST['conf_password']=="" ||
        $_POST['conf_password']!="" && $_POST['password']!="" && $_POST['new_password']=="") {
        ?>
        <div class="alert alert-danger" role="alert">
            Veuillez remplir tous les champs pour le changement de mot de passe !
        </div>
        <?php
    }
    if ($_POST['new_password']!="" && $_POST['conf_password']!="" && $_POST['password']!="") {
        if (!password_verify($_POST['new_password'], $_SESSION['pw'])
            && $_POST['new_password'] == $_POST['conf_password']
            && password_verify($_POST['password'], $_SESSION['pw'])) {
            $user->setPw($user->getId(), $_POST['new_password']);
            ?>
            <div class="alert alert-success" role="alert">
                Mot de passe modifié avec succès !
            </div>
            <?php
        }
        elseif (!password_verify($_POST['password'], $_SESSION['pw'])) {
            ?>
            <div class="alert alert-danger" role="alert">
                Le mot de passe actuel n'est pas le bon&nbsp;!
            </div>
            <?php
        }
        elseif ($_POST['new_password'] != $_POST['conf_password']) {
            ?>
            <div class="alert alert-danger" role="alert">
                Le mot de passe de confirmation ne correspond pas au nouveau mot de passe&nbsp;!
            </div>
            <?php
        }
        elseif (password_verify($_POST['new_password'], $_SESSION['pw'])) {
            ?>
            <div class="alert alert-danger" role="alert">
                Le nouveau mot de passe que vous avez saisie est le même que votre mot de passe actuel !
            </div>
            <?php
        }
    }

}

?>

<html>
<head>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.4.1/css/bootstrap.min.css" integrity="sha384-Vkoo8x4CGsO3+Hhxv8T/Q5PaXtkKtu6ug5TOeNV6gBiFeWPGFN9MuhOf23Q9Ifjh" crossorigin="anonymous">
    <link rel="stylesheet" media="screen" type="text/css" title="style" href="css/styleForm.css"/>
    <meta charset="UTF-8" />
    <title>Profile - <?php echo $_SESSION['login'] ?></title>
    <style>

        div label{
            display:block;
            width:2px;
            float:left;
            white-space: nowrap;
        }

    </style>
</head>
<body>
<?= $userbar ?>
<hr>
<div class="container">
    <form method="post">
        <div class="form-group row">

            <div class="col-sm-6 text-center">
                <label for="login">Login</label>
                <input type="text" name="login" id="login" disabled="disabled" value="<?php echo $_SESSION['login'] ?>"/>
            </div>

            <div class="col-sm-6 text-center">
                <label for="new_password">Nouveau mot de passe</label>
                <input type="password" name="new_password" id="new_password"  value=""/>
            </div>

            <div class="col-sm-6 text-center">
                <label for="lastname">Nom</label>
                <input type="text" name="lastname" id="lastname"  value="<?php echo $_SESSION['lastname'] ?>"/>
            </div>

            <div class="col-sm-6 text-center">
                <label for="conf_password">Confirmer mot de passe</label>
                <input type="password" name="conf_password" id="conf_password"  value=""/>
            </div>

            <div class="col-sm-6 text-center">
                <label for="firstname">Prénom</label>
                <input type="text" name="firstname" id="firstname"  value="<?php echo $_SESSION['firstname'] ?>"/>
            </div>

            <div class="col-sm-6 text-center">
                <label for="password">Mot de passe actuel</label>
                <input type="password" name="password" id="password"  value=""/>
            </div>

            <div class="col-sm-6 text-center">
                <label for="email">Email</label>
                <input type="email" name="email" id="email"  value="<?php echo $_SESSION['email'] ?>"/>
            </div>

            <div class="col-sm-6 text-center">
                <button type="submit" name="enregistrer" class="btn btn-primary mt-4">Enregistrer</button>
            </div>

        </div>

    </form>

</div>


</body>
</html>


