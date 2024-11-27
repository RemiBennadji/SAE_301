<html lang="fr">
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
    $joursData = [];

    // Récupérer les données pour tous les jours en une fois
    for ($i = 0; $i < 5; $i++) {
        $jourTimestamp = strtotime("+$i day", strtotime($lundi));
        $jour = date("Y-m-d", $jourTimestamp);
        $joursData[$i] = RecupererCoursParJour($jour, $classe, $annee);
        echo "<th>" . $joursSemaine[$i] . " " . date("d/m", $jourTimestamp) . "</th>";
    }
    echo "</tr>";

    $listeHorraire = ['08:00', '09:30', '11:00', '12:30', '14:00', '15:30', '17:00'];
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
                // Calculer la durée en minutes
                $dureeStr = $cours['duree'];
                if (strpos($dureeStr, 'years') !== false) {
                    preg_match('/(\d+) hours (\d+) mins/', $dureeStr, $matches);
                    $dureeMinutes = !empty($matches) ? (intval($matches[1]) * 60) + intval($matches[2]) : 90;
                } else {
                    $dureeParts = explode(':', $dureeStr);
                    $dureeMinutes = count($dureeParts) == 3 ? (intval($dureeParts[0]) * 60) + intval($dureeParts[1]) : 90;
                }

                $nombreCreneaux = ceil($dureeMinutes / 90);

                // Générer le contenu HTML avec le style approprié
                $discipline = strtolower(supprimerAccents($cours['discipline']));
                $discipline = preg_replace('/[^a-z0-9]+/', '-', $discipline);
                $discipline = trim($discipline, '-');

                $typeSeance = strtolower($cours['typeseance']);

                // Déterminer la classe CSS
                if ($typeSeance == 'ds') {
                    $classeCSS = "ds";
                } elseif ($typeSeance == 'prj') {
                    $classeCSS = "sae";
                } else {
                    $classeCSS = $dureeMinutes == 180 ?
                        "cours-" . $discipline . "-" . $typeSeance . '-3' :
                        "cours-" . $discipline . "-" . $typeSeance;
                }

                $prenomProf = $cours['prenom'][0] . ".";
                if ($prenomProf == ".") {
                    $prenomProf = "";  // Si c'est juste un ".", cela veut dire qu'il n'y a pas de prof
                }

                $contenuHTML = "<div class='$classeCSS'>" .
                    $cours['typeseance'] . "<br>" .
                    $cours['matiere'] . " | " . $cours['code'] . "<br>" .
                    $prenomProf . $cours['nom'] . "<br>" .
                    "Salle " . $cours['salle'] .
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

// Fonction pour retirer les accents et convertir en équivalents non accentués
function supprimerAccents($str) {
    return str_replace(
        ['é', 'è', 'ê', 'ë', 'à', 'â', 'ä', 'ù', 'û', 'ü', 'î', 'ï', 'ô', 'ö', 'ç', 'É', 'È', 'Ê', 'Ë', 'À', ' ', 'Ä', 'Ù', 'Û', 'Ü', 'Î', 'Ï', 'Ô', 'Ö', 'Ç'],
        ['e', 'e', 'e', 'e', 'a', 'a', 'a', 'u', 'u', 'u', 'i', 'i', 'o', 'o', 'c', 'e', 'e', 'e', 'e', 'a', 'a', 'a', 'u', 'u', 'u', 'i', 'i', 'o', 'o', 'c'],
        $str
    );
}

function RecupererCoursParJour($jour, $classe, $annee) {
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
    WHERE DATE(horaire) = ?
        AND version = 38
        AND nomressource = ?
        AND semestre IN ($semestresString)
    ORDER BY horaire
    ";

    $connexion = getConnectionBDD();
    $req = $connexion->prepare($sql);
    $req->execute([$jour, $classe]);
    return $req->fetchAll(PDO::FETCH_ASSOC);
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

// Affichage du footer
echo ('<footer class="footer">
    <p>&copy; 2024 - SAE Emploi du temps. Rémi | Dorian | Matthéo | Bastien | Noah.</p>
</footer>');

// Affichage de l'emploi du temps pour la semaine choisie
AfficherEdtSemaine($dateActuel, $classeActuel, $anneeActuel);
?>
</body>
</html>