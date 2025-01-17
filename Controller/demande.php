<?php

require "ConnectionBDD.php";

session_start();

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Récupération des données du formulaire
    $date = $_POST["dateReport"]; // Exemple : "2025-01-17"
    $heure = $_POST["heureReport"]; // Exemple : "14:30:00"
    $timestamp = date("Y-m-d H:i:s", strtotime("$date $heure"));
    $raison = $_POST["sujet"];
    $type = $_POST["typeDemande"];
    $mail = strtolower($_SESSION["mail"]);


    // Requête pour récupérer nom et prénom du professeur
    $info = "SELECT nom, prenom FROM collegue WHERE mail = :MAIL";

    // Requête pour insérer la demande dans la BDD
    $sql = "INSERT INTO Demande(dateDemande, raison, nom, prenom, typeDemande) 
            VALUES(:DATEDEMANDE, :RAISON, :NOM, :PRENOM, :TYPEDEMANDE)";

    try {
        $conn = getConnectionBDD();

        // Récupérer les informations du professeur
        $getInfo = $conn->prepare($info);
        $getInfo->bindParam(":MAIL", $mail);
        $getInfo->execute();

        $res = $getInfo->fetch(PDO::FETCH_ASSOC);
        if ($res) {
            $nom = $res["nom"];
            $prenom = $res["prenom"];
        }

        // Insérer les données dans la table Demande
        $insertion = $conn->prepare($sql);
        $insertion->bindParam(":DATEDEMANDE", $timestamp);
        $insertion->bindParam(":RAISON", $raison);
        $insertion->bindParam(":NOM", $nom);
        $insertion->bindParam(":PRENOM", $prenom);
        $insertion->bindParam(":TYPEDEMANDE", $type);
        $insertion->execute();

        header('Location: EDTprof.php');

    } catch (Exception $e) {
        // Gestion des erreurs
        $message = $e->getMessage();
    }

}
?>
