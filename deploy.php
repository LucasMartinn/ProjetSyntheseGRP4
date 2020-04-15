<?php
//Préparer la base de données, les tables, peupler les tables
define('DEPLOYDIR' , pathinfo(__FILE__,PATHINFO_DIRNAME));
require_once(DEPLOYDIR.'/www/inc/Database.php');
$db = new Database;
$db->createTables();
$db->populateTables();
if !file_exists('www/inc/config.php'){
    echo "\e[1mVeuillez créer le fichier www/inc/config.php à partir de www/inc/config_skel.php\e[0m\n";
}
