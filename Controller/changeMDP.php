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


if(empty($_POST['mdp'] || !isset($_SESSION['compte']))) {
    echo json_encode(['error'=>'errorConnexion']);
    exit();
}

$mdp = $_POST['mdp'];
$compte = $_SESSION['compte'];

// Vérifiez que l'objet est valide
if (!$compte instanceof Compte) {
    echo json_encode(['error' => 'L\'objet compte n\'est pas valide.']);
    exit();
}

// Vérifiez que la méthode existe
if (!method_exists($compte, 'changeMdp')) {
    echo json_encode(['error' => 'La méthode changeMdp n\'existe pas pour cet objet.']);
    exit();
}
$compte->setMDP($mdp);
$compte->changeMdp($mdp);
if($compte->verifMdp($mdp)){
    echo json_encode(['redirect'=>'../../Controller/EDT.php']);
    exit();
}
echo json_encode(['error'=>'errorConnexion']);
exit();

//
//if($compte->verifMDP($mdp)){
//    $compte->changeMdp($mdp);
//    echo json_encode(['redirect' => '../../Controller/EDT.php']);
//    exit();
//}
//
//echo json_encode(['error'=> 'errorConnexion']);
//exit();