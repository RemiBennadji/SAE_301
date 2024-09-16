<?php

abstract class Compte
{
private $id;
private $mdp;

public function __construct(){
    $this->id = "None";
    $this->mdp = "";
}
public function getId(){
    return $this->id;
}
public function getMdp(){
    return $this->mdp;
}
public function setMdp($mdp){
    $caraSpec = array('!', '.', '€', '@');
    $chiffre = array('0', '1', '2', '3', '4', '5', '6', '7', '8', '9');
    $sec = false;
    $nbrCara = false;
    $nbrChiffre = 0;
    if(count($mdp) >= 8){
        $nbrCara = true;
    }
    foreach ($caraSpec as $cara) {
        if(in_array($cara, $chiffre)){
            $nbrChiffre++;
        }
        if (str_contains($mdp, $cara) == false) {
            continue;
        }
        else{
            $sec = true;
        }
    }
    if($nbrCara == true && $nbrChiffre >= 4 && $sec == true){
        $this->mdp = $mdp;
    }
    else{
        echo "Il y a une condition qui n'est pas rempli, veuillez revérifier votre mot de passe.";
    }

}

}