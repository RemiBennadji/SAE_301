<?php
header('Content-Type: application/json');
session_start();

include "../Controller/ConnectionBDD.php";
include_once "../Model/Classe/Compte.php";
include_once "../Model/Classe/Administrateur.php";
include_once "../Model/Classe/Etudiant.php";

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

if(empty($_POST['mdp'] || empty($_SESSION['compte']))) {
    echo json_encode(['error'=>'errorConnexion']);
    exit();
}

$mdp = $_POST['mdp'];
$compte = $_SESSION['typeCompte'];

if($compte->verifMDP($mdp)){
    $compte->changeMDP($mdp);
    echo json_encode(['redirect' => '../../Controller/EDT.php']);
    exit();
}
echo json_encode(['error'=> 'errorConnexion']);
exit();