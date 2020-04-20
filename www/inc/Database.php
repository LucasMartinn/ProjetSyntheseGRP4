<?php
/**
 * Gérer les accès à la base de donnée
 */
require_once("inc/config.php");

class Database{

    private $dbh=Null;

    public function __construct(){
        if ($this->dbh==Null){
            try {
                $this->dbh = new PDO('mysql:host='.DB_HOST.';dbname='.DB_NAME, DB_USER, DB_PASS);
            }
            catch (PDOException $e) {
                die("Impossible de se connecter à la base de données");
                //die("Impossible de se connecter : " . $e->getMessage());
            }
        }
    }
    
    public function newRound(string $code, string $pw, int $game):int{
        try {
            $hash=password_hash($pw, PASSWORD_DEFAULT);
            $this->dbh->beginTransaction();
            $stmt = $this->dbh->prepare("INSERT INTO round (code, pw, game, creationdate) VALUES (:code, :pw, :game, NOW())");
            $stmt->bindParam(':code', $code, PDO::PARAM_STR, 5);
            $stmt->bindParam(':pw', $hash, PDO::PARAM_STR);
            $stmt->bindParam(':game', $game, PDO::PARAM_INT);
            $stmt->execute();
            $this->dbh->commit();
            return 0;
        }
        catch (PDOException $e) {
            $this->dbh->rollBack();
            if ($e->errorInfo[1] == 1062) {
                //le code existe déja dans la base, il faut en trouver un autre
                return 1;
            }
            
            echo "Impossible d'enregistrer une nouvelle partie dans la base de données";
            //die("Impossible de se connecter à la base de données");
            //die("Impossible de se connecter : " . $e->getMessage());
        }
        return 2;
    }

    public function getUserByLogin(string $login):array{
        try {
            $this->dbh->beginTransaction();
            $stmt = $this->dbh->prepare("SELECT * FROM user WHERE login=:login");
            $stmt->bindParam(':login', $login, PDO::PARAM_STR);
            $stmt->execute();
            $result = $stmt->fetch(PDO::FETCH_ASSOC);
            $this->dbh->commit();
        }
        catch (PDOException $e) {
            $this->dbh->rollBack();
            echo "Récupération de l'utilisateur impossible";
        }
        if (gettype($result)=="array"){
            return $result;
        }
        else{
            return array();
            /* Si la requête n'a pas de résultat ce n'est pas un tableau vide qui est retourné
            mais un booléen. On gère ce cas en renvoyant un tableau vide. */
        }
    }

    public function registerUser(string $login,string $pw,string $email,string $firstname=NULL,string $lastname=NULL):int{
        /**
         * Retour 1: l'utilisateur a été créé
         * Retour 2: l'utilisateur existe déja
         * Retour 3: autre erreur
         * */
        if (!empty($this->getUserByLogin($login))){
            return 2;
            // Le login existe
            // ToDo: Renvoyer un message utile à l'utilisateur
        }
        
        try{
            $this->dbh->beginTransaction();
            $stmt = $this->dbh->prepare("INSERT INTO user (login, pw, firstname, lastname, email, registerdate) VALUES (:login, :pw, :firstname, :lastname, :email, CURRENT_DATE());");
            $stmt->bindParam(':login', $login, PDO::PARAM_STR);
            $stmt->bindParam(':pw', $pw, PDO::PARAM_STR);
            $stmt->bindParam(':firstname', $firstname, PDO::PARAM_STR);
            $stmt->bindParam(':lastname', $lastname, PDO::PARAM_STR);
            $stmt->bindParam(':email', $email, PDO::PARAM_STR);

            $stmt->execute();
            $this->dbh->commit();
            return 1;
        }
        catch (PDOException $e) {
            $this->dbh->rollBack();
            echo "Impossible d'enregistrer l'utilisateur<br>";
        }
        return 3;
    }
    
    /**
     * Enregistrer le jeton de session dans la table user.
     */
    public function setUserToken(string $login,string $token=Null):bool{
        try{
            $this->dbh->beginTransaction();
            if ($token==Null){
                $stmt = $this->dbh->prepare("UPDATE user SET token = NULL WHERE login = :login");
            }
            else{
                $stmt = $this->dbh->prepare("UPDATE user SET token =  :token WHERE login = :login");
                $stmt->bindParam(':token', $token, PDO::PARAM_STR);
            }
            $stmt->bindParam(':login', $login, PDO::PARAM_STR);
            $stmt->execute();
            $this->dbh->commit();
            return True;
        }
        catch (PDOException $e) {
            $this->dbh->rollBack();
        }
        return False;
    }

    //#########################################################################
    //#########################################################################
    /*
     * Fonctions utilisées pour le déploiement du site
     */
    public function createTables():void{
        $bold  = "\e[1m";
        $reset = "\e[0m";
        // Table game
        try {
            $this->dbh->beginTransaction();
            $this->dbh->exec("CREATE TABLE IF NOT EXISTS game (
            id INT AUTO_INCREMENT,
            name VARCHAR(100),
            PRIMARY KEY (id)
            );" );
            $this->dbh->commit();
            echo "Table ${bold}game${reset} créée\n";
            }
        catch (PDOException $e) {
            $this->dbh->rollBack();
            echo "Impossible de créer la table ${bold}game${reset}\n";
        }

        // Table card
        // Contient la valeur et le nom des carte
        try {
            $this->dbh->beginTransaction();
            $this->dbh->exec("CREATE TABLE IF NOT EXISTS card (
            id INT AUTO_INCREMENT,
            name VARCHAR(100) NOT NULL,
            value VARCHAR(255),
            game INT,
            FOREIGN KEY (game) REFERENCES game(id),
            PRIMARY KEY (id)
            );" );
            $this->dbh->commit();
            echo "Table ${bold}card${reset} créée\n";
            }
        catch (PDOException $e) {
            $this->dbh->rollBack();
            echo "Impossible de créer la table ${bold}card${reset}\n";
        }

        // Table round
        try {
            $this->dbh->beginTransaction();
            $this->dbh->exec("CREATE TABLE IF NOT EXISTS round (
            code VARCHAR(6) NOT NULL,
            pw VARCHAR(255),
            game INT,
            FOREIGN KEY (game) REFERENCES game(id),
            PRIMARY KEY (code)
            );" );
            $this->dbh->commit();
            echo "Table ${bold}round${reset} créée\n";
            }
        catch (PDOException $e) {
            $this->dbh->rollBack();
            echo "Impossible de créer la table ${bold}round${reset}\n";
        }

        // Colonne creationdate
        try {
            $this->dbh->beginTransaction();
            $this->dbh->exec("ALTER TABLE `round` ADD `creationdate` TIMESTAMP NOT NULL AFTER `game`;");
            $this->dbh->commit();
        }
        catch (PDOException $e) {
            $this->dbh->rollBack();
            echo "Impossible d'ajouter la colonne `creationdate` à la table ${bold}round${reset}\n";
        }

        // Colonne owner
        try {
            $this->dbh->beginTransaction();
            $this->dbh->exec("ALTER TABLE `round` ADD `owner` INT AFTER `creationdate`;");
            $this->dbh->exec("ALTER TABLE `round` FOREIGN KEY (owner) REFERENCES user(id);");
            $this->dbh->commit();
        }
        catch (PDOException $e) {
            $this->dbh->rollBack();
            echo "Impossible d'ajouter la colonne `owner` à la table ${bold}round${reset}\n";
        }

        // Table user
        try {
            $this->dbh->beginTransaction();
            $this->dbh->exec("CREATE TABLE IF NOT EXISTS user (
            id INT AUTO_INCREMENT,
            login VARCHAR(30),
            pw VARCHAR(255),
            firstname VARCHAR(30),
            lastname VARCHAR(30),
            email VARCHAR(100),
            registerdate DATE NOT NULL,
            PRIMARY KEY (id)
            );" );
            $this->dbh->commit();
            echo "Table ${bold}user${reset} créée\n";
            }
        catch (PDOException $e) {
            $this->dbh->rollBack();
            echo "Impossible de créer la table ${bold}user${reset}\n";
        }

        // Colonne registerdate
        try {
            $this->dbh->beginTransaction();
            $this->dbh->exec("ALTER TABLE `user` ADD `registerdate` DATE NOT NULL AFTER `email`;");
            $this->dbh->commit();
        }
        catch (PDOException $e) {
            $this->dbh->rollBack();
            echo "Impossible d'ajouter la colonne `registerdate` à la table ${bold}user${reset}\n";
        }

        // Colonne token
        try {
            $this->dbh->beginTransaction();
            $this->dbh->exec("ALTER TABLE `user` ADD `token` VARCHAR(255) AFTER `pw`;");
            $this->dbh->commit();
        }
        catch (PDOException $e) {
            $this->dbh->rollBack();
            echo "Impossible d'ajouter la colonne `token` à la table ${bold}user${reset}\n";
        }
        
        // Colonne session à supprimer
        try {
            $this->dbh->beginTransaction();
            $this->dbh->exec("ALTER TABLE `user` DROP COLUMN `session`;");
            $this->dbh->commit();
        }
        catch (PDOException $e) {
            $this->dbh->rollBack();
            echo "Impossible de supprimer la colonne `session` de la table ${bold}user${reset}\n";
        }

        // Table points
        try {
            $this->dbh->beginTransaction();
            $this->dbh->exec("CREATE TABLE IF NOT EXISTS points (
            id INT AUTO_INCREMENT,
            card INT,
            amount INT,
            round VARCHAR(6),
            guest VARCHAR(30),
            user INT,
            FOREIGN KEY (id) REFERENCES card(id),
            PRIMARY KEY (id)
            );" );
            //FOREIGN KEY (id) REFERENCES user(id), ?
            $this->dbh->commit();
            echo "Table ${bold}points${reset} créée\n";
            }
        catch (PDOException $e) {
            $this->dbh->rollBack();
            echo "Impossible de créer la table ${bold}points${reset}\n";
        }
    }


    
    public function populateTables():void{
        // Table round, censure de mots grossiers
        $bold  = "\e[1m";
        $reset = "\e[0m";
        try {
            // On récupère la liste des mots à retirer
            $badwords=[];
            if ($handle = opendir( 'badwords')) {
                while (false !== ($entry = readdir($handle))) {
                    if (is_file("badwords/$entry")){
                        echo "fichier $bold$entry$reset trouvé\n";
                        require_once("badwords/$entry");
                    }
                }
                closedir($handle);
            }
            
            // Suppression des accents
            $normalizeChars = array(
                'Š'=>'S', 'š'=>'s', 'Ð'=>'Dj','Ž'=>'Z', 'ž'=>'z', 'À'=>'A', 'Á'=>'A', 'Â'=>'A', 'Ã'=>'A', 'Ä'=>'A',
                'Å'=>'A', 'Æ'=>'A', 'Ç'=>'C', 'È'=>'E', 'É'=>'E', 'Ê'=>'E', 'Ë'=>'E', 'Ì'=>'I', 'Í'=>'I', 'Î'=>'I',
                'Ï'=>'I', 'Ñ'=>'N', 'Ń'=>'N', 'Ò'=>'O', 'Ó'=>'O', 'Ô'=>'O', 'Õ'=>'O', 'Ö'=>'O', 'Ø'=>'O', 'Ù'=>'U', 'Ú'=>'U',
                'Û'=>'U', 'Ü'=>'U', 'Ý'=>'Y', 'Þ'=>'B', 'ß'=>'Ss','à'=>'a', 'á'=>'a', 'â'=>'a', 'ã'=>'a', 'ä'=>'a',
                'å'=>'a', 'æ'=>'a', 'ç'=>'c', 'è'=>'e', 'é'=>'e', 'ê'=>'e', 'ë'=>'e', 'ì'=>'i', 'í'=>'i', 'î'=>'i',
                'ï'=>'i', 'ð'=>'o', 'ñ'=>'n', 'ń'=>'n', 'ò'=>'o', 'ó'=>'o', 'ô'=>'o', 'õ'=>'o', 'ö'=>'o', 'ø'=>'o', 'ù'=>'u',
                'ú'=>'u', 'û'=>'u', 'ü'=>'u', 'ý'=>'y', 'ý'=>'y', 'þ'=>'b', 'ÿ'=>'y', 'ƒ'=>'f',
                'ă'=>'a', 'î'=>'i', 'â'=>'a', 'ș'=>'s', 'ț'=>'t', 'Ă'=>'A', 'Î'=>'I', 'Â'=>'A', 'Ș'=>'S', 'Ț'=>'T',
            );
            foreach($badwords as $key=>$value){
                $badwords[$key] = strtr($value, $normalizeChars);
            }
            
            // Suppression des doublons
            $badwords=array_unique($badwords);
        }
        catch (PDOException $e) {
            echo "Impossible de récupérer les mots blacklistés\n";
        }
        
        try{
            // On insère les mots dans la table round
            echo "${bold}Ajout des mots blacklistés${reset}\n";
            $this->dbh->beginTransaction();
            $stmt = $this->dbh->prepare("INSERT INTO round (code, pw, game, creationdate) VALUES (:code, NULL, NULL, NOW());");
            $stmt->bindParam(':code', $code);
            foreach($badwords as $code){
                if (strlen($code)==5){
                    $stmt->execute();
                    echo "ajout du mot $bold$code$reset\n";
                }
            }
            $this->dbh->commit();
            echo "Commit...\n";
        }
        catch (PDOException $e) {
            $this->dbh->rollBack();
            echo "Impossible d'enregistrer les mots blacklistés\n";
        }

        try{
            // On configure les premiers jeux
            // Pour les tests
            echo "${bold}Ajout des jeux 1 et 2${reset}\n";
            $games=array("It's a Wonderful World","Autre jeu");
            $this->dbh->beginTransaction();
            $stmt = $this->dbh->prepare("INSERT INTO `game` (`id`, `name`) VALUES (:id, :name);");
            $stmt->bindParam(':name', $name, PDO::PARAM_STR);
            $stmt->bindParam(':id', $id, PDO::PARAM_INT);
            $id=1;
            foreach($games as $name){
                    $stmt->execute();
                    echo "ajout du jeu n°$id: $bold$name$reset\n";
                    $id++;
                }
            $this->dbh->commit();
            echo "Commit...\n";
        }
        catch (PDOException $e) {
            $this->dbh->rollBack();
            echo "Impossible d'enregistrer les jeux\n";
        }
        
        //return true;
    }
    
}
