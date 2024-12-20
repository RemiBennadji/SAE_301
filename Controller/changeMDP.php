<?php
header('Content-Type: application/json');

include "ConnectionBDD.php";
require_once "../Model/Classe/Compte.php";
require_once "../Model/Classe/Administrateur.php";
require_once "../Model/Classe/Etudiant.php";

session_start();

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

//Vérification des données si vides @Noah
if(empty($_POST['mdp'])) {
    echo json_encode(['error'=>'errorConnexion']);
    exit();
}

if(empty($_SESSION['ID']) && empty($_SESSION['from'])) {
    echo json_encode(['error'=>'sessionExpired', 'redirect'=>'../../View/HTML/Identification.html']);
    exit();
}
$conn = getConnectionBDD();

if($_SESSION[('from')]){
    $crea = "SELECT mail, identifiant, role FROM infoutilisateur WHERE mail=:mail";
    $recupMail = "SELECT email FROM codeverif WHERE codev =:code";

    try{
//        $recup = $conn->prepare($recupMail);
//        $recup->bindParam('code', $_SESSION['code']);
//        $recup->execute();
//        $recup = $recup->fetchAll(PDO::FETCH_ASSOC);

        $connect = $conn->prepare($crea);
        $connect->bindParam(':mail', $_SESSION['mail']);
        $connect->execute();
        $connect = $connect->fetchAll(PDO::FETCH_ASSOC);

        if (!$connect) {
            echo json_encode(['error' => $_SESSION['mail']]);
            exit();
        }

        if($connect[0]['role'] == 'etudiant'){
            $compte = new Etudiant();
        }
        else if($connect[0]['role'] == 'administrateur'){
            $compte = new Administrateur();
        }
        else if($connect[0]['role'] == 'secretariat'){
            $compte = new Secretariat();
        }
        else if($connect[0]['role'] == 'professeur'){
            $compte = new Professeur();
        }
        $compte->setMail($_SESSION['mail']);
        $compte->setIdentifiant($connect[0]['identifiant']);
        $compte->setRole($connect[0]['role']);
    }catch (Exception $e){
        json_encode(['error'=>$e->getMessage()]);
    }
}else{
    $compte = $_SESSION['compte'];

}
//Récupération des données du formulaire @Noah
$mdp = $_POST['mdp'];
$mdpverify = $_POST['mdpverify'];

//Test si le mot de passe entré est identique au second @Noah
if($mdp == $mdpverify) {
    //Défini le mot de passe du compte @Noah
    $compte->setMDP($mdp);
    $compte->changeMdp($mdp);

    //Vérifie si le mot de passe correspond bien aux critères @Noah
    if($compte->verifMdp($mdp)){
        if($_SESSION['from']){
            echo json_encode(['success'=>'ok', 'redirect'=>'../../View/HTML/Identification.html']);
            exit();
        }else {
            echo json_encode(['redirect' => '../../Controller/EDT.php']);
            exit();
        }
    }
    echo json_encode(['error'=>'errorConnexion']);
    exit();
}else{
    //Si le mot de passe n'est pas égal alors il passe le champ de saisie en rouge @Noah
    echo json_encode(['error'=>'errorConnexion']);
    exit();
}