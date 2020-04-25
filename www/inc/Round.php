<?php
require_once("inc/Database.php");
//Gérer les parties
//Créer un tableau d'id de 5 caractères alphabétiques

//Liste de mots à banir:
//https://github.com/snipe/banbuilder/tree/master/src/dict

class Round{
    private $code; //identifiant unique
    private $pw;
    private $creationdate;
    private $game;
    private $owner;
    private $status;
    private $gamename;
    // Status:
    // 0 round non chargé
    // 1 round chargé
    // 2 impossible de trouver la partie
    // 3 Impossible de créer une nouvelle partie
    
    public function __construct( string $game="", string $pw="", string $code=""){
        $this->reset();
        if($pw!="" && $game!=""){
            // Créer une nouvelle partie
            $this->newRound($game, $pw);
            return;
        }
        if ($code!=""){
            // Récupérer la partie depuis la base de donnée
            if ($this->fromDB($code)){
                return;
            }
        }
        if ($this->fromSession()){
            // Récupérer la partie depuis la session PHP
            return;
        }
        $this->status=2;
    }
    
    private function newCode():string{
        $char=str_split('abcdefghijklmnopqrstuvwxyz');
        $code='';
        
        for ($i=0; $i<5; $i++){
            $code .= $char[rand(0,25)];
        }
        return $code;
    }
    
    public function newRound($game, $pw):void{
        for($i=0; $i<24; $i++){
            $db=new Database();
            $code=$this->newCode();
            // On teste le code au xaximum 24 fois, 1 chance sur 8millions
            // de ne pas en trouver si il reste la moitié des codes de disponibles (26^5/2)
            $ret=$db->newRound($code, $pw, $game);
            if ($ret==0){
                // La partie est créée
                // On l'enregistre dans la session
                $_SESSION['round']=$code;
                break;
            }
            if ($ret==2){
                //Il y a eu un problème lors de l'enregistrement dans la base de données
                $this->status=3;
            }
        }
        $this->code=$code;
    }

    public function fromDB(string $code):bool{
        $db=new Database();
        $round=$db->getRound($code);
        if (!empty($round)){
            $this->code         = $round['code'];
            $this->pw           = $round['pw'];
            $this->creationdate = $round['creationdate'];
            $this->game         = $round['game'];
            $this->owner        = $round['owner'];
            $this->gamename     = $round['gamename'];
            $this->status       = 1;
            $this->toSession();
            return True;
        }
        return False;
    }

private function toSession():bool{
    if ($this->status==1){
        $_SESSION['round_code']         = $this->code;
        $_SESSION['round_pw']           = $this->pw;
        $_SESSION['round_creationdate'] = $this->creationdate;
        $_SESSION['round_game']         = $this->game;
        $_SESSION['round_owner']        = $this->owner;
        $_SESSION['round_status']       = $this->status;
        $_SESSION['round_gamename']     = $this->gamename;
        return True;
    }
    return False;
}

private function fromSession():bool{
    if (isset( $_SESSION['round_status'] )){
        $this->code         = $_SESSION['round_code'];
        $this->pw           = $_SESSION['round_pw'];
        $this->creationdate = $_SESSION['round_creationdate'];
        $this->game         = $_SESSION['round_game'];
        $this->owner        = $_SESSION['round_owner'];
        $this->status       = $_SESSION['round_status'];
        $this->gamename     = $_SESSION['round_gamename'];
        return True;
    }
    return False;
}

public function setPoint(int $card, int $amount, int $multi, ?int $user=Null, ?string $guestname=Null):bool{
    $db=new Database();
    $point=$db->setPoint($this->code, $card, $amount, $multi, $user, $guestname);
    return $point;
}


public function getCode():string{
    return $this->code;
}

public function getPw():string{
    return $this->pw;
}

public function getCreationdate():string{
    return $this->creationdate;
}

public function getGame():string{
    return $this->game;
}

public function getOwner():string{
    return $this->owner;
}
public function getStatus():string{
    return $this->status;
}

public function getGameName():string{
    return $this->gamename;
}

    public function reset(){
        $this->code         = Null;
        $this->pw           = Null;
        $this->creationdate = Null;
        $this->game         = Null;
        $this->owner        = Null;
        $this->status       = 0;
    }

    public function __tostring():string{
        return $this->code;
    }
}
