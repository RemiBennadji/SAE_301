<?php

$mail = $_POST["email"];


$sql1="select identifiant, motdepasse from mailidentifiant JOIN infoutilisateur USING(identifiant) where mail=:mail";

try {
    $conn = getConnectionBDDEDTIdentification();
    $res1 = $conn->prepare($sql1);
    $res1->bindParam(":mail", $mail);
    $res1->execute();

} catch (PDOException $e) {
    $erreur = "Erreur SQL : " . $e->getMessage();
}
?>