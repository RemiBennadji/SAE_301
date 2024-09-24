<?php


function AfficherCoursClasseSemaine($jour, $classe){
    $listeHorraire = ['08:00:00', '09:30:00', '11:00:00', '14:00:00', '15:30:00'];
    for ($i=0; $i < 5; $i++){
        $heure = $jour.$listeHorraire[$i];
        AfficherCoursClasseHeure($heure, $classe);
    }
}

function AfficherCoursClasseHeure($heure, $classe){
    $sql = "
    SELECT DISTINCT seance.idseance, seance.typeseance, duree, schedule.salle, collegue.prenom, collegue.nom, enseignement.court as matiere, horaire as date, schedule.nomgroupe
    FROM seance
    JOIN collegue ON seance.collegue = collegue.id
    JOIN enseignement ON seance.code = enseignement.code
    JOIN schedule ON seance.nomgroupe = schedule.nomgroupe
    WHERE horaire = ? AND schedule.nomgroupe=?
    LIMIT 1"; // Il manque juste le fait est de prendre le cours associé à un élève

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

// AfficherCoursClasseHeure('2025-01-09 08:00:00', 'TDA');
AfficherCoursClasseSemaine('2025-01-09 ', 'TDA');