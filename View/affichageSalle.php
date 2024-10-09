<?php
$jour = $_POST["idJour"];
$heure = $_POST["idHeure"];
$timestamp = strtotime($jour);

// On s'assure que la date est bien au format YYYY-MM-DD (ex : lundi)
$date = date("Y-m-d", $timestamp).' '.$heure.':00';

//$sql11 ="select role from infoutilisateur where identifiant=:ID and mdp=:PWD";
$sql1 ="select distinct salle from schedule where horaire = '$date'";

try {
    $connection = new PDO ("pgsql:host=iutinfo-sgbd.uphf.fr; dbname=edt user=iutinfo301 password=YAH+rfI3");
    $connection->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    $resultSalles = $connection->prepare($sql1);
    $resultSalles->execute();
    $listeSalles =$resultSalles->fetchAll(PDO::FETCH_ASSOC);
    foreach ($listeSalles as $salle) {
        echo $salle['salle'].'<br>';
    }

} catch (PDOException $e) {
    echo $e->getMessage();
}