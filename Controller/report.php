<?php
session_start();

if($_SERVER["REQUEST_METHOD"] == "POST"){
    $date = $_POST["date"];
    $raison = $_POST["sujet"];
    $mail = $_SESSION["mail"];

    $sql = "INSERT INTO Report VALUES(dateReport=:DATEREPORT, raison=:RAISON, )";

    try{
        $connexion = get
    }catch{

    }
}
