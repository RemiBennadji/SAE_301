<?php
include "../Controller/ConnectionBDD.php";

//Recupération des informations lors de la connexion de l'utilisateur
$ID = $_POST["id"];
$PWD = $_POST["pwd"];

//Requête SQL
$sql1 ="select role from infoutilisateur where identifiant=:ID and motdepasse=:PWD";
$sql2 ="select identifiant, motDePasse from infoutilisateur";

//Connexion à la base de donnée + lancement des requête SQL
try {
    $connection = getConnectionBDDEDTIdentification();//Utilisation des informations de connexion entrer dans le fichier ConnexionBDD.php

    $result = $connection->prepare($sql1);
    $result->bindParam(':ID', $ID);
    $result->bindParam(':PWD', $PWD);
    $result->execute();
    $result = $result->fetch(PDO::FETCH_ASSOC);

    $result2 = $connection->prepare($sql2);
    $result2->execute();
    $result2 = $result2->fetchall(PDO::FETCH_ASSOC);

    if (!empty($result2)) {
        $i = 0;
        for ($i = 0; $i < count($result2); $i++ ) {
            if ($result2[$i]['identifiant'] == $ID and $result2[$i]['motdepasse'] == $PWD) {
                if ($result) {//si le role est bien recupérer alors on démarre la session et cookies
                    $role = $result['role'];

                    session_start();//Début session
                    $_SESSION['role'] = $role;
                    $_SESSION['ID'] = $ID;

                    setcookie("role", $role, time() + (60 * 15), "/");//Début cookie
                    setcookie("ID", $ID, time() + (60 * 15), "/");

                    if (isset($role)) {//si le role n'est pas vide alors on lance MenuPrincipal.php
                        header("location:../Controller/MenuPrincipal.php");
                        exit();
                    }
                }
            }
        }
    }
    echo 'fail';

} catch (PDOException $e) {
    echo $e->getMessage();
}