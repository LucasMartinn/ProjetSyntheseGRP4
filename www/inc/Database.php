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
            if (isset($_SESSION['id'])){
                $owner=$_SESSION['id'];
            }
            else{
                $owner=Null;
            }
            $hash=password_hash($pw, PASSWORD_DEFAULT);
            $this->dbh->beginTransaction();
            $stmt = $this->dbh->prepare("INSERT INTO round (code, pw, game, creationdate, owner) VALUES (:code, :pw, :game, NOW(), :owner)");
            $stmt->bindParam(':code', $code, PDO::PARAM_STR, 5);
            $stmt->bindParam(':pw', $hash, PDO::PARAM_STR);
            $stmt->bindParam(':game', $game, PDO::PARAM_INT);
            $stmt->bindParam(':owner', $owner, PDO::PARAM_INT);
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

    public function getRound(string $code):array{
        try {
            $this->dbh->beginTransaction();
            $stmt = $this->dbh->prepare("SELECT code, pw, game, creationdate, owner, id, name gamename FROM round, game WHERE code=:code AND round.game = game.id");
            $stmt->bindParam(':code', $code, PDO::PARAM_STR);
            $stmt->execute();
            $result = $stmt->fetch(PDO::FETCH_ASSOC);
            $this->dbh->commit();
        }
        catch (PDOException $e) {
            $this->dbh->rollBack();
            echo "Récupération de la partie impossible";
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

public function setPoint(string $round, int $card, int $amount, int $multi, ?int $user=Null, ?string $guest=Null):int{
    try{
        // Il faut strictement une valeur pour $guest ou pour $user
        if (($user == Null && $guest == Null) || ($user != Null && $guest != Null)) {
            return 0;
        }
        $this->dbh->beginTransaction();
        // On vérifie que l'enregistrement n'existe pas déjà
        if ($user !=Null){
            $var_user = "user = :user";
            $var_guest= "guest IS NULL";
        }
        else{
            $var_user = "user IS NULL";
            $var_guest= "guest = :guest";
        }
        $stmt=$this->dbh->prepare("SELECT COUNT(*) q FROM points WHERE round = :round AND card = :card AND $var_user AND $var_guest;");
        $stmt->bindParam(':round',  $round,  PDO::PARAM_STR);
        $stmt->bindParam(':card',   $card,   PDO::PARAM_INT);
        if ($user != Null){
            $stmt->bindParam(':user',   $user,   PDO::PARAM_INT);
        }
        else{
            $stmt->bindParam(':guest',  $guest,  PDO::PARAM_STR);
        }
        $stmt->execute();
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        if ($result['q']>0){
            return 3; // Il y a déjà un enregistrement, on quitte
        }
        // On enregistre les points
        if ($user == Null){
            $stmt = $this->dbh->prepare("INSERT INTO points (round, card, amount, multi, user, guest)
            VALUES (:round, :card, :amount, :multi, NULL, :guest);");
            $stmt->bindParam(':guest',  $guest,  PDO::PARAM_STR);
        }
        else{
            $stmt = $this->dbh->prepare("INSERT INTO points (round, card, amount, multi, user, guest)
            VALUES (:round, :card, :amount, :multi, :user, NULL);");
            $stmt->bindParam(':user',   $user,   PDO::PARAM_INT);
        }
        
        $stmt->bindParam(':round',  $round,  PDO::PARAM_STR);
        $stmt->bindParam(':card',   $card,   PDO::PARAM_INT);
        $stmt->bindParam(':amount', $amount, PDO::PARAM_INT);
        $stmt->bindParam(':multi',  $multi,  PDO::PARAM_INT);
        $stmt->execute();
        $this->dbh->commit();
        return 1;
    }
    catch (PDOException $e) {
        $this->dbh->rollBack();
        echo "Impossible d'enregistrer le score<br>";
    }
    return False;
}

    public function getPlayers(string $round):array{
        try {
            $this->dbh->beginTransaction();
            $stmt = $this->dbh->prepare(
            "SELECT DISTINCT user 'id', guest 'guestname', login, SUM(amount * multi) score FROM points, user WHERE round=:round AND user.id = points.user
            GROUP BY id
            UNION
            SELECT DISTINCT user 'id', guest 'guestname', NULL 'login', SUM(amount * multi) score FROM points WHERE round=:round AND points.user IS NULL
            GROUP BY guestname
            ORDER BY score");
            $stmt->bindParam(':round', $round, PDO::PARAM_STR);
            $stmt->execute();
            $result = $stmt->fetchall(PDO::FETCH_ASSOC);
            $this->dbh->commit();
        }
        catch (PDOException $e) {
            $this->dbh->rollBack();
            echo "Récupération des joueurs impossible";
        }
        if (gettype($result)=="array"){
            return $result;
        }
        else{
            return array();
        }
    }

    public function getRoundsFromUser(int $id, int $limit=null, int $page=null):?array{
    // Retourne les parties auxquelles l'utilisateur a participé
        try {
            $req="SELECT DISTINCT round, SUM(amount * multi) score, name game
            FROM points, user, round, game
            WHERE points.user=:id
            AND user.id = points.user
            AND points.round = round.code
            AND round.game = game.id
            GROUP BY round
            ORDER BY score";
            if ($limit != null){
                $req  .= " LIMIT :begin, :rows";
                $begin = ($page-1)*$limit;
                $rows  = $limit;
            }
            
            $this->dbh->beginTransaction();
            $stmt = $this->dbh->prepare($req);
            $stmt->bindParam(':id', $id, PDO::PARAM_INT);
            if ($limit != null){
                $stmt->bindParam(':begin', $begin, PDO::PARAM_INT);
                $stmt->bindParam(':rows' , $rows,  PDO::PARAM_INT);
            }
            $stmt->execute();
            $result = $stmt->fetchall(PDO::FETCH_ASSOC);
            $this->dbh->commit();
        }
        catch (PDOException $e) {
            $this->dbh->rollBack();
            echo "Récupération des parties impossible";
        }
        if (gettype($result)=="array"){
            return $result;
        }
        else{
            return array();
        }
    }

    public function getEmptyRoundsFromUser(int $id):array{
        // Retourne les parties créées par l'utilisateur mais auxquelles il n'a pas participé
        try {
            $req="SELECT code round, name game
            FROM round, game
            WHERE owner = :id
            AND code NOT IN (
            SELECT DISTINCT round
            FROM points
            WHERE points.user=:id
            )
            AND round.game = game.id";

            
            $this->dbh->beginTransaction();
            $stmt = $this->dbh->prepare($req);
            $stmt->bindParam(':id', $id, PDO::PARAM_INT);
            $stmt->execute();
            $result = $stmt->fetchall(PDO::FETCH_ASSOC);
            $this->dbh->commit();
        }
        catch (PDOException $e) {
            $this->dbh->rollBack();
            echo "Récupération des parties non jouées impossible";
        }
        if (gettype($result)=="array"){
            return $result;
        }
        return array();
    }

    public function getPlayerFromRound(int $userid, string $roundcode):bool{
        try {
            $this->dbh->beginTransaction();
            $stmt = $this->dbh->prepare("SELECT COUNT(*) c FROM points WHERE user=:user AND round=:code ORDER BY c DESC");
            $stmt->bindParam(':user', $userid,    PDO::PARAM_INT);
            $stmt->bindParam(':code', $roundcode, PDO::PARAM_STR);
            $stmt->execute();
            $result = $stmt->fetch(PDO::FETCH_ASSOC);
            $this->dbh->commit();
        }
        catch (PDOException $e) {
            $this->dbh->rollBack();
            echo "Ipossible de vérifier la présence du joueur";
        }
        //return $result;
        return ($result['c']>0);
    }

    public function getPoints(string $round, ?int $userid, ?string $guestname):array{
        try {
            if ( ($userid == Null && $guestname == Null) || ($userid != Null && $guestname != Null) ){
                die ("Erreur de récupération des points: nom d'utilisateur ou invité invalide");
            }
            $this->dbh->beginTransaction();
            if($userid == Null){
                $stmt = $this->dbh->prepare(
                "SELECT card, amount, points.multi, name cardname, name_plural cardname_p FROM points, card
                WHERE round=:round
                AND   user IS NULL
                AND   guest=:guestname
                AND   points.card = card.id ");
                $stmt->bindParam(':guestname', $guestname, PDO::PARAM_STR);
            }
            else{
                $stmt = $this->dbh->prepare(
                "SELECT card, amount, points.multi, name cardname, name_plural cardname_p FROM points, card
                WHERE round=:round
                AND   user=:userid
                AND   guest IS NULL
                AND   points.card = card.id ");
                $stmt->bindParam(':userid',    $userid, PDO::PARAM_INT);
            }
            $stmt->bindParam(':round',     $round, PDO::PARAM_STR);
            $stmt->execute();
            $result = $stmt->fetchall(PDO::FETCH_ASSOC);
            $this->dbh->commit();
        }
        catch (PDOException $e) {
            $this->dbh->rollBack();
            echo "Récupération des points impossible";
        }
        if (gettype($result)=="array"){
            return $result;
        }
        else{
            return array();
        }
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

    public function setFirstname(int $id, string $firstname):bool{
        try {
            $this->dbh->beginTransaction();
            $stmt = $this->dbh->prepare("UPDATE user SET firstname=:firstname WHERE id=:id");
            $stmt->bindParam(':id',        $id,        PDO::PARAM_INT);
            $stmt->bindParam(':firstname', $firstname, PDO::PARAM_STR);
            $stmt->execute();
            $this->dbh->commit();
            return True;
        }
        catch (PDOException $e) {
            $this->dbh->rollBack();
            return False;
            echo "Modification du prénom impossible";
        }
    }

    public function setLastname(int $id, string $lastname):bool{
        try {
            $this->dbh->beginTransaction();
            $stmt = $this->dbh->prepare("UPDATE user SET lastname=:lastname WHERE id=:id");
            $stmt->bindParam(':id',       $id,       PDO::PARAM_INT);
            $stmt->bindParam(':lastname', $lastname, PDO::PARAM_STR);
            $stmt->execute();
            $this->dbh->commit();
            return True;
        }
        catch (PDOException $e) {
            $this->dbh->rollBack();
            return False;
            echo "Modification du nom impossible";
        }
    }

    public function setPw(int $id, string $pw):bool{
        try {
            $hash=password_hash($pw, PASSWORD_DEFAULT);
            $this->dbh->beginTransaction();
            $stmt = $this->dbh->prepare("UPDATE user SET pw=:pw WHERE id=:id");
            $stmt->bindParam(':id', $id, PDO::PARAM_INT);
            $stmt->bindParam(':pw', $hash, PDO::PARAM_STR);
            $stmt->execute();
            $this->dbh->commit();
            return True;
        }
        catch (PDOException $e) {
            $this->dbh->rollBack();
            return False;
            echo "Modification du mot de passe impossible";
        }
    }

    public function setEmail(int $id, string $email):bool{
        try {
            $this->dbh->beginTransaction();
            $stmt = $this->dbh->prepare("UPDATE user SET email=:email WHERE id=:id");
            $stmt->bindParam(':id',    $id,    PDO::PARAM_INT);
            $stmt->bindParam(':email', $email, PDO::PARAM_STR);
            $stmt->execute();
            $this->dbh->commit();
            return True;
        }
        catch (PDOException $e) {
            $this->dbh->rollBack();
            return False;
            echo "Modification de l'e-mail impossible";
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
        // Modifier la table existante
        try {
            $this->dbh->beginTransaction();
            $this->dbh->exec("ALTER TABLE `card` ADD `name_plural` VARCHAR(100) AFTER `name`;");
            $this->dbh->exec("ALTER TABLE `card` ADD `multi` VARCHAR(100) AFTER `name_plural`;");
            $this->dbh->exec("ALTER TABLE `card` DROP COLUMN `value`;");
            $this->dbh->commit();
            echo "Table ${bold}card${reset} modifiée\n";
            }
        catch (PDOException $e) {
            $this->dbh->rollBack();
            echo "Impossible de modifier la table ${bold}card${reset}\n";
        }
        // Créer la table card
        try {
            $this->dbh->beginTransaction();
            $this->dbh->exec("CREATE TABLE IF NOT EXISTS `card` (
            id INT AUTO_INCREMENT,
            name VARCHAR(100) NOT NULL,
            name_plural VARCHAR(100) NOT NULL,
            multi VARCHAR(100) NOT NULL,
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
            multi INT DEFAULT 1,
            round VARCHAR(6),
            guest VARCHAR(30),
            user INT,
            FOREIGN KEY (card) REFERENCES card(id),
            FOREIGN KEY (user) REFERENCES user(id),
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
        // Ajouter une colonne et contrainte de clé étrangère
        /*try {
            $this->dbh->beginTransaction();
            $this->dbh->exec("ALTER TABLE `points` ADD `multi` INT DEFAULT 1 AFTER `amount`;");
            $this->dbh->exec("ALTER TABLE `points` ADD FOREIGN KEY (user) REFERENCES user(id);");
            $this->dbh->commit();
            echo "Table ${bold}points${reset} modifiée\n";
            }
        catch (PDOException $e) {
            $this->dbh->rollBack();
            echo "Impossible de modifier la table ${bold}card${reset}\n";
        }*/
        
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

        try{
            // Cartes du jeu "It's a Wonderful World"
            echo "${bold}Ajout des carte du jeu 1${reset}\n";
            $cards=array(
                // name, name_plural, multi, game
                array("point de victoire","points de victoire", NULL,          1),
                array("carte armée",      "cartes armées",      "X armée",     1),
                array("carte science",    "cartes sciences",    "X science",   1),
                array("carte économie",   "cartes économies",   "X économie",  1),
                array("carte merveille",  "cartes merveilles",  "X merveille", 1),
                array("jeton trader ",    "jetons traders ",    "X trader",    1),
                array("jeton militaire ", "jetons militaires ", "X militaire", 1)
            );
            $this->dbh->beginTransaction();
            $stmt = $this->dbh->prepare("INSERT INTO `card` (`id`, `name`,`name_plural`,`multi`,`game`)
                VALUES (:id, :name, :name_plural, :multi, :game);");
            $stmt->bindParam(':id',          $id,          PDO::PARAM_INT);
            $stmt->bindParam(':name',        $name,        PDO::PARAM_STR);
            $stmt->bindParam(':name_plural', $name_plural, PDO::PARAM_STR);
            $stmt->bindParam(':multi',       $multi,       PDO::PARAM_STR);
            $stmt->bindParam(':game',        $game,        PDO::PARAM_INT);
            $id=1;
            foreach($cards as $line){
                    $name        = $line[0];
                    $name_plural = $line[1];
                    $multi       = $line[2];
                    $game        = $line[3];
                    $stmt->execute();
                    echo "ajout de la carte n°$id: $bold$name$reset\n";
                    echo $id." / ".$line[0]." / ".$line[1]." / ".$line[2]." / ".$line[3]."\n";
                    $id++;
                }
            $this->dbh->commit();
            echo "Commit...\n";
        }
        catch (PDOException $e) {
            $this->dbh->rollBack();
            echo "Impossible d'enregistrer les cartes\n";
        }
        
        //return true;
    }
    
}
