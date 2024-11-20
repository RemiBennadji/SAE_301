<html>
<head>
    <title>EDT</title>
    <link rel="stylesheet" type="text/css" href="../View/CSS/CSSBasique.css">
</head>
<body>
<a href="MenuPrincipal.php"><img src="../Ressource/logouphf2.png" class="logoUPHF" alt="Logo UPHF"></a>
<header>
    <nav>
        <div class="burger">
            <span></span>
            <span></span>
            <span></span>
        </div>
        <ul class="menu">
            <li><a class="underline-animation" href="../Controller/EDT.php">Emploi du temps</a></li>
            <li><a class="underline-animation" href="#">Messagerie</a></li>
            <li><a class="underline-animation" href="../View/HTML/creationCompte.html" id="creationCompte" style="display: none">Créer un compte</a></li>
            <li><a class="underline-animation" href="../Controller/Deconnexion.php">Déconnexion</a></li>
        </ul>
    </nav>
</header>
<script><!-- script pour que les liens href soi responsive -->
    const burger = document.querySelector('.burger');
    const menu = document.querySelector('.menu');
    burger.addEventListener("click", () => {
        menu.classList.toggle("active");
        burger.classList.toggle("toggle");
    });
</script>
<br><br><br>

<?php
//
//session_start();
//// Vérification si le rôle est défini, sinon rediriger vers la page de connexion
//if (!isset($_SESSION['role'])) {
//    header("Location: ../View/HTML/Identification.html"); // Redirection si pas de rôle
//    exit();
//}

include "../Controller/ConnectionBDD.php";

// Exemple + Test
$dateActuel = ' 2025-01-06';  // Date par défaut
$classeActuel = 'C1';         // Groupe par défaut (TPC1 en 1ère année)
$anneeActuel = 1;             // Année par défaut (1ère année)

// Fonction pour afficher l'emploi du temps de la semaine
function AfficherEdtSemaine($dateDebut, $classe, $annee) {
    $timestamp = strtotime($dateDebut);
    $lundi = date("Y-m-d", $timestamp);

    echo "<table>";
    echo "<tr><th>Heure</th>";

    $joursSemaine = ['Lundi', 'Mardi', 'Mercredi', 'Jeudi', 'Vendredi'];
    for ($i = 0; $i < 5; $i++) {
        $jourTimestamp = strtotime("+$i day", strtotime($lundi));
        echo "<th>" . $joursSemaine[$i] . " " . date("d/m", $jourTimestamp) . "</th>";
    }
    echo "</tr>";

    $listeHorraire = ['08:00', '09:30', '11:00', '12:30', '14:00', '15:30', '17:00'];

    // Tableau pour stocker les cellules à sauter
    $cellulesSautees = array_fill(0, 5, 0);

    // Boucle sur chaque horaire
    for ($h = 0; $h < count($listeHorraire); $h++) {
        echo "<tr>";
        echo "<td style='vertical-align: top;'>$listeHorraire[$h]</td>";

        // Boucle pour chaque jour
        for ($j = 0; $j < 5; $j++) {
            if ($cellulesSautees[$j] > 0) {
                $cellulesSautees[$j]--;
                continue;
            }

            $jourTimestamp = strtotime("+$j day", strtotime($lundi));
            $jour = date("Y-m-d", $jourTimestamp);

            $coursInfo = RecupererCours($jour, $listeHorraire[$h], $classe, $annee);

            if ($coursInfo) {
                $cours = $coursInfo['contenu'];
                $duree = $coursInfo['duree'];
                $nombreCreneaux = ceil($duree / 90);

                if ($nombreCreneaux > 1) {
                    echo "<td rowspan='$nombreCreneaux'>$cours</td>";
                    $cellulesSautees[$j] = $nombreCreneaux - 1;
                } else {
                    echo "<td>$cours</td>";
                }
            } else {
                echo "<td></td>";
            }
        }
        echo "</tr>";
    }
    echo "</table>";
}

// Fonction pour retirer les accents et convertir en équivalents non accentués
function supprimerAccents($str) {
    return str_replace(
        ['é', 'è', 'ê', 'ë', 'à', 'â', 'ä', 'ù', 'û', 'ü', 'î', 'ï', 'ô', 'ö', 'ç', 'É', 'È', 'Ê', 'Ë', 'À', ' ', 'Ä', 'Ù', 'Û', 'Ü', 'Î', 'Ï', 'Ô', 'Ö', 'Ç'],
        ['e', 'e', 'e', 'e', 'a', 'a', 'a', 'u', 'u', 'u', 'i', 'i', 'o', 'o', 'c', 'e', 'e', 'e', 'e', 'a', 'a', 'a', 'u', 'u', 'u', 'i', 'i', 'o', 'o', 'c'],
        $str
    );
}

// Fonction pour récupérer un cours pour un jour et une heure donnés
function RecupererCours($jour, $horaire, $classe, $annee) {
    $dateTime = $jour . ' ' . $horaire . ':00';

    $semestres = ($annee == 1) ? [1, 2] : (($annee == 2) ? [3, 4] : [5, 6]);
    $semestresString = implode(",", $semestres);
    $sql = "
    SELECT
    seance.idseance, seance.typeseance, seance.duree, schedulesalle.salle,
    collegue.prenom, collegue.nom,
    enseignement.court as matiere,
    enseignement.discipline, horaire as date, schedule.nomgroupe, code
    FROM seance
         LEFT JOIN collegue ON seance.collegue = collegue.id
         JOIN enseignement USING (code, semestre)
         JOIN schedule USING (code, typeseance, typeformation, nomgroupe, semestre, noseance)
         JOIN ressourcegroupe rg USING (nomgroupe, typeformation, semestre)
         JOIN schedulesalle USING (code, typeseance, typeformation, nomgroupe, semestre, noseance, version)
    WHERE horaire = ?
    AND version = 38
    AND nomressource = ?
    AND semestre IN ($semestresString)
    ";

    $connexion = getConnectionBDD();
    $req = $connexion->prepare($sql);
    $req->execute([$dateTime, $classe]);

    // Récupérer toutes les lignes pour un cours
    $coursList = $req->fetchAll(PDO::FETCH_ASSOC);

    if ($coursList) {
        // Récupérer la première entrée
        $cours = $coursList[0];

        // Récupérer toutes les salles de manière unique
        $salles = array_unique(array_map(function($row) { return $row['salle']; }, $coursList));

        // Extraire la durée en minutes
        $dureeStr = $cours['duree'];

        // Gérer les deux formats possibles de durée
        if (strpos($dureeStr, 'years') !== false) {
            preg_match('/(\d+) hours (\d+) mins/', $dureeStr, $matches);
            if (!empty($matches)) {
                $dureeMinutes = (intval($matches[1]) * 60) + intval($matches[2]);
            }
        } else {
            $dureeParts = explode(':', $dureeStr);
            if (count($dureeParts) == 3) {
                $dureeMinutes = (intval($dureeParts[0]) * 60) + intval($dureeParts[1]);
            }
        }

        if (!isset($dureeMinutes)) {
            $dureeMinutes = 90;
        }

        $discipline = strtolower(supprimerAccents($cours['discipline']));
        $discipline = preg_replace('/[^a-z0-9]+/', '-', $discipline);
        $discipline = trim($discipline, '-');

        $typeSeance = strtolower($cours['typeseance']);

        // Déterminer la classe CSS et le format des salles en fonction du type de séance
        if ($typeSeance == 'ds') {
            $classeCSS = "ds";
            $sallesStr = "Amphi, Salle 110"; // Format fixe pour les DS
        }
        elseif ($typeSeance == 'prj') {
            $classeCSS = "sae";
            $sallesStr = "Salle " . implode(", ", $salles);
        }
        else {
            if ($dureeMinutes == 180){
                $classeCSS = "cours-" . $discipline . "-" . $typeSeance.'-3';
            }
            else {
                $classeCSS = "cours-" . $discipline . "-" . $typeSeance;
            }
            // Pour les autres types, vérifier si c'est uniquement en Amphi
            if (count($salles) == 1 && reset($salles) == '200') {
                $sallesStr = "Amphi";
            } else {
                $sallesStr = "Salle " . implode(", ", $salles);
            }
        }

        $profInfo = '';
        if ($cours['prenom'] && $cours['nom']) {
            $profInfo = $cours['prenom'][0] . ". " . $cours['nom'];
        }

        $contenuHTML = "<div class='$classeCSS'>" .
            $cours['typeseance'] . "<br>" .
            $cours['matiere']  . " | " .  $cours['code']   ."<br>" .
            $profInfo . "<br>" .
            $sallesStr .
            "</div>";

        return [
            'contenu' => $contenuHTML,
            'duree' => $dureeMinutes
        ];
    }

    return null;
}


// Fonction pour incrémenter une semaine
function incrementerSemaine($ancienneDate) {
    $timestamp = strtotime($ancienneDate);
    $nouveauLundi = strtotime("+7 day", $timestamp);
    return date("Y-m-d", $nouveauLundi);
}

// Fonction pour décrémenter une semaine
function decrementerSemaine($ancienneDate) {
    $timestamp = strtotime($ancienneDate);
    $nouveauLundi = strtotime("-7 day", $timestamp);
    return date("Y-m-d", $nouveauLundi);
}

// Gestion des requêtes POST (navigation entre les semaines)
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Récupération de la date actuelle envoyée par le formulaire
    $dateActuel = isset($_POST["dateActuel"]) ? $_POST["dateActuel"] : $dateActuel;

    // Si le bouton semaine précédente est pressé
    if (isset($_POST["precedent"])) {
        $dateActuel = decrementerSemaine($dateActuel);
    }

    // Si le bouton semaine suivante est pressé
    if (isset($_POST["suivant"])) {
        $dateActuel = incrementerSemaine($dateActuel);
    }
}

// Affichage du titre et du formulaire de changement de semaine
echo ('<div class="changerSemaine">
   <form action="EDT.php" method="post">
       <button type="submit" name="precedent"><</button>
       <label>Semaine du ' . date("d/m/Y", strtotime($dateActuel)) . '</label>
       <input type="hidden" name="dateActuel" value="'. $dateActuel .'">
       <button type="submit" name="suivant">></button>
   </form>
</div>');

echo ('<footer class="footer">
    <p>&copy; 2024 - SAE Emploi du temps. Rémi | Dorian | Matthéo | Bastien | Noah.</p>
</footer>');

// Affichage de l'emploi du temps pour la semaine choisie
AfficherEdtSemaine($dateActuel, $classeActuel, $anneeActuel);
?>
</body>
</html>
