<?php

header('Content-Type: application/json');

include "../Controller/ConnectionBDD.php";
include_once "../Model/Classe/Compte.php";
include_once "../Model/Classe/Administrateur.php";
include_once "../Model/Classe/Etudiant.php";
include_once "../Model/Classe/Secretariat.php";
include_once "../Model/Classe/Professeur.php";

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
$sql1 ="SELECT role FROM infoutilisateur WHERE identifiant=:ID AND motdepasse=:PWD";
$sql2 ="SELECT identifiant, motdepasse, changeMDP, role FROM infoutilisateur";

//Connexion à la base de donnée + lancement des requêtes SQL
try {
    $connection = getConnectionBDDEDTIdentification(); // Utilisation des informations de connexion du fichier ConnexionBDD.php
//    echo "Étape 2 : Connexion BDD réussie<br>";
    $result = $connection->prepare($sql1);
    $result->bindParam(':ID', $ID);
    $result->bindParam(':PWD', $PWD);
    $result->execute();
    $result = $result->fetch(PDO::FETCH_ASSOC);

    $result2 = $connection->prepare($sql2);
    $result2->execute();
    $result2 = $result2->fetchAll(PDO::FETCH_ASSOC);

    if (!empty($result2)) {
        $i = 0;
        for ($i = 0; $i < count($result2); $i++ ) {
            if (($result2[$i]['identifiant'] == $ID) and ($result2[$i]['motdepasse'] == $PWD)) {
                if (!$result2[$i]['changemdp']) {
                    echo json_encode(['redirect' => '../../View/HTML/changeMDP.html']); // Retourne la redirection
                    exit(); // Stoppe le script PHP
                }
                echo $result2[$i]['role'];
                if (!empty($result['role'])) {//si le role est bien recupérer alors on démarre la session et cookies
                    $role = $ID;
                    $compte = null;

                    //tests pour déterminer quel type de compte créer
//                    if($result['role'] == 'etudiant'){
//                        $compte = new Etudiant();
//                    }
//                    else if($result['role'] == 'administrateur'){
//                        $compte = new Administrateur();
//                    }
//                    else if($result['role'] == 'secretariat'){
//                        $compte = new Secretariat();
//                    }
//                    else if($result['role'] == 'professeur'){
//                        $compte = new Professeur();
//                    }

                    session_start();//Début session
                    $_SESSION['role'] = $role;
                    $_SESSION['ID'] = $ID;
//                    $_SESSION['compte'] = $compte;

                    setcookie("role", $role, time() + (60 * 15), "/");//Début cookie
                    setcookie("ID", $ID, time() + (60 * 15), "/");

                    if ($role) {
                        echo json_encode(['redirect' => '../../Controller/EDT.php']); // Retourne la redirection
                        exit();
                    }
//                    if (isset($role)) {//si le role n'est pas vide alors on lance MenuPrincipal.php
//                        header("Location: ../../Controller/EDT.php");
//                        exit();
//                    }
                }
            }
            echo json_encode(['error' => 'errorConnexion']);
            exit();
        }
    } else {
//        echo "Identifiant ou mot de passe incorrect.";
    }
//    echo 'fail';

} catch (PDOException $e) {
    echo json_encode(['error' => 'Erreur du serveur : ' . $e->getMessage()]);
    exit();
}