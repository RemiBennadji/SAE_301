<?php

global $tabEntier;
include ('../Model/connectionServ.php');

// Afficher les valeurs pour chaque ligne

/*foreach($tabEntier as $row){
    foreach($row as $key => $value){
        echo $key.": ".$value."<br>";
    }
    echo "<br>";
}*/


$minut = 0;//un cours de 1h30 est égal à 90min
$edt = array();//liste de l'emploi du temps complet
foreach ($tabEntier as $i){
    $day = array();//fait la liste de cours pour une journée
    $lesson = array();//enregistre les id pour verifier si les cours sont deja placé
    while($minut<=450) {
        if ((!in_array($i['idseance'], $lesson))) {//450 minutes = 7h30 de cours et verifie si l'id de la seance est deja placé
            array_push($day, $i);
            array_push($lesson, $i['idseance']);
            $day += $i['duree'];
        }
    }
    $minut = 0;
    array_push($edt, $day);
    array_splice($day);//clear la liste
}



























// Fonction pour afficher l'EDT
function afficherEDT ($week) {
    // Dans la semaine, pour chaque jour et heures
    foreach($week as $day){
        foreach($day as $lesson){
            // On affiche toutes les infos du cours (matière, prof, type de cours)
            afficherInfoHeure($lesson);
        }

    }
}

function afficherInfoHeure($lesson){
    // Il faut retourner ici une case (d'un tableau) qui contient dans l'ordre, le type de séance, la salle, le prof, et la matière
    // echo ("<td type de séance, la salle, le prof, et la matière>"); // Remplacer par les élement du tableau de cours
    echo $lesson."<br>";
}


$edtSemaineTest = [[1,2,3]];
afficherEDT($edtSemaineTest);


























?>
