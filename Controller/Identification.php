<?php

header('Content-Type: application/json');

require 'ConnectionBDD.php';
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


//Requete pour avoir la version max de l edt @Bastien

//Connexion à la BDD + lancement des requêtes SQL @Noah
try {
    //Test si identifiant existant dans la bdd @matthéo
    $connect = findUserBDD($ID); //$sql1 ="SELECT identifiant, motdepasse, changeMDP, role, mail FROM infoutilisateur WHERE identifiant=?"; @mattheo

    // Si l'utilisateur n'existe pas, cela renvoie une erreur au JS @Noah
    if(!$connect){
        echo json_encode(['error' => 'error identifiant']);
        exit();
    }

    $version = maxVersion(); //$sql = "select max(version) from versionValideEDT"; @mattheo

    //Attribution du role @Noah
    $role = $connect[0]['role'];

    setcookie("groupe", "A1", time() + (60 * 15 * 4 ), "/");
    if($role == "etudiant"){
        $mail = $connect[0]["mail"];

        $res = nomRessource($mail); //$sql2 ="select nom_ressource, semestre from etudiants where email=:EMAIL"; @mattheo
        $annee = 0;
        $semestre = $res[0]["semestre"];
        if ($semestre==1 || $semestre==2){
            $annee = 1;
        } else if ($semestre==3 || $semestre==4){
            $annee = 2;
        } else if ($semestre==5 || $semestre==6){
            $annee = 3;
        }
        setcookie("groupe", $res[0]['nom_ressource'], time() + (60 * 30 * 4 ), "/");
        setcookie("annee", $annee, time() + (60 * 15 * 4 ), "/");
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
        setcookie("nomProf", $nomProf, time() + (60 * 15 * 4 ), "/");
    }


    //Définit l'identifiant du compte @Noah
    $compte->setIdentifiant($connect[0]['identifiant']);

    //Attribution des différentes variables dans la session @Noah
    $_SESSION['role'] = $role;
    $_SESSION['ID'] = $ID;
    $_SESSION['compte'] = $compte;
    $_SESSION['mail'] = $connect[0]['mail'];

    //Début cookie
    setcookie("role", $role, time() + (60 * 15 * 4 ), "/");
    setcookie("ID", $ID, time() + (60 * 15 * 4 ), "/");
    setcookie("annee", 1, time() + (60 * 15 * 4 ), "/");
    setcookie("version", $version, time() + (60 * 15 * 4 ), "/");


    //Vérification si c'est la première connexion @Noah
    if (!$connect[0]['changemdp']) {
        echo json_encode(['redirect' => '../../View/Pages/changeMDP.html']);
        exit();
    }

    //Vérification avec le hashage @Noah
    if (password_verify($PWD,$connect[0]['motdepasse'])) {
        if($compte->getRole() == "professeur"){
            echo json_encode(['redirect' => '../../View/Pages/EDTprof.php']); // Retourne la redirection
        }
        else{
            echo json_encode(['redirect' => '../../View/Pages/EDT.php']); // Retourne la redirection
        }
        exit();
    } else{
        echo json_encode(['error' => 'errorMDP']);
    }

    } catch (PDOException $e) {
        echo json_encode(['error' => 'Erreur du serveur : ' . $e->getMessage()]);
        exit();
    }