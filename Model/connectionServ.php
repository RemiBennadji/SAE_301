<?php

// Arguments de connetion + requête SQL
$connection = pg_connect ("host=iutinfo-sgbd.uphf.fr dbname=edt user=iutinfoXXX password=XXXXXXX");
$sql1 = "SELECT * FROM heures";

function recupererDonnees($conn, $sql)
{
    if ($conn) {
        $tab = array();
        $resu = pg_query($conn, $sql);
        for ($i = 0; $i < pg_num_rows($resu); $i++){
            $row = pg_fetch_row($resu);
            $tab[] = $row[0];
        }
        return $tab;
    }
}

$tab = recupererDonnees($connection, $sql1);

// Affichage des éléments du tableau
for ($j = 0; $j < count($tab); $j++)
    echo $tab[$j]."\n";
?>

