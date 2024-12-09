<?php

$id = $_POST["identifiant"];
$pwd1 = $_POST['mdp'];
$pwd2 = $_POST['mdp2'];

$sql1="select identifiant, motdepasse from infoutilisateur where identifiant=:identifiant";

try {
    $conn = getConnectionBDDEDTIdentification();
    $res1 = $conn->prepare($sql1);
    $res1->bindParam(":identifiant", $id);
    $res1->execute();
    if ($pwd1 == $pwd2) {
        if ($pwd1 != $res1[0]['motdepasse']) {

        }

    }

} catch (PDOException $e) {
    $erreur = "Erreur SQL : " . $e->getMessage();
}
?>