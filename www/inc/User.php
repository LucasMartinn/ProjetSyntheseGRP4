<?php

class User{
    private $id;
    private $login;
    private $token;
    private $pw;
    private $firstname;
    private $lastname;
    private $email;
    private $status;
    // 0 = user non chargé
    // 1 = user chargé
    // 2 = user non trouvé dans la bdd
    // 3 = mdp incorrect
    // 4 = enregistrement impossible car l'utilisateur existe déjà
    // 5 = l'enregistrement a échoué pour une autre raison

    public function __construct(string $login=Null,string $pw=Null,string $email=Null, string $firstname=Null,string $lastname=Null){
        $this->reset();
        // Enregistrement d'un nouveau compte
        if (isset($login) && isset($pw) && isset($email)){
            $ret=$this->register($login,$pw,$email,$firstname,$lastname);
            if ($ret == 1){
                // Utilisateur créé, connexion automatique
                if($this->login($login,$pw)){
                    return;
                }
            }
            elseif ($ret == 2){
                // Le login est déjà utilisé
                $this->status=4;
                return;
            }
            else{
                // Autre erreur
                $this->status=5;
                return;
            }
        }

        // On récupère les infos depuis la BDD si login et pw
        if (isset($login) && isset($pw)){
            if($this->login($login,$pw)){
                return;
            }
        }
        
        if ($this->status==0){
            // On récupère les infos depuis la session PHP
            // Avantage: ne nécessite pas d'accès à la base de donnée
            if ((isset($_SESSION['id'])
            and isset($_SESSION['login'])
            and isset($_SESSION['token'])
            and isset($_SESSION['pw'])
            and isset($_SESSION['firstname'])
            and isset($_SESSION['lastname']) )
            and isset($_SESSION['email'])){
                $this->id        = $_SESSION['id'];
                $this->login     = $_SESSION['login'];
                $this->token     = $_SESSION['token'];
                $this->pw        = $_SESSION['pw'];
                $this->firstname = $_SESSION['firstname'];
                $this->lastname  = $_SESSION['lastname'];
                $this->email     = $_SESSION['email'];
                $this->status    = 1;
                return;
            }

            // On récupère les infos depuis la BDD si token valide
            if (isset($_COOKIE['token']) and isset($_COOKIE['login'])){
                $db   = new Database;
                $user = $db->getUserByLogin($_COOKIE['login']);
                if (!empty($user)){
                    if (password_verify($_COOKIE['token'],$user['token'])){
                        $this->id        = $user['id'];
                        $this->login     = $user['login'];
                        $this->token     = $user['token'];
                        $this->pw        = $user['pw'];
                        $this->firstname = $user['firstname'];
                        $this->lastname  = $user['lastname'];
                        $this->email     = $user['email'];
                        $this->status    = 1;
                        $this->setSession();
                        return;
                    }
                }
            }

        }
    }

    private function reset(){
        // On charge les valeurs par défaut
        $this->id        = Null;
        $this->login     = Null;
        $this->token     = Null;
        $this->pw        = Null;
        $this->firstname = Null;
        $this->lastname  = Null;
        $this->email     = Null;
        $this->status    = 0;
    }

    private function register(string $login,string $pw,string $email,string $firstname=NULL,string $lastname=NULL):int{
        $db = new Database;
        $ret= $db->registerUser($login, password_hash($pw, PASSWORD_DEFAULT),$email,$firstname,$lastname);
        return $ret;
    }
    
    private function login(string $login, string $pw):bool{
        $db = new Database;
        $user = $db->getUserByLogin($login);
        if (!empty($user) && password_verify($pw,$user["pw"])){
            // On charge l'utilisateur
            $this->id        = $user['id'];
            $this->login     = $user['login'];
            $this->pw        = $user['pw'];
            $this->firstname = $user['firstname'];
            $this->lastname  = $user['lastname'];
            $this->email     = $user['email'];
            $this->status    = 1; // trouvé
            
            $this->setSession();
            $this->setCookie();
            
            return True;
        }
        elseif (empty($user)){
            $this->status=2; // non trouvé dans la bdd
        }
        else{
            $this->status=3; // mot de passe incorrect
        }
        return False;
    }

    /**
     * Enregistrer les données de l'utilisateur en session
     * pour les récupérer durant la navigation sur le site.
     */
    public function setSession(){
        $_SESSION['id']         = $this->id;
        $_SESSION['login']      = $this->login;
        $_SESSION['token']      = $this->token;
        $_SESSION['pw']         = $this->pw;
        $_SESSION['firstname']  = $this->firstname;
        $_SESSION['lastname']   = $this->lastname;
        $_SESSION['email']      = $this->email;
    }
    
    /**
     * Effacer les données de l'utilisateur en session.
     */
    public function unsetSession(){
        unset ($_SESSION['id']);
        unset ($_SESSION['login']);
        unset ($_SESSION['token']);
        unset ($_SESSION['pw']);
        unset ($_SESSION['firstname']);
        unset ($_SESSION['lastname']);
        unset ($_SESSION['email']);
    }

    /**
     * Enregistrer un cookie de session pour 1 an.
     * La connexion depuis un autre appareil déconnecte
     * la session précédente (régénération du jeton).
     */
    public function setCookie():bool{
        if ($this->status == 1){
            // Générer un jeton unique de 64 caractères hexadécimaux
            $this->token = bin2hex(random_bytes(32));
            // Enregistrer l'user et le jeton dans un cookie
            setcookie ("login" , $this->login , time()+366*24*60*60);
            setcookie ("token" , $this->token , time()+366*24*60*60);
            // Enregistrer le jeton dans la BDD
            $db = new Database;
            if ($db->setUserToken($this->login, password_hash($this->token, PASSWORD_DEFAULT))){
                return True;
            }
        }
        $this->token = Null;
        return False;
    }

    /**
     * Supprimer le cookie de session.
     */
    public function unsetCookie():bool{
        // Purger les cookies
        setcookie ("login" , "" , time() - 3600);
        setcookie ("token" , "" , time() - 3600);
        // Supprimer le jeton de la BDD
        $db = new Database;
        if ($db->setUserToken($this->login, Null)){
            return True;
        }
        return False;
    }

    public function logout(){
        if($this->login){
            // On supprime le jeton
            $this->unsetCookie();
            // On vide la session
            $this->unsetSession();
            // On réinitialise les variables privées de l'utilisateur
            $this->reset();
        }
    }

    public function getStatus():int{
        return $this->status;
    }
    
    public function getLogin():?string{
        return $this->login;
    }

    public function getLastname():?string{
        return $this->lastname;
    }

    public function getFirstname():?string{
        return $this->firstname;
    }

    public function getEmail():?string{
        return $this->email;
    }

    public function getPw():?string{
        return $this->pw;
    }

    public function getId():?int{
        return $this->id;
    }

    public function setPw(int $id, string $pw):bool{
        $this->pw = password_hash($pw, PASSWORD_DEFAULT);
        $db = new Database;
        $this->setSession();
        return $db->setPw($id, $pw);
    }

    public function setFirstname(int $id, string $firstname):bool{
        $this->firstname = $firstname;
        $db = new Database;
        $this->setSession();
        return $db->setFirstname($id, $firstname);
    }

    public function setLastname(int $id, string $lastname):bool{
        $this->lastname = $lastname;
        $db = new Database;
        $this->setSession();
        return $db->setLastname($id, $lastname);
    }

    public function setEmail(int $id, string $email):bool{
        $this->email = $email;
        $db = new Database;
        $this->setSession();
        return $db->setEmail($id, $email);
    }
    
    public function getUserByLogin(string $login):bool{
        $db   = new Database;
        $user = $db->getUserByLogin($login);
        if (!empty($user)){
            $this->id        = $user['id'];
            $this->login     = $user['login'];
            $this->token     = $user['token'];
            $this->pw        = $user['pw'];
            $this->firstname = $user['firstname'];
            $this->lastname  = $user['lastname'];
            $this->email     = $user['email'];
            $this->status    = 3;
            return True;
        }
        return false;
    }
    
    public function sendCode():bool{
        $to = $this->email;
        $subject = "Minotaure - Récupération du mot de passe";
        $headers[] = 'MIME-Version: 1.0';
        $headers[] = 'Content-type: text/html; charset=utf-8';
        $headers[] = 'From: Minotaure <ne-pas-repondre@cyril-minette.net>';
        
        $code=sha1($this->pw);
        
        $lien="http://".$_SERVER['SERVER_NAME'].pathinfo ( $_SERVER["PHP_SELF"] ,  PATHINFO_DIRNAME )."/lostpw.php?l=".urlencode($this->login)."&c=".urlencode($code);
        
        $message = "<html><body>
        <p>Vous avez demandé la récupération de votre mot de passe.
        Pour validercette action, cliquez sur le lien suivant:</p>
        <p>
        <a href='$lien'>$lien</a>
        </p>
        <p>Si le lien ne fonctionne pas, vous pouvez copier-coller cette
        adresse dans votre navigateur web.</p>
        <p>Si vous n'êtes pas à l'orrigine de cette demande, vous
        pouvez simplement ignorer cet e-mail.</p>
        
        <p>Cordialement,<br>
        L'équipe de Minotaure.</p>
        </body><html>";

        return mail($to,$subject,$message,implode("\r\n", $headers));
    }

    public function sendNewPw(string $code):bool{
        
        if (sha1($this->pw) != $code){
            return false;
        }
        
        $pw = bin2hex(random_bytes(5));
        $this->setPw($this->id, $pw);
        
        $to = $this->email;
        $subject = "Minotaure - Nouveau mot de passe";
        $headers[] = 'MIME-Version: 1.0';
        $headers[] = 'Content-type: text/html; charset=utf-8';
        $headers[] = 'From: Minotaure <ne-pas-repondre@cyril-minette.net>';

        $message = "<html><body>
        <p>Un nouveau mot de passe vous a été attribué. Nous
        vous conseillons de le modifier dès votre connexion
        depuis votre profil.</p>

        <p>Nouveau mot de passe: <strong>$pw</strong></p>

        <p>Cordialement,<br>
        L'équipe de Minotaure.</p></body></html>";
        mail($to,$subject,$message,implode("\r\n", $headers));
        return true;
    }

public function getRounds($limit=null, $page=null):?array{
    //Retourne un tableau de résultats
    if($this->id!=null){
        $db     = new Database;
        $result = $db->getRoundsFromUser($this->id, $limit, $page);
        if (gettype($result)=="array"){
            return $result;
        }
        return array();
    }
    return null;
}

public function getEmptyRounds():?array{
    //Retourne un tableau de résultats
    if($this->id!=null){
        $db     = new Database;
        $result = $db->getEmptyRoundsFromUser($this->id);
        if (gettype($result)=="array"){
            return $result;
        }
        return array();
    }
    return null;
}

    public function __tostring():string{
        $str ="id: ".       htmlentities($this->id).       "<br>";
        $str.="login: ".    htmlentities($this->login).    "<br>";
        $str.="token: ".    htmlentities($this->token).    "<br>";
        $str.="firstname: ".htmlentities($this->firstname)."<br>";
        $str.="lastname: ". htmlentities($this->lastname). "<br>";
        $str.="email: ".    htmlentities($this->email).    "<br>";
        $str.="status: ".    htmlentities($this->status);
        return $str;
    }
    
}
