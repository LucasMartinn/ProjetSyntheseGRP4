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
            else{
                // Prévoir un message utile à l'utilisateur
                //echo "Ret $ret";
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
                if (password_verify($_COOKIE['token'],$user['token'])){
                    $this->id        = $user['id'];
                    $this->login     = $user['login'];
                    $this->token     = $user['token'];
                    $this->pw        = $user['pw'];
                    $this->firstname = $user['firstname'];
                    $this->lastname  = $user['lastname'];
                    $this->email     = $user['email'];
                    $this->status    = 1;
                    return;
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
