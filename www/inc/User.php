<?php

class User{

    private $id        = Null;
    private $login     = Null;
    private $session   = Null;
    private $firstname = Null;
    private $lastname  = Null;
    private $email     = Null;
    private $status    = 0;
    // 1 = user chargé depuis la bdd
    // 2 = user non trouvé dans la bdd
    // 3 = mdp incorrect


    public function __construct(string $login=Null,string $pw=Null,string $email=Null, string $firstname=Null,string $lastname=Null){
        // Enregistrement d'un nouveau compte
        if (isset($login) && isset($pw) && isset($email)){
            $ret=$this->register($login,$pw,$email,$firstname,$lastname);
            if ($ret == 1){
                // Utilisateur créé, connexion automatique
                $this->logUser($login,$pw);
            }
            else{
                // Prévoir un message utile à l'utilisateur
                //echo "Ret $ret";
            }
        }
        
        elseif ($this->id==Null){
            echo "construct user: session php<br>";
             // On récupère les infos depuis la session PHP
            if ((isset($_SESSION['id'])
            and isset($_SESSION['login'])
            and isset($_SESSION['session'])
            and isset($_SESSION['firstname'])
            and isset($_SESSION['lastname']) )
            and isset($_SESSION['email'])){
                $this->id        = $_SESSION['id'];
                $this->login     = $_SESSION['login'];
                $this->session   = $_SESSION['session'];
                $this->firstname = $_SESSION['firstname'];
                $this->lastname  = $_SESSION['lastname'];
                $this->email     = $_SESSION['email'];
            }

            // On récupère les infos depuis la BDD si cookie
            elseif (isset($_COOKIE['session']) and isset($_COOKIE['user'])){
                $db   = new Database;
                $user = $db->getUserBySession($_COOKIE['user'],$_COOKIE['session']);
                $this->id        = $user['id'];
                $this->login     = $user['login'];
                $this->session   = $user['session'];
                $this->firstname = $user['firstname'];
                $this->lastname  = $user['lastname'];
                $this->email     = $user['email'];
                
            }
            
            // On récupère les infos depuis la BDD si login et pw
            elseif (isset($login) && isset($pw)){
                $this->logUser($login,$pw);
                echo "construct user: bdd<br>";
            }
        }
    }
    
    private function register(string $login,string $pw,string $email,string $firstname=NULL,string $lastname=NULL):int{
        $db = new Database;
        $ret= $db->registerUser($login,$pw,$email,$firstname,$lastname);
        return $ret;
    }
    
    public function logUser(string $login, string $pw):bool{
        $db = new Database;
        $user = $db->getUserByLogin($login);
        if (!empty($user) && password_verify($pw,$user["pw"])){
            // On charge l'utilisateur
            $this->id        = $user['id'];
            $this->login     = $user['login'];
            $this->session   = $user['session'];
            $this->firstname = $user['firstname'];
            $this->lastname  = $user['lastname'];
            $this->email     = $user['email'];
            $this->status=1; // trouvé
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

    public function getStatus():int{
        return $this->status;
    }

    public function __tostring():string{
        $str ="id: ".       htmlentities($this->id).       "<br>";
        $str.="login: ".    htmlentities($this->login).    "<br>";
        $str.="session: ".  htmlentities($this->session).  "<br>";
        $str.="firstname: ".htmlentities($this->firstname)."<br>";
        $str.="lastname: ". htmlentities($this->lastname). "<br>";
        $str.="email: ".    htmlentities($this->email). "<br>";
        $str.="status: ".    htmlentities($this->status);
        return $str;
    }
    
}
