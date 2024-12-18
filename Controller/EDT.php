<html lang="fr">
<head>
    <title>EDT</title>
    <link rel="stylesheet" type="text/css" href="../View/CSS/CSSBasique.css">
</head>
<body>
<a href="MenuPrincipal.php"><img src="../Ressource/logouphf2.png" class="logoUPHF" alt="Logo UPHF"></a>
<script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf/2.5.1/jspdf.umd.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf-autotable/3.5.22/jspdf.plugin.autotable.min.js"></script>
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
<script>
    const burger = document.querySelector('.burger');
    const menu = document.querySelector('.menu');
    burger.addEventListener("click", () => {
        menu.classList.toggle("active");
        burger.classList.toggle("toggle");
    });
</script>
<br><br><br>

<?php
include "../Controller/ConnectionBDD.php";

//ini_set('display_errors', 1);
//ini_set('display_startup_errors', 1);
//error_reporting(E_ALL);

// Vérifier si le cookie "groupe" existe
if (isset($_COOKIE['groupe'])) {
    $classeActuel = $_COOKIE['groupe'];
} else {
    echo "Le cookie 'groupe' n'est pas défini.";
}

// Vérifier si le cookie "annee" existe
if (isset($_COOKIE['annee'])) {
    $anneeActuel = $_COOKIE['annee'];
} else {
    echo "Le cookie 'annee' n'est pas défini.";
}

$dateActuel = date('Y-m-d', strtotime('monday this week'));

function AfficherEdtSemaine($dateDebut, $classe, $annee) {
    $timestamp = strtotime($dateDebut);
    $lundi = date("Y-m-d", $timestamp);

    echo "<table>";
    echo "<tr><th>Heure</th>";

    $joursSemaine = ['Lundi', 'Mardi', 'Mercredi', 'Jeudi', 'Vendredi'];
    $joursData = [];

    for ($i = 0; $i < 5; $i++) {
        $jourTimestamp = strtotime("+$i day", strtotime($lundi));
        $jour = date("Y-m-d", $jourTimestamp);
        $joursData[$i] = RecupererCoursParJour($jour, $classe, $annee);
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

                $dureeStr = $cours['duree'];
                if (strpos($dureeStr, 'years') !== false) {
                    preg_match('/(\d+) hours (\d+) mins/', $dureeStr, $matches);
                    $dureeMinutes = !empty($matches) ? (intval($matches[1]) * 60) + intval($matches[2]) : 90;
                } else {
                    $dureeParts = explode(':', $dureeStr);
                    $dureeMinutes = count($dureeParts) == 3 ? (intval($dureeParts[0]) * 60) + intval($dureeParts[1]) : 90;
                }

                $nombreCreneaux = ceil($dureeMinutes / 90);

                $discipline = strtolower(supprimerAccents($cours['discipline']));
                $discipline = preg_replace('/[^a-z0-9]+/', '-', $discipline);
                $discipline = trim($discipline, '-');

                $typeSeance = strtolower($cours['typeseance']);
                $salles = explode(',', $cours['salles']);

                if ($typeSeance == 'ds') {
                    $classeCSS = "ds";
                    if ($annee == 1){
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
                        "cours-" . $discipline . "-" . $typeSeance . '-3' :
                        "cours-" . $discipline . "-" . $typeSeance;

                    if (count($salles) == 1 && $salles[0] == '200') {
                        $sallesStr = "Amphi";
                    } else {
                        $sallesStr = "Salle " . implode(", ", $salles);
                    }
                }

                $prenomProf = $cours['prenom'][0] . ".";
                if ($prenomProf == ".") {
                    $prenomProf = "";
                }

                $contenuHTML = "<div class='$classeCSS'>" .
                    $cours['typeseance'] . "<br>" .
                    $cours['code'] . " " . $cours['matiere'] . "<br>" .
                    $prenomProf . $cours['nom'] . "<br>" .
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

function supprimerAccents($str) {
    return str_replace(
        ['é', 'è', 'ê', 'ë', 'à', 'â', 'ä', 'ù', 'û', 'ü', 'î', 'ï', 'ô', 'ö', 'ç', 'É', 'È', 'Ê', 'Ë', 'À', ' ', 'Ä', 'Ù', 'Û', 'Ü', 'Î', 'Ï', 'Ô', 'Ö', 'Ç'],
        ['e', 'e', 'e', 'e', 'a', 'a', 'a', 'u', 'u', 'u', 'i', 'i', 'o', 'o', 'c', 'e', 'e', 'e', 'e', 'a', 'a', 'a', 'u', 'u', 'u', 'i', 'i', 'o', 'o', 'c'],
        $str
    );
}

function RecupererCoursParJour($jour, $classe, $annee): array
{
    $semestres = ($annee == 1) ? [1, 2] : (($annee == 2) ? [3, 4] : [5, 6]);
    $semestresString = implode(",", $semestres);
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

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    if (isset($_POST["selectedDate"])) {
        // Convertir la date sélectionnée en date du lundi de la semaine
        $selectedDate = new DateTime($_POST["selectedDate"]);
        $dayOfWeek = $selectedDate->format('N'); // 1 (lundi) à 7 (dimanche)
        $daysToSubtract = $dayOfWeek - 1;
        $selectedDate->sub(new DateInterval("P{$daysToSubtract}D"));
        $dateActuel = $selectedDate->format('Y-m-d');
    } else {
        $dateActuel = $_POST["dateActuel"] ?? $dateActuel;
    }

    if (isset($_POST["precedent"])) {
        $dateActuel = decrementerSemaine($dateActuel);
    }

    if (isset($_POST["suivant"])) {
        $dateActuel = incrementerSemaine($dateActuel);
    }
}

echo '<div class="changerSemaine">
    <button id="download-pdf" class="btn">Télécharger en PDF</button>
    <form action="EDT.php" method="post">
        <button type="submit" name="precedent">&lt;</button>
        
        <label for="selectionnerSemaine">Semaine du</label>
        <input type="date" id="selectionnerSemaine" name="selectedDate" onchange="this.form.submit()" 
               value="' . htmlspecialchars($dateActuel, ENT_QUOTES, 'UTF-8') . '">
        
        <input type="hidden"  name="dateActuel" 
               value="' . htmlspecialchars($dateActuel, ENT_QUOTES, 'UTF-8') . '">
        
        <button type="submit" name="suivant">&gt;</button>
    </form>
</div>';

echo ('<footer class="footer">
    <p>&copy; 2024 - SAE Emploi du temps. Rémi | Dorian | Matthéo | Bastien | Noah.</p>
</footer>');
AfficherEdtSemaine($dateActuel, $classeActuel, $anneeActuel);
?>

<script src="../Model/JavaScript/CalendrierEDT.js"></script>
<script src="../Model/JavaScript/GenererPDF.js"></script>
</body>
</html>