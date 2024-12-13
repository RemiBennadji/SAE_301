<?php
header('Content-Type: application/json');

include "../Controller/ConnectionBDD.php";
require_once "../Model/Classe/Compte.php";
require_once "../Model/Classe/Administrateur.php";
require_once "../Model/Classe/Etudiant.php";

session_start();

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

//Vérification des données si vides @Noah
if(empty($_POST['mdp'] || !isset($_SESSION['compte']))) {
    echo json_encode(['error'=>'errorConnexion']);
    exit();
}

//Récupération des données du formulaire @Noah
$mdp = $_POST['mdp'];
$mdpverify = $_POST['mdpverify'];
$compte = $_SESSION['compte'];

//Test si le mot de passe entré est identique au second @Noah
if($mdp == $mdpverify) {
    //Défini le mot de passe du compte @Noah
    $compte->setMDP($mdp);
    $compte->changeMdp($mdp);

    //Vérifie si le mot de passe correspond bien aux critères @Noah
    if($compte->verifMdp($mdp)){
        echo json_encode(['redirect'=>'../../Controller/EDT.php']);
        exit();
    }
    echo json_encode(['error'=>'errorConnexion']);
    exit();
}else{
    //Si le mot de passe n'est pas égal alors il passe le champ de saisie en rouge @Noah
    echo json_encode(['error'=>'errorConnexion']);
    exit();
}