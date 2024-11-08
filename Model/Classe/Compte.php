<?php
include_once "../../Controller/ConnectionBDD.php";
require_once "Administrateur.php";
require_once "Etudiant.php";
require_once "Secretariat.php";
require_once "Professeur.php";

abstract class Compte
{
    private $mdp;
    private $identifiant;
    private $role;
    private $nom;
    private $prenom;

    public function __construct($role, $nom, $prenom)
    {
        $this->role=$role;
        $this->nom=$nom;
        $this->prenom=$prenom;
        $this->identifiant= $this->genererIdentifiant();
        $this->mdp = password_hash($this->genererMDP(), PASSWORD_DEFAULT);
    }

    public function getMdp()
    {
        return $this->mdp;
    }

    public function insererDonnees()
    {
        $req = "INSERT INTO infoutilisateur VALUES(:identifiant, :motdepasse, :role)";

        try {
            $conn = getConnectionBDDEDTIdentification();

            $insert = $conn->prepare($req);
            $insert->bindParam(":identifiant", $this->identifiant);
            $insert->bindParam(":motdepasse", $this->mdp);
            $insert->bindParam(":role", $this->role);
            $insert->execute();


        } catch (PDOException $e) {
            echo $e->getMessage();
        }
    }

    public function verifMdp($mdp)
    {
        $caraSpec = array('!', '.', '€', '@','*');
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

    public function genererMDP(){
        // Définir le tableau de caractères pour le mot de passe
        $liste = array(
            range('a', 'z'), // Lettres minuscules
            range('A', 'Z'), // Lettres majuscules
            range(0, 9),     // Chiffres
            array('!', '#', '$', '*', '+', '-', '.', '/', ':', '?', '_') // Symboles spéciaux
        );
        $mdp = "";
        while (strlen($mdp) < 8) {
            // Ajouter un caractère au mot de passe à partir de chaque sous-tableau
            $mdp .= $liste[0][rand(0, count($liste[0]) - 1)]  // Lettre minuscule
                . $liste[1][rand(0, count($liste[1]) - 1)]  // Lettre majuscule
                . $liste[2][rand(0, count($liste[2]) - 1)]  // Chiffre
                . $liste[3][rand(0, count($liste[3]) - 1)]; // Symbole spécial
        }
        return $mdp;
    }

    public function genererIdentifiant(){
        $identifiant = strtolower($this->prenom) . '.' . strtolower($this->nom);
        return $identifiant;
    }


    public function setRole($r)
    {
        $this->role = $r;
    }
}