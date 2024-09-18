<?php
//requête SQL
$sql1 = "SELECT * FROM seance";//idseance typeseance salle code join collège(nom complet prof, matière)

/*
SELECT idseance, typeseance, salle, collegue.prenom, collegue.nom
FROM seance
JOIN collegue ON seance.collegue = collegue.id
*/

/*
SELECT idseance, typeseance, salle, collegue.prenom, collegue.nom, enseignement.long
FROM seance
JOIN collegue ON seance.collegue = collegue.id
JOIN enseignement ON enseignement.code = matiere
*/

// Arguments de connetion
try {
    $connection = new PDO ("pgsql:host=iutinfo-sgbd.uphf.fr; dbname=edt user=iutinfo340 password=jWBfxD1E");
    $connection -> setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    //echo "connecté.<br>";

    $test = $connection -> prepare($sql1);
    $test -> execute();
    $test1= $test -> fetchAll(PDO::FETCH_ASSOC);
    foreach($test1 as $row){
        foreach($row as $key => $value){
            echo $key.": ".$value."<br>";
            echo $key.": ".$value."<br>";
        }
        echo "<br>";
    }

} catch ( Exception $e ) {
    echo $e->getMessage();
}


