<?php

abstract class Compte
{
  
private $id;
private $mdp;
private $nom;
private $prenom;

public function __construct(){
    $this->id = "";
    $this->mdp = "";
}
public function getId(){
    return $this->id;
}
public function getMdp(){
    return $this->mdp;
}
public function verifMdp($mdp)
{
    $caraSpec = array('!', '.', '€', '@');
    $chiffre = array('0', '1', '2', '3', '4', '5', '6', '7', '8', '9');
    $sec = false;
    $nbrCara = false;
    $nbrChiffre = 0;
    if (strlen($mdp) >= 8) {
        $nbrCara = true;
    }
    for ($i = 0; $i < strlen($mdp); $i++) {
        $char = $mdp[$i];

        // Vérification des chiffres
        if (is_numeric($char)) {
            $nbrChiffre++;
        }

        // Vérification des caractères spéciaux
        if (in_array($char, $caraSpec)) {
            $sec = true;
        }
    }
    if ($nbrCara && $nbrChiffre >= 1 && $sec) {
        return true;
    } else {
        return false;
    }

}

public function setMdp($mdp){
    if($this->verifMdp($mdp)){
        $this->mdp = $mdp;
    }
    else{
        echo "Il y a une condition qui n'est pas rempli, veuillez revérifier votre mot de passe.";
    }
}

public function genererIdentifiant(){
    $identifiant = strtolower($this->prenom).'.'.strtolower($this->nom);
    return $identifiant;
}
public function setNom($nom){
    $this->nom = $nom;
}
public function setPrenom($prenom){
    $this->prenom = $prenom;
}
public function getNom(){
    return $this->nom;
}

public function getPrenom(){
    return $this->prenom;
}
}
