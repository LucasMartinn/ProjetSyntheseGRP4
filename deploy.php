<?php
//Préparer la base de données, les tables, peupler les tables
define('DEPLOYDIR' , pathinfo(__FILE__,PATHINFO_DIRNAME));
require_once(DEPLOYDIR.'/www/inc/Database.php');
$db = new Database;
$db->createTables();
$db->populateTables();
