<?php
//Préparer la base de données, les tables, peupler les tables

if (!file_exists('www/inc/config.php')){
    die("\e[1mVeuillez créer le fichier www/inc/config.php à partir de www/inc/config_skel.php\e[0m\n");
}

// On inclut les fichiers comme si on travaillait dans le répertoire www
set_include_path("www/");
require_once('inc/Database.php');
$db = new Database;
$db->createTables();
$db->populateTables();
