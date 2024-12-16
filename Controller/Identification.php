<?php

header('Content-Type: application/json');

include "../Controller/ConnectionBDD.php";
include_once "../Model/Classe/Compte.php";
include_once "../Model/Classe/Administrateur.php";
include_once "../Model/Classe/Etudiant.php";
include_once "../Model/Classe/Secretariat.php";
include_once "../Model/Classe/Professeur.php";

session_start();

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

//Vérification si les données ne sont pas vides @Noah
if (!isset($_POST['id']) || !isset($_POST['pwd'])) {
    exit();
}

//Récupération des informations entrées par l'utilisateur lors de sa connexion @Noah
$ID = $_POST["id"];
$PWD = $_POST["pwd"];

//Requête SQL permettant de retrouver l'utilisateur dans la BDD @Noah
$sql1 ="SELECT identifiant, motdepasse, changeMDP, role FROM infoutilisateur WHERE identifiant=:ID";


//Connexion à la BDD + lancement des requêtes SQL @Noah
try {
    $connection = getConnectionBDD();
    $result = $connection->prepare($sql1);
    $result->bindParam(':ID', $ID);
    $result->execute();
    $result = $result->fetchAll(PDO::FETCH_ASSOC);

    // Si l'utilisateur n'existe pas, cela renvoie une erreur au JS @Noah
    if(!$result){
        echo json_encode(['error' => 'errorConnexion']);
        exit();
    }

    //Attribution du role @Noah
    $role = $result[0]['role'];

    $compte = null;

    //Tests pour déterminer quel type de compte créer @Noah
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

    //Définit l'identifiant du compte @Noah
    $compte->setIdentifiant($result[0]['identifiant']);

    //Attribution des différentes variables dans la session @Noah
    $_SESSION['role'] = $role;
    $_SESSION['ID'] = $ID;
    $_SESSION['compte'] = $compte;

    //Début cookie
    setcookie("role", $role, time() + (60 * 15), "/");
    setcookie("ID", $ID, time() + (60 * 15), "/");

    //Vérification si c'est la première connexion @Noah
    if (!$result[0]['changemdp']) {
        echo json_encode(['redirect' => '../../View/HTML/changeMDP.html']);
        exit();
    }

    //Vérification avec le hashage @Noah
    if (password_verify($PWD,$result[0]['motdepasse'])) {
        echo json_encode(['redirect' => '../../Controller/EDT.php']); // Retourne la redirection
        exit();
    } else{
        echo json_encode(['error' => 'errorConnexion']);
    }

    } catch (PDOException $e) {
        echo json_encode(['error' => 'Erreur du serveur : ' . $e->getMessage()]);
        exit();
    }