<?php

require "ConnectionBDD.php";

session_start();

if($_SERVER["REQUEST_METHOD"] == "POST"){
    $date = $_POST["date"];
    $date = date("Y-m-d", $date);
    $heureReport = $_POST["heure"];
    $raison = $_POST["sujet"];
    $mail = $_SESSION["mail"];

    $message = "";

    $info = "SELECT nom, prenom FROM collegue WHERE mail =: MAIL";
    $sql = "INSERT INTO Report VALUES(dateReport=:DATEREPORT, raison=:RAISON, nom=:NOM, prenom=:PRENOM, heureReport=:HEUREREPORT)";

    try{
        $conn = getConnectionBDD();

        $getInfo = $conn->prepare($info);
        $getInfo->bindParam(":MAIL", $mail);
        $getInfo->execute();
        foreach($getInfo->fetchAll() as $value){
            $nom = $value["nom"];
            $prenom = $value["prenom"];
        }

        $insertion = $conn->prepare($sql);
        $insertion->bindParam(":DATEREPORT", $date);
        $insertion->bindParam(":RAISON", $raison);
        $insertion->bindParam(":NOM", $nom);
        $insertion->bindParam(":PRENOM", $prenom);
        $insertion->bindParam(":HEUREREPORT", $heureReport);
        $insertion->execute();

        $message = "Votre demande a été envoyée avec succès !";
        $typeMess= "success";



    }catch (Exception $e){
        $message = $e->getMessage();
        $typeMess= "error";

    }
}
