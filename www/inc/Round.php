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
    
    public function __construct( string $game="", string $pw="", string $code=""){
        $this->reset();
        if ($code!=""){
            // Si la partie existe
            // À compléter
            $this->code=$code;
        }
        elseif($pw!="" && $game!=""){
            // Créer une nouvelle partie
            $this->newRound($game, $pw);
        }
        else{
            die("La partie ne peut pas être chargée, paramètres inappropriés");
        }
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
                die("Impossible de créer une nouvelle partie");
            }
        }
        $this->code=$code;
    }

    public function loadRound(string $code):void{
        $db=new Database();
        $round=$db->getRound($code);
        if (!empty($round)){
            $this->code         = $round['code'];
            $this->pw           = $round['pw'];
            $this->creationdate = $round['creationdate'];
            $this->game         = $round['game'];
            $this->owner        = $round['owner'];
            $this->status       = 1;
        }
    }

    public function reset(){
        $this->code         = Null;
        $this->pw           = Null;
        $this->creationdate = Null;
        $this->game         = Null;
        $this->owner        = Null;
        $this->status       = Null;
    }

    public function __tostring():string{
        return $this->code;
    }
    
}
