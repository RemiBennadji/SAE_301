<?php
include "../Model/ConnectionBDD.php";

abstract class Compte
{
    private $id;
    private $mdp;
    private $nom;
    private $prenom;
    private $identifiant;
    private $role;

    public function __construct()
    {
        $this->id = "";
        $this->mdp = "";
    }

    public function getId()
    {
        return $this->id;
    }

    public function getMdp()
    {
        return $this->mdp;
    }

    public function insererDonnees()
    {
        $req = "INSERT INTO infoutilisateur VALUES('$this->identifiant', '$this->nom','$this->prenom' ,'$this->role', '$this->mdp')";

        try {
            $conn = getConnectionBDDEDTIdentification();

            $insert = $conn->prepare($req);
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

    public function setMdp($mdp)
    {
        if ($this->verifMdp($mdp)) {
            $this->mdp = $mdp;
        } else {
            echo "Il y a une condition qui n'est pas rempli, veuillez revérifier votre mot de passe.";
        }
    }

    public function genererMDP(){
        $liste = array(
            array(range('a', 'z')),
            array(range('A', 'Z')),
            array(implode('', range(0, 9))),
            array('!', '#', '$','*', '+', '-', '.', '/', ':','?','_')
        );
        $mdp = "";
        while (strlen($mdp) < 8) {
            $mdp .= $liste[0][rand(0, strlen($liste[0])-1)].$liste[1][rand(0, strlen($liste[1])-1)].$liste[2][rand(0,strlen($liste[2])-1)].$liste[3][rand(0, strlen($liste[3])-1)];
        }
        return $mdp;
    }

    public function genererIdentifiant()
    {
        $uniqueId = 0;
        $allId = array();
        $req = "SELECT identifiant FROM infoutilisateur";
        try {
            $conn = getConnectionBDDEDTIdentification();

            $identifiants = $conn->query($req);
            while ($row = $identifiants->fetch()) {
                $allId[] = $row['identifiant'];
            }
        } catch (PDOException $e) {
            echo $e->getMessage();
        }
        $identifiant = strtolower($this->prenom) . '.' . strtolower($this->nom);
        while (in_array($identifiant, $allId)) {
            $uniqueId++;
            $identifiant = strtolower($this->prenom) . '.' . strtolower($this->nom) . $uniqueId;
        }
        $this->identifiant = $identifiant;
        return $this->identifiant;
    }

    public
    function setNom($nom)
    {
        $this->nom = $nom;
    }

    public
    function setPrenom($prenom)
    {
        $this->prenom = $prenom;
    }

    public
    function getNom()
    {
        return $this->nom;
    }

    public
    function getPrenom()
    {
        return $this->prenom;
    }

    public function setRole($r)
    {
        $this->role = $r;
    }
}
