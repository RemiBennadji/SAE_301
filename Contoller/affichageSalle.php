<?php
$jour = $_POST["idJour"];
$heure = $_POST["idHeure"];
$timestamp = strtotime($jour);
$horaire = array('8:00', '9:30', '11:00', '12:30', '14:00', '15:30', '17:00');
// On s'assure que la date est bien au format YYYY-MM-DD (ex : lundi)
$date = date("Y-m-d", $timestamp).' '.$heure.':00';

//for($i=0; $i < count($horaire); $i++) {
//    if($horaire[$i]!=0){
//        if($horaire[$i]==$heure){
//            $heureInf = $horaire[$i-1];
//            $dateInf = date("Y-m-d", $timestamp).' '.$heureInf.':00';
//        }
//    }
//}



$sql1 ="select distinct salle from schedule where horaire = '$date'";
$sql2 ="select distinct nosalle from listesalles";
$sallesAll = array();

try {
    $connection = new PDO ("pgsql:host=iutinfo-sgbd.uphf.fr; dbname=edt user=iutinfo301 password=YAH+rfI3");
    $connection->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    $resultSalles = $connection->prepare($sql1);
    $resultSalles->execute();
    $listeSalles =$resultSalles->fetchAll(PDO::FETCH_ASSOC);
    foreach ($listeSalles as $salle) {
        $sallesAll[] = $salle['salle'];
    }
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