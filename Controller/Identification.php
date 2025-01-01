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
$sql1 ="SELECT identifiant, motdepasse, changeMDP, role, mail FROM infoutilisateur WHERE identifiant=:ID";
$sql2 ="select nom_ressource, semestre from etudiants where email=:EMAIL";


//Connexion à la BDD + lancement des requêtes SQL @Noah
try {
    $connection = getConnectionBDD();
    $connect = $connection->prepare($sql1);
    $connect->bindParam(':ID', $ID);
    $connect->execute();
    $connect = $connect->fetchAll(PDO::FETCH_ASSOC);


    //Attribution du role @Noah
    $role = $connect[0]['role'];

    if($role == "etudiant"){
        $mail = $connect[0]["mail"];
        $res = $connection->prepare($sql2);
        $res->bindParam(':EMAIL', $mail);
        $res->execute();
        $res = $res->fetchAll(PDO::FETCH_ASSOC);
        $annee = 0;
        $semestre = $res[0]["semestre"];
        if ($semestre==1 || $semestre==2){
            $annee = 1;
        } else if ($semestre==3 || $semestre==4){
            $annee = 2;
        } else if ($semestre==5 || $semestre==6){
            $annee = 3;
        }
        setcookie("groupe", $res[0]['nom_ressource'], time() + (60 * 15), "/");
        setcookie("annee", $annee, time() + (60 * 15), "/");
    }

    // Si l'utilisateur n'existe pas, cela renvoie une erreur au JS @Noah
    if(!$connect){
        echo json_encode(['error' => 'error requête']);
        exit();
    }

    $compte = null;

    //Tests pour déterminer quel type de compte créer @Noah
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
        //Recupération du nom du professeur
        $nomProf = "";
        $bool = false;
        for ($i = 0; $i < strlen($ID); $i++) {
            if($ID[$i] == "."){
                $bool = True;
            }
            else if($bool){
                $nomProf .= $ID[$i];
            }
        }
        setcookie("nomProf", $nomProf, time() + (60 * 15), "/");
    }


    //Définit l'identifiant du compte @Noah
    $compte->setIdentifiant($connect[0]['identifiant']);

    //Attribution des différentes variables dans la session @Noah
    $_SESSION['role'] = $role;
    $_SESSION['ID'] = $ID;
    $_SESSION['compte'] = $compte;

    //Début cookie
    setcookie("role", $role, time() + (60 * 15), "/");
    setcookie("ID", $ID, time() + (60 * 15), "/");
    setcookie("groupe", "A1", time() + (60 * 15), "/");
    setcookie("annee", 1, time() + (60 * 15), "/");


    //Vérification si c'est la première connexion @Noah
    if (!$connect[0]['changemdp']) {
        echo json_encode(['redirect' => '../../View/HTML/changeMDP.html']);
        exit();
    }

    //Vérification avec le hashage @Noah
//    echo json_encode($connect[0]['motdepasse']);
    if (password_verify($PWD,$connect[0]['motdepasse'])) {
        if($connect[0]['role'] == 'professeur'){
            echo json_encode(['redirect' => '../../Controller/EDTprof.php']); // Retourne la redirection
        }
        else{
            echo json_encode(['redirect' => '../../Controller/EDT.php']); // Retourne la redirection
        }
        exit();
    } else{
        echo json_encode(['error' => 'errorConnexion']);
    }

    } catch (PDOException $e) {
        echo json_encode(['error' => 'Erreur du serveur : ' . $e->getMessage()]);
        exit();
    }