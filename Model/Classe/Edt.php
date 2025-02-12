<?php

class Edt
{

    function AfficherEdtJour($dateDebut, $classe, $annee, $version){

        $timestamp = strtotime($dateDebut);
        echo "<table>";
        echo "<tr><th>Heure</th><th>" . date("d/m/Y", $timestamp) . "</th></tr>";

        $joursSemaine = ['Lundi', 'Mardi', 'Mercredi', 'Jeudi', 'Vendredi'];

        $jour = date("Y-m-d", $timestamp);
        $joursData = $this->RecupererCoursParJour($jour, $classe, $annee, $version);
        echo "</tr>";

        $listeHoraire = ['08:00', '09:30', '11:00', '12:30', '14:00', '15:30'.'<br>'.'<br>'.'<br>'.'17:00'];
        $cellulesSautees = array_fill(0, 5, 0);


        $j = 2;
        // Parcourir chaque horaire pour afficher les cours correspondants
        for ($h = 0; $h < count($listeHoraire); $h++) {
            echo "<tr>";
            echo "<td style='vertical-align: top;'>$listeHoraire[$h]</td>";
            if ($cellulesSautees[$j] > 0) {
                $cellulesSautees[$j]--;
                continue;
            }

            $horaireCourant = date("H:i:s", strtotime($listeHoraire[$h]));
            $coursDuJour = array_filter($joursData[$j], function($cours) use ($horaireCourant) {
                return date("H:i:s", strtotime($cours['date'])) === $horaireCourant;
            });

            if (!empty($coursDuJour)) {
                $cours = current($coursDuJour);

                // Récupération de la durée des cours (1h30, 3h00) @Dorian
                $dureeStr = $cours['duree'];
                if (strpos($dureeStr, 'years') !== false) {
                    preg_match('/(\d+) hours (\d+) mins/', $dureeStr, $matches);
                    $dureeMinutes = !empty($matches) ? (intval($matches[1]) * 60) + intval($matches[2]) : 90;
                } else {
                    $dureeParts = explode(':', $dureeStr);
                    $dureeMinutes = count($dureeParts) == 3 ? (intval($dureeParts[0]) * 60) + intval($dureeParts[1]) : 90;
                }

                $nombreCreneaux = ceil($dureeMinutes / 90);

                // Formatage du nom de la discipline pour qu'elle adaptait au CSS @Dorian
                $discipline = strtolower($this->supprimerAccents($cours['discipline']));
                $discipline = preg_replace('/[^a-z0-9]+/', '-', $discipline);
                $discipline = trim($discipline, '-');

                $typeSeance = strtolower($cours['typeseance']);
                $salles = explode(',', $cours['salles']);

                // Si c'est UN DS @Dorian
                if ($typeSeance == 'ds') {
                    $classeCSS = "ds";
                    // Et que le DS est pour les premières années
                    if ($annee == 1){
                        // On ajoute la salle 110
                        $sallesStr = "Amphi, Salle 110";
                    }
                    else{
                        $sallesStr = "Amphi";
                    }
                }
                elseif ($typeSeance == 'prj') {
                    $classeCSS = "sae";
                    $sallesStr = "Salle " . implode(", ", $salles);
                }
                else {
                    $classeCSS = $dureeMinutes == 180 ?
                        // Si le cours dure 3h, alors on ajoute "-3" pour la case soit plus "large" sinon, c'est le format de base @Dorian
                        "cours-" . $discipline . "-" . $typeSeance . '-3' :
                        "cours-" . $discipline . "-" . $typeSeance;

                    if (count($salles) == 1 && $salles[0] == '200') {
                        $sallesStr = "Amphi";
                    } else {
                        $sallesStr = "Salle " . implode(", ", $salles);
                    }
                }

                if(isset($cours['prenom'])){
                    // Formatage du nom des professeurs (P. Nom)
                    $nomProf = $cours['prenom'][0] . ". ". $cours['nom'];
                }

                // Si aucun prof (On reformate en vide)
                if ($nomProf == ". ") {
                    $nomProf = "";
                }

                // On compile toutes les informations "intéressante" à afficher @Dorian
                $contenuHTML = "<div class='tooltip caseEDT $classeCSS'>" .
                    $cours['typeseance'] . "<br>" .
                    "<span class='tooltiptext'>" .
                    "Professeur : " . $cours['prenom'] . " " . $cours['nom'] . "<br>" .
                    "Groupe : " . $cours['nomgroupe'] . "<br>" .
                    "Horaire : " . date("H:i", strtotime($cours['date'])) .
                    "</span>" .
                    $cours['code'] . " " . $cours['matiere'] . "<br>" .
                    $nomProf . "<br>" .
                    $sallesStr .
                    "</div>";

                echo "<td rowspan='$nombreCreneaux'>$contenuHTML</td>";
                $cellulesSautees[$j] = $nombreCreneaux - 1;
            } else {
                echo "<td></td>";
            }
        }

        // Fermer le tableau HTML
        echo "</table>";


    }
    function AfficherEdtSemaine($dateDebut, $classe, $annee, $version) {

        $timestamp = strtotime($dateDebut);
        $lundi = date("Y-m-d", $timestamp);

        echo "<table>";
        echo "<tr><th>Heure</th>";

        $joursSemaine = ['Lundi', 'Mardi', 'Mercredi', 'Jeudi', 'Vendredi'];
        $joursData = [];

        // Pour tous les jours de la semaine @Dorian
        for ($i = 0; $i < 5; $i++) {
            $jourTimestamp = strtotime("+$i day", strtotime($lundi));
            $jour = date("Y-m-d", $jourTimestamp);
            $joursData[$i] = $this->RecupererCoursParJour($jour, $classe, $annee, $version);
            echo "<th>" . $joursSemaine[$i] . " " . date("d/m", $jourTimestamp) . "</th>";
        }
        echo "</tr>";

        $listeHorraire = ['08:00', '09:30', '11:00', '12:30', '14:00', '15:30'.'<br>'.'<br>'.'<br>'.'17:00'];
        $cellulesSautees = array_fill(0, 5, 0);

        for ($h = 0; $h < count($listeHorraire); $h++) {
            echo "<tr>";
            echo "<td style='vertical-align: top;'>$listeHorraire[$h]</td>";

            for ($j = 0; $j < 5; $j++) {
                if ($cellulesSautees[$j] > 0) {
                    $cellulesSautees[$j]--;
                    continue;
                }

                $horaireCourant = date("H:i:s", strtotime($listeHorraire[$h]));
                $coursDuJour = array_filter($joursData[$j], function($cours) use ($horaireCourant) {
                    return date("H:i:s", strtotime($cours['date'])) === $horaireCourant;
                });

                if (!empty($coursDuJour)) {
                    $cours = current($coursDuJour);

                    // Récupération de la durée des cours (1h30, 3h00) @Dorian
                    $dureeStr = $cours['duree'];
                    if (strpos($dureeStr, 'years') !== false) {
                        preg_match('/(\d+) hours (\d+) mins/', $dureeStr, $matches);
                        $dureeMinutes = !empty($matches) ? (intval($matches[1]) * 60) + intval($matches[2]) : 90;
                    } else {
                        $dureeParts = explode(':', $dureeStr);
                        $dureeMinutes = count($dureeParts) == 3 ? (intval($dureeParts[0]) * 60) + intval($dureeParts[1]) : 90;
                    }

                    $nombreCreneaux = ceil($dureeMinutes / 90);

                    // Formatage du nom de la discipline pour qu'elle adaptait au CSS @Dorian
                    $discipline = strtolower($this->supprimerAccents($cours['discipline']));
                    $discipline = preg_replace('/[^a-z0-9]+/', '-', $discipline);
                    $discipline = trim($discipline, '-');

                    $typeSeance = strtolower($cours['typeseance']);
                    $salles = explode(',', $cours['salles']);

                    // Si c'est UN DS @Dorian
                    if ($typeSeance == 'ds') {
                        $classeCSS = "ds";
                        // Et que le DS est pour les premières années
                        if ($annee == 1){
                            // On ajoute la salle 110
                            $sallesStr = "Amphi, Salle 110";
                        }
                        else{
                            $sallesStr = "Amphi";
                        }
                    }
                    elseif ($typeSeance == 'prj') {
                        $classeCSS = "sae";
                        $sallesStr = "Salle " . implode(", ", $salles);
                    }
                    else {
                        $classeCSS = $dureeMinutes == 180 ?
                            // Si le cours dure 3h, alors on ajoute "-3" pour la case soit plus "large" sinon, c'est le format de base @Dorian
                            "cours-" . $discipline . "-" . $typeSeance . '-3' :
                            "cours-" . $discipline . "-" . $typeSeance;

                        if (count($salles) == 1 && $salles[0] == '200') {
                            $sallesStr = "Amphi";
                        } else {
                            $sallesStr = "Salle " . implode(", ", $salles);
                        }
                    }

                    if(isset($cours['prenom'])){
                        // Formatage du nom des professeurs (P. Nom)
                        $nomProf = $cours['prenom'][0] . ". ". $cours['nom'];
                    }

                    // Si aucun prof (On reformate en vide)
                    if ($nomProf == ". ") {
                        $nomProf = "";
                    }

                    // On compile toutes les informations "intéressante" à afficher @Dorian
                    $contenuHTML = "<div class='tooltip caseEDT $classeCSS'>" .
                        $cours['typeseance'] . "<br>" .
                        "<span class='tooltiptext'>" .
                        "Professeur : " . $cours['prenom'] . " " . $cours['nom'] . "<br>" .
                        "Groupe : " . $cours['nomgroupe'] . "<br>" .
                        "Horaire : " . date("H:i", strtotime($cours['date'])) .
                        "</span>" .
                        $cours['code'] . " " . $cours['matiere'] . "<br>" .
                        $nomProf . "<br>" .
                        $sallesStr .
                        "</div>";

                    echo "<td rowspan='$nombreCreneaux'>$contenuHTML</td>";
                    $cellulesSautees[$j] = $nombreCreneaux - 1;
                } else {
                    echo "<td></td>";
                }
            }
            echo "</tr>";
        }
        echo "</table>";
    }

    // Fonction pour retirer les accents (pour s'adapter avec le CSS) @Dorian
    function supprimerAccents($str) {
        return str_replace(
            ['é', 'è', 'ê', 'ë', 'à', 'â', 'ä', 'ù', 'û', 'ü', 'î', 'ï', 'ô', 'ö', 'ç', 'É', 'È', 'Ê', 'Ë', 'À', ' ', 'Ä', 'Ù', 'Û', 'Ü', 'Î', 'Ï', 'Ô', 'Ö', 'Ç'],
            ['e', 'e', 'e', 'e', 'a', 'a', 'a', 'u', 'u', 'u', 'i', 'i', 'o', 'o', 'c', 'e', 'e', 'e', 'e', 'a', 'a', 'a', 'u', 'u', 'u', 'i', 'i', 'o', 'o', 'c'],
            $str
        );
    }

    function RecupererCoursParJour($jour, $classe, $annee, $version): array
    // On "devine" les semestres, selon l'année de l'étudiant
    {
        if($annee==1){
            $s1 = 1;
            $s2 = 2;
        }
        elseif ($annee==2){
            $s1 = 3;
            $s2 = 4;
        }
        elseif ($annee==3){
            $s1 = 5;
            $s2 = 6;
        }
        $sql = "
    SELECT
        seance.idseance, seance.typeseance, seance.duree,
        schedulesalle.salle as salles,
        collegue.prenom, collegue.nom,
        enseignement.court as matiere,
        enseignement.discipline, horaire as date, schedule.nomgroupe, code
    FROM seance
        LEFT JOIN collegue ON seance.collegue = collegue.id
        JOIN enseignement USING (code, semestre)
        JOIN schedule USING (code, typeseance, nomgroupe, semestre, noseance)
        JOIN ressourcegroupe rg USING (nomgroupe, semestre)
        JOIN schedulesalle USING (code, typeseance, nomgroupe, semestre, noseance, version)
    WHERE DATE(horaire) = ?
        AND version = ?
        AND nomressource = ?
        AND semestre IN (?,?)
    ORDER BY horaire
    ";
        // On se connecte pour récupérer les valeurs
        $connexion = getConnectionBDD();
        $req = $connexion->prepare($sql);
        $req->execute([$jour,$version, $classe, $s1, $s2]);
        return $req->fetchAll(PDO::FETCH_ASSOC);
    }

    function incrementerSemaine($ancienneDate) {
        $timestamp = strtotime($ancienneDate);
        $nouveauLundi = strtotime("+7 day", $timestamp);
        return date("Y-m-d", $nouveauLundi);
    }

    function decrementerSemaine($ancienneDate) {
        $timestamp = strtotime($ancienneDate);
        $nouveauLundi = strtotime("-7 day", $timestamp);
        return date("Y-m-d", $nouveauLundi);
    }

    function incrementerJour($ancienneDate) {
        $timestamp = strtotime($ancienneDate);
        $nouveauLundi = strtotime("+1 day", $timestamp);
        return date("Y-m-d", $nouveauLundi);
    }

    function decrementerJour($ancienneDate) {
        $timestamp = strtotime($ancienneDate);
        $nouveauLundi = strtotime("-1 day", $timestamp);
        return date("Y-m-d", $nouveauLundi);
    }
}