<?php

header('Content-Type: application/json');

include "../Controller/ConnectionBDD.php";
include_once "../Model/Classe/Compte.php";
include_once "../Model/Classe/Administrateur.php";
include_once "../Model/Classe/Etudiant.php";
include_once "../Model/Classe/Secretariat.php";
include_once "../Model/Classe/Professeur.php";
session_start();
//echo "Étape 1 : Script démarré<br>";

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

//Recupération des informations lors de la connexion de l'utilisateur
if (!isset($_POST['id']) || !isset($_POST['pwd'])) {
    exit();
}

$ID = $_POST["id"];
$PWD = $_POST["pwd"];

//Requête SQL
$sql1 ="SELECT identifiant, motdepasse, changeMDP, role FROM infoutilisateur WHERE identifiant=:ID AND motdepasse=:PWD";


//Connexion à la base de donnée + lancement des requêtes SQL
try {
    $connection = getConnectionBDDEDTIdentification(); // Utilisation des informations de connexion du fichier ConnexionBDD.php
    $result = $connection->prepare($sql1);
    $result->bindParam(':ID', $ID);
    $result->bindParam(':PWD', $PWD);
    $result->execute();
    $result = $result->fetchAll(PDO::FETCH_ASSOC);

     //si le role est bien recupérer alors on démarre la session et cookies
    $role = $result[0]['role'];
    $compte = null;

    //tests pour déterminer quel type de compte créer
    if($result[0]['role'] == 'etudiant'){
        $compte = new Etudiant();
    }
    else if($result[0]['role'] == 'administrateur'){
        $compte = new Administrateur();
    }
    else if($result[0]['role'] == 'secretariat'){
        $compte = new Secretariat();
    }
    else if($result[0]['role'] == 'professeur'){
        $compte = new Professeur();
    }
    $compte->setIdentifiant($result[0]['identifiant']);
    //Début session
    $_SESSION['role'] = $role;
    $_SESSION['ID'] = $ID;
    $_SESSION['compte'] = $compte;

    setcookie("role", $role, time() + (60 * 15), "/");//Début cookie
    setcookie("ID", $ID, time() + (60 * 15), "/");

    if (!$result[0]['changemdp']) {
        echo json_encode(['redirect' => '../../View/HTML/changeMDP.html']); // Retourne la redirection
        exit(); // Stoppe le script PHP
    }

    echo json_encode(['redirect' => '../../Controller/EDT.php']); // Retourne la redirection
    exit();

    } catch (PDOException $e) {
        echo json_encode(['error' => 'Erreur du serveur : ' . $e->getMessage()]);
        exit();
    }