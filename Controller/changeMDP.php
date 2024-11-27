<?php
session_start();
header('Content-Type: application/json');

include "../Controller/ConnectionBDD.php";
include "../Model/Classe/Compte.php";
include"../Model/Classe/Administrateur.php";
include "../Model/Classe/Etudiant.php";

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

if(empty($_POST['mdp'] || !isset($_SESSION['compte']))) {
    echo json_encode(['error'=>'errorConnexion']);
    exit();
}

$mdp = $_POST['mdp'];
$compte = $_SESSION['compte'];

if($compte->verifMDP($mdp)){
    $compte->changeMDP($mdp);
    echo json_encode(['redirect' => '../../Controller/EDT.php']);
    exit();
}

echo json_encode(['error'=> 'errorConnexion']);
exit();