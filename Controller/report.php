<?php

require "ConnectionBDD.php";

session_start();

if($_SERVER["REQUEST_METHOD"] == "POST"){
    $date = $_POST["date"];
    $raison = $_POST["sujet"];
    $mail = $_SESSION["mail"];

    $message = "";

    $info = "SELECT ";
    $sql = "INSERT INTO Report VALUES(dateReport=:DATEREPORT, raison=:RAISON, nom:=NOM, prenom:=PRENOM)";

    try{
        $conn = getConnectionBDD();
        $insertion = $conn->prepare($sql);
        $insertion->bindParam(":DATEREPORT", $date);
        $insertion->bindParam(":RAISON", $raison);
        $insertion->bindParam(":MAIL", $mail);
        $insertion->execute();

        $message = "Votre demande a été envoyée avec succès !";
        $typeMess= "success";



    }catch (Exception $e){
        $message = $e->getMessage();
        $typeMess= "error";

    }
}
