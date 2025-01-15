<?php

require "ConnectionBDD.php";

session_start();

if($_SERVER["REQUEST_METHOD"] == "POST"){
    $date = $_POST["date"];
    $date = date("Y-m-d", $date);
    $heureDemande = $_POST["heure"];
    $raison = $_POST["sujet"];
    $type = $_POST["typeDemande"];
    $mail = $_SESSION["mail"];

    $message = "";

    $info = "SELECT nom, prenom FROM collegue WHERE mail =: MAIL";
    $sql = "INSERT INTO Demande VALUES(dateDemande=:DATEDEMANDE, raison=:RAISON, nom=:NOM, prenom=:PRENOM, heureDemande=:HEUREDEMANDE, typeDemande=:TYPEDEMANDE)";

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
        $insertion->bindParam(":DATEDEMANDE", $date);
        $insertion->bindParam(":RAISON", $raison);
        $insertion->bindParam(":NOM", $nom);
        $insertion->bindParam(":PRENOM", $prenom);
        $insertion->bindParam(":HEUREDEMANDE", $heureDemande);
        $insertion->bindParam(":TYPEDEMANDE", $type);
        $insertion->execute();

        $message = "Votre demande a été envoyée avec succès !";
        $typeMess= "success";



    }catch (Exception $e){
        $message = $e->getMessage();
        $typeMess= "error";

    }
}
