<?php

require 'ConnectionBDD.php';

session_start();

$conn = getConnectionBDD();

if($_SERVER["REQUEST_METHOD"] == "POST" and isset($_POST["code"])){
    $codeVerif = htmlspecialchars($_POST["code"]);
    $recupCode = "SELECT codev FROM codeverif WHERE codev = :code";
    $recupCode = $conn->prepare($recupCode);
    $recupCode->bindParam(':code', $codeVerif);
    $recupCode->execute();
    $recupCode = $recupCode->fetchAll(PDO::FETCH_ASSOC);
    if($recupCode[0]["codev"] !=''){
        if($codeVerif == $recupCode[0]["codev"]){
            $sup = "DELETE FROM codeverif WHERE codev = :code";
            $sup = $conn->prepare($sup);
            $sup->bindParam(':code', $codeVerif);
            $sup->execute();
            header("location:../View/HTML/changeMDP.html");
        }else{
            ECHO 'fail';
        }
    }else{
        ECHO 'big fail';
    }
}