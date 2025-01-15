<?php

require "ConnectionBDD.php";

session_start();

if($_SERVER["REQUEST_METHOD"] == "POST"){
    //Récupération des données du formulaire @Noah
    $date = $_POST["date"];
    $date = date("Y-m-d", $date);
    $heureDemande = $_POST["heure"];
    $raison = $_POST["sujet"];
    $type = $_POST["typeDemande"];
    $mail = $_SESSION["mail"];

    //Variable qui servira à afficher un message d'errreur ou de succès @Noah
    $message = "";

    //Requête pour récupérer nom et prénom du professeur @Noah
    $info = "SELECT nom, prenom FROM collegue WHERE mail =: MAIL";

    //Requête pour insérer la demande dans la BDD @Noah
    $sql = "INSERT INTO Demande VALUES(dateDemande=:DATEDEMANDE, raison=:RAISON, nom=:NOM, prenom=:PRENOM, heureDemande=:HEUREDEMANDE, typeDemande=:TYPEDEMANDE)";

    try{
        $conn = getConnectionBDD();

        //Requête préparée @Noah
        $getInfo = $conn->prepare($info);
        $getInfo->bindParam(":MAIL", $mail);
        $getInfo->execute();

        //Récupération des valeurs @Noah
        foreach($getInfo->fetchAll() as $value){
            $nom = $value["nom"];
            $prenom = $value["prenom"];
        }

        //Insertion des données @Noah
        $insertion = $conn->prepare($sql);
        $insertion->bindParam(":DATEDEMANDE", $date);
        $insertion->bindParam(":RAISON", $raison);
        $insertion->bindParam(":NOM", $nom);
        $insertion->bindParam(":PRENOM", $prenom);
        $insertion->bindParam(":HEUREDEMANDE", $heureDemande);
        $insertion->bindParam(":TYPEDEMANDE", $type);
        $insertion->execute();

        //Indique un message de succès @Noah
        $message = "Votre demande a été envoyée avec succès !";
        $typeMess= "success";



    }catch (Exception $e){
        //Indique un message d'erreur @Noah
        $message = $e->getMessage();
        $typeMess= "error";

    }
}
