<?php
//error_reporting(E_ALL);
//ini_set('display_errors', 1);

include "../Controller/ConnectionBDD.php";

//Recupération des informations lors de la connexion de l'utilisateur
if (!isset($_POST['id']) || !isset($_POST['pwd'])) {
    echo "Les informations de connexion ne sont pas fournies.";
    exit();
}

$ID = $_POST["id"];
$PWD = $_POST["pwd"];

//Requête SQL
$sql1 ="SELECT role FROM infoutilisateur WHERE identifiant=:ID AND motdepasse=:PWD";
$sql2 ="SELECT identifiant, motdepasse, changeMDP FROM infoutilisateur";

//Connexion à la base de donnée + lancement des requêtes SQL
try {
    $connection = getConnectionBDDEDTIdentification(); // Utilisation des informations de connexion du fichier ConnexionBDD.php

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
                if($result2[$i]['changemdp'] == false){
                    //TODO
                    header("location: ../View/HTML/ChangeMDP.php");
                }
                if ($result) {//si le role est bien recupérer alors on démarre la session et cookies
                    $role = $result['role'];

                    session_start();//Début session
                    if($role == "administrateur"){
                        $compte = new Administrateur();
                    }elseif ($role == "etudiant"){
                        $compte = new Etudiant();
                    }
                    $_SESSION['role'] = $role;
                    $_SESSION['ID'] = $ID;

                    setcookie("role", $role, time() + (60 * 15), "/");//Début cookie
                    setcookie("ID", $ID, time() + (60 * 15), "/");

                    if (isset($role)) {//si le role n'est pas vide alors on lance MenuPrincipal.php
                        header("location:../Controller/EDT.php");
                        exit();
                    }
                }
            }
        }
    } else {
        echo "Identifiant ou mot de passe incorrect.";
    }
    echo 'fail';

} catch (PDOException $e) {
    echo "Erreur : " . $e->getMessage();
    exit();
}