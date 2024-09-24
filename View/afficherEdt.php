<?php

function AfficherCoursClasseHeure($heure, $classe){
    $sql = "
    SELECT DISTINCT seance.idseance, seance.typeseance, duree, schedule.salle, collegue.prenom, collegue.nom, enseignement.court as matiere, horaire as date, schedule.nomgroupe
    FROM seance
    JOIN collegue ON seance.collegue = collegue.id
    JOIN enseignement ON seance.code = enseignement.code
    JOIN schedule ON seance.nomgroupe = schedule.nomgroupe
    WHERE horaire = ? AND schedule.nomgroupe=?";

    // Connexion
    $connexion = new PDO ("pgsql:host=iutinfo-sgbd.uphf.fr; dbname=edt user=iutinfo340 password=jWBfxD1E");
    $connexion->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    // Préparation de la requête avec les arguments
    $req = $connexion->prepare($sql);
    $req -> execute(array($heure,$classe));
    $req->execute();

    // Récupération des informations
    $tab = $req->fetchAll(PDO::FETCH_ASSOC);

    // Affichage des informations
    foreach ($tab as $row) {
        foreach ($row as $key => $value) {
            echo $key . " : " . $value . "<br>";
        }
        echo "<br>";
    }
}

// Test
AfficherCoursClasseHeure('2025-01-09 08:00:00', 'TDA');