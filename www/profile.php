<?php
require_once("tpl/userbar.php");
require_once("inc/User.php");
require_once("inc/Database.php");
require_once("tpl/header.php");
$message="";
if (isset($_POST['enregistrer'])) {

    if ($_POST['lastname'] != $_SESSION['lastname']) {
        $user->setLastname($user->getId(), $_POST['lastname']);
    }
    if ($_POST['firstname'] != $_SESSION['firstname']) {
        $user->setFirstname($user->getId(), $_POST['firstname']);
    }
    if ($_POST['email'] != $_SESSION['email']) {
        $user->setEmail($user->getId(), $_POST['email']);
    }
    if ($_POST['new_password'] != "" && $_POST['conf_password'] != "" && $_POST['password'] == "" ||
        $_POST['new_password'] != "" && $_POST['password'] != "" && $_POST['conf_password'] == "" ||
        $_POST['conf_password'] != "" && $_POST['password'] != "" && $_POST['new_password'] == "") {
        $message.='
        <div class="alert alert-danger" role="alert">
            Veuillez remplir tous les champs pour le changement de mot de passe&nbsp;!
        </div>';
    }
    if ($_POST['new_password'] != "" && $_POST['conf_password'] != "" && $_POST['password'] != "") {
        if (!password_verify($_POST['new_password'], $user->getPw() )
            && $_POST['new_password'] == $_POST['conf_password']
            && password_verify($_POST['password'], $user->getPw() )) {
            $user->setPw($user->getId(), $_POST['new_password']);
            $message.='
            <div class="alert alert-success" role="alert">
                Mot de passe modifié avec succès&nbsp;!
            </div>';
        } elseif (!password_verify($_POST['password'], $user->getPw() )) {
            $message.='
            <div class="alert alert-danger" role="alert">
                Le mot de passe actuel n\'est pas le bon&nbsp;!
            </div>';
        } elseif ($_POST['new_password'] != $_POST['conf_password']) {
            $message.='
            <div class="alert alert-danger" role="alert">
                Le mot de passe de confirmation ne correspond pas au nouveau mot de passe&nbsp;!
            </div>';
        } elseif (password_verify($_POST['new_password'], $user->getPw() )) {
            $message.='
            <div class="alert alert-danger" role="alert">
                Le nouveau mot de passe que vous avez saisi est le même que votre mot de passe actuel, choisissez-en un autre&nbsp;!
            </div>';
        }
    }
}
?><!DOCTYPE html>
<html lang="fr">
<head>
    <link rel="stylesheet" media="screen" type="text/css" title="style" href="css/styleForm.css"/>
    <link rel="stylesheet" href="css/style.css" type="text/css" media="screen"/>
    <meta charset="UTF-8"/>
    <title>Profile - <?php echo $user->getLogin() ?></title>
    <style>
        input {
            margin-bottom: 5px;
        }
    </style>
</head>
<body>
<?= $header ?>
<div class="container">
    <?= $message ?>
    <form id = 'form_profile' method="post">

        <div class="text-center">
            <label id = 'first_label' for="login">Login</label>
            <input type="text" name="login" id="login" disabled="disabled" value="<?php echo $user->getLogin() ?>"/>
        </div>

        <div class="text-center">
            <label for="lastname">Nom</label>
            <input type="text" name="lastname" id="lastname" value="<?php echo $user->getLastname() ?>"/>
        </div>

        <div class="text-center">
            <label for="firstname">Prénom</label>
            <input type="text" name="firstname" id="firstname" value="<?php echo $user->getFirstname() ?>"/>
        </div>

        <div class="text-center">
            <label for="email">Email</label>
            <input type="email" name="email" id="email" value="<?php echo $user->getEmail() ?>"/>
        </div>

        <div class="text-center">
            <label for="new_password">Nouveau mot de passe</label>
            <input type="password" name="new_password" id="new_password" value=""/>
        </div>

        <div class="text-center">
            <label for="conf_password">Confirmer mot de passe</label>
            <input type="password" name="conf_password" id="conf_password" value=""/>
        </div>

        <div class="text-center">
            <label for="password">Mot de passe actuel</label>
            <input type="password" name="password" id="password" value=""/>
        </div>

        <div class="text-center">
            <button type="submit" name="enregistrer" class="btn btn-primary mt-4">Enregistrer</button>
        </div>

    </form>

</div>


</body>
</html>
