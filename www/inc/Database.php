<?php
//Gérer les accès à la base de donnée
class Database{
    
    private $db_host="localhost";
    private $db_user="eleve";
    private $db_pass="eleve";
    private $db_name="minotaure";

    private $dbh=Null;

    public function __construct(){
        if ($this->dbh==Null){
            try {
                $this->dbh = new PDO('mysql:host='.$this->db_host.';dbname='.$this->db_name, $this->db_user, $this->db_pass);
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
            $stmt = $this->dbh->prepare("INSERT INTO round (code, pw, game) VALUES (:code, :pw, :game)");
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
    
    public function createTables():void{
        $bold  = "\e[1m";
        $reset = "\e[0m";
        // Table game
        try {
            $this->dbh->beginTransaction();
            $this->dbh->exec("CREATE TABLE game (
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
            $this->dbh->exec("CREATE TABLE card (
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
            $this->dbh->exec("CREATE TABLE round (
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

        // Table user
        try {
            $this->dbh->beginTransaction();
            $this->dbh->exec("CREATE TABLE user (
            id INT AUTO_INCREMENT,
            login VARCHAR(30),
            firstname VARCHAR(30),
            lastname VARCHAR(30),
            email VARCHAR(100),
            PRIMARY KEY (id)
            );" );
            $this->dbh->commit();
            echo "Table ${bold}user${reset} créée\n";
            }
        catch (PDOException $e) {
            $this->dbh->rollBack();
            echo "Impossible de créer la table ${bold}user${reset}\n";
        }

        // Table points
        try {
            $this->dbh->beginTransaction();
            $this->dbh->exec("CREATE TABLE points (
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
            if ($handle = opendir( DEPLOYDIR.'/badwords')) {
                while (false !== ($entry = readdir($handle))) {
                    if (is_file(DEPLOYDIR."/badwords/$entry")){
                        echo "fichier $bold$entry$reset trouvé\n";
                        require_once(DEPLOYDIR."/badwords/$entry");
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
                $badword[$key] = strtr($value, $normalizeChars);
            }
            
            // Suppression des doublons
            $badwords=array_unique($badwords);
        }
        catch (PDOException $e) {
            echo "Impossible de récupérer les mots blacklistés<br>";
        }
        
        try{
            // On insère les mots dans la table round
            echo "${bold}Ajout des mots blacklistés${reset}\n";
            $this->dbh->beginTransaction();
            $stmt = $this->dbh->prepare("INSERT INTO round (code, pw, game) VALUES (:code, NULL, NULL)");
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
            $stmt = $this->dbh->prepare("INSERT INTO game (name) VALUES (:name)");
            $stmt->bindParam(':name', $name);
            foreach($games as $name){
                    $stmt->execute();
                    echo "ajout du jeu $bold$name$reset\n";
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
