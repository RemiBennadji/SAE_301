<?php

// Requête(s) SQL

// Cette requête contient l'id, le type de séance, la salle associé, le nom du proffeseur, et l'intitulé de la matière
$infoSeance =
    "SELECT idseance, typeseance, duree, salle, collegue.prenom, collegue.nom, enseignement.court
    FROM seance
    JOIN collegue ON seance.collegue = collegue.id
    JOIN enseignement ON seance.code = enseignement.code
    ";

try {
    // Connection à la Base de données
    $connection = new PDO ("pgsql:host=iutinfo-sgbd.uphf.fr; dbname=edt user=iutinfo340 password=jWBfxD1E");
    $connection -> setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    $test = $connection -> prepare($infoSeance);
    $test -> execute();

    // Récuprer les valeurs de la requête
    $tabEntier = $test -> fetchAll(PDO::FETCH_ASSOC);

    // Afficher les valeurs pour chaque ligne
    foreach($tabEntier as $row){
        foreach($row as $key => $value){
            echo $key.": ".$value."<br>";
        }
        echo "<br>";
    }

} catch ( Exception $e ) {
    echo $e->getMessage();
}


