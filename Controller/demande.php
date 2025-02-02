<?php

require "ConnectionBDD.php";

session_start();

if (isset($_SESSION['role'])) {
    if($_COOKIE['role'] != 'professeur'){
        header("Location: ./Deconnexion.php"); // Redirection si pas de rôle
        exit();
    }
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Récupération des données du formulaire
    $date = $_POST["dateReport"]; // Exemple : "2025-01-17"
    $heure = $_POST["heureReport"]; // Exemple : "14:30:00"
    $timestamp = date("Y-m-d H:i:s", strtotime("$date $heure"));
    $raison = $_POST["sujet"];
    $type = $_POST["typeDemande"];
    $mail = strtolower($_SESSION["mail"]);


    // Requête pour récupérer nom et prénom du professeur
    //$info = "SELECT nom, prenom FROM collegue WHERE mail = :MAIL";

    // Requête pour insérer la demande dans la BDD
//    $sql = "INSERT INTO demande(dateDemande, raison, nom, prenom, typeDemande)
//            VALUES(:DATEDEMANDE, :RAISON, :NOM, :PRENOM, :TYPEDEMANDE)";

    try {
        //$conn = getConnectionBDD();

        // Récupérer les informations du professeur
//        $getInfo = $conn->prepare($info);
//        $getInfo->bindParam(":MAIL", $mail);
//        $getInfo->execute();

        //$res = $getInfo->fetch(PDO::FETCH_ASSOC);
        $res = recupNomPrenomProf($mail);
        if ($res) {
            $nom = $res[0]["nom"];
            $prenom = $res[0]["prenom"];
            echo $nom . " " . $prenom;
        }

        // Insérer les données dans la table Demande
//        $insertion = $conn->prepare($sql);
//        $insertion->bindParam(":DATEDEMANDE", $timestamp);
//        $insertion->bindParam(":RAISON", $raison);
//        $insertion->bindParam(":NOM", $nom);
//        $insertion->bindParam(":PRENOM", $prenom);
//        $insertion->bindParam(":TYPEDEMANDE", $type);
//        $insertion->execute();
        insertDemande($timestamp,$raison,$nom,$prenom,$type);

        header('Location: ../View/Pages/EDTprof.php');

    } catch (Exception $e) {
        // Gestion des erreurs
        echo $e->getMessage();
    }

}
?>
