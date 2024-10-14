<?php
//récupération des données du formulaire @Noah
$jour = $_POST["idJour"];
$heure = $_POST["idHeure"];

//convertion de la date au format timestamp @Noah
$timestamp = strtotime($jour);
$date = date("Y-m-d", $timestamp).' '.$heure.':00';

//requête permettant d'accéder aux salles utilisées à l'horaire saisi @Noah
$sql1 ="select distinct salle from schedule where horaire = '$date'";

//requête qui permet d'avoir toutes les salles @Noah
$sql2 ="select distinct nosalle from listesalles";

//liste qui va stocker les salles utilisées @Noah
$sallesAll = array();

//connexion BDD @Noah
try {
    $connection = new PDO ("pgsql:host=iutinfo-sgbd.uphf.fr; dbname=edt user=iutinfo301 password=YAH+rfI3");
    $connection->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    //execution de la requête 1 et ajoute les salles à la liste sallesAll @Noah
    $resultSalles = $connection->prepare($sql1);
    $resultSalles->execute();
    $listeSalles =$resultSalles->fetchAll(PDO::FETCH_ASSOC);
    foreach ($listeSalles as $salle) {
        $sallesAll[] = $salle['salle'];
    }

    //execution de la requête 2 et affiche les salles grâce à une comparaison avec sallesAll @Noah
    $salles = $connection->prepare($sql2);
    $salles->execute();
    $sallesDispo = $salles->fetchAll(PDO::FETCH_ASSOC);
    foreach ($sallesDispo as $nosalle) {
        if(!in_array($nosalle['nosalle'], $sallesAll)){
            echo $nosalle['nosalle'].'<br>';
        }
    }

} catch (PDOException $e) {
    echo $e->getMessage();
}
?>