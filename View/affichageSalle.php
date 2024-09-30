<?php
$jour = $_GET["jour"];
$heure = $_GET["heure"];

//$sql11 ="select role from infoutilisateur where identifiant=:ID and mdp=:PWD";
$sql1 ="select nosalle from listesalles";
$sql2 ="select ";

try {
    $connection = new PDO ("pgsql:host=iutinfo-sgbd.uphf.fr; dbname=edt user=iutinfo301 password=YAH+rfI3");
    $connection->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    $resultSalles = $connection->prepare($sql1);
    $resultSalles->execute();
    $listeSalles =$resultSalles->fetch(PDO::FETCH_ASSOC);


    $resultSallesPrises = $connection->prepare($sql2);
    $resultSallesPrises->execute();
    $listeSallesPrises =$resultSallesPrises->fetch(PDO::FETCH_ASSOC);

    echo "$listeSalles";

} catch (PDOException $e) {
    echo $e->getMessage();
}



//try {
//    $connection = new PDO ("pgsql:host=iutinfo-sgbd.uphf.fr; dbname=edt user=iutinfo301 password=YAH+rfI3");
//    $connection->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
//
//    $resultSallesPrises = $connection->prepare($sql2);
//    $resultSallesPrises->execute();
//    $listeSallesPrises =$resultSallesPrises->fetch(PDO::FETCH_ASSOC);
//
//} catch (PDOException $e) {
//    echo $e->getMessage();
//}

foreach ($listeSalles as $salle) {
    if (in_array($salle, $ListeSallesPrises)) {
}