<!DOCTYPE html>
<html lang="fr">
<head>
	<meta charset="utf-8">
	<meta name="viewport" content="width=device-width, user-scalable=yes, initial-scale=1.0">
	<title>Minotaure</title>
	<link rel="icon" href="images/favicon.png" />
	<!--<link rel="stylesheet" href="" media="screen"/>-->
</head>
<body>
<h1>Minotaure</h1>

<?php
if (!isset($_POST["r"])){
    echo"
    <a href='create.php'>Créer une partie</a><br>
    ou<br>
    <b>Rejoindre une partie</b>
    <form>
    <input type='text' name='k'>
    <input type='submit'>
    </form>";
}
?>

</body>
</html>
