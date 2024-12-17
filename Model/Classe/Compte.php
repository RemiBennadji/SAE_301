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
    
    private $mail;

    public function __construct($role)
    {
        $this->role=$role;
        $this->mdp = $this->genererMDP();
    }

    public function getMdp()
    {
        return $this->mdp;
    }

    //Fonction permettant le hashage et l'insertion du mot de passe du compte dans la BDD @Noah
    public function changeMdp($mdp){
        //Vérification des critères @Noah
        if ($this->verifMdp($mdp)){
            //Hashage du mdp @Noah
            $mdp = password_hash($mdp, PASSWORD_DEFAULT);
            //Met à jour le mot de passe et dit à la BDD que la première connexion a été faite @Noah
            $change = "UPDATE infoutilisateur SET motdepasse = :motdepasse, changemdp = true WHERE identifiant = :identifiant;";
            try {
                $conn = getConnectionBDDEDTIdentification();

                $insertion = $conn->prepare($change);
                $insertion->bindParam(":motdepasse", $mdp);
                $insertion->bindParam(":identifiant", $this->identifiant);
                $insertion->execute();

                if ($insertion->rowCount() === 0) {
                    echo json_encode(['error' => 'Aucune donnée mise à jour.']);
                    exit();
                }

            } catch (PDOException $e) {
                echo json_encode(['error' => $e->getMessage()]);
            }
        }
    }

    public function insererDonnees()
    {
//        $req1 ="SELECT etudiants.email from etudiants where nom=$this->nom";
        $req2 = "INSERT INTO infoutilisateur VALUES(:identifiant, :motdepasse, :role, false, :mail)";


        try {
            $conn = getConnectionBDDEDTIdentification();
            $this->identifiant= $this->genererIdentifiant();

            $insert = $conn->prepare($req2);
            $insert->bindParam(":identifiant", $this->identifiant);
            $insert->bindParam(":motdepasse", $this->mdp);
            $insert->bindParam(":role", $this->role);
            $insert->bindParam(":mail", $this->mail);
            $insert->execute();
        } catch (PDOException $e) {
            echo $e->getMessage();
        }
    }

    public function verifMdp($mdp)
    {
        $caraSpec = array('!', '#', '$', '*', '+', '-', '.', '/', ':', '?', '_');
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

    public function genererIdentifiant()
    {
        $identifiant = strtolower($this->prenom) . '.' . strtolower($this->nom);
        $id = "";
        foreach (str_split($identifiant) as $element) {
            $id = ($element == " ") ? $id .= "-" : $id .= $element;
        }
        return $id;
    }

    public function setIdentifiant($id){
        $this->identifiant = $id;
    }

    public function setRole($r)
    {
        $this->role = $r;
    }
    public function setPrenom($prenom){
        $this->prenom = $prenom;
    }

    public function setMDP($mdp){
        if($this->verifMdp($mdp)){
            $this->mdp = $mdp;
        }
        else{
            json_encode(['error' => 'Mot de passe incorrect.']);
        }
}
    public function setNom($nom){
        $this->nom = $nom;
    }
    public function getMail()
    {
        return $this->mail;
    }

    public function setMail($mail)
    {
        $this->mail = $mail;
    }
}