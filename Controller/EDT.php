<head>
    <title>EDT</title>
    <link rel="stylesheet" type="text/css" href="../View/CSS/CSSBasique.css">
</head>
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
include "../Controller/ConnectionBDD.php";

// Exemple + Test
$dateActuel = ' 2024-10-21';  // Date par défaut
$classeActuel = 'TPC1';       // Groupe par défaut (TPC1 en 1ère année)
$anneeActuel = 1;            // Année par défaut (1ère année)

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
        echo "<td style='vertical-align: top;'>{$listeHorraire[$h]}</td>";

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
    SELECT DISTINCT seance.idseance, seance.typeseance, seance.duree, schedule.salle, 
           collegue.prenom, collegue.nom, enseignement.court as matiere, 
           enseignement.discipline, horaire as date, schedule.nomgroupe
    FROM seance
    JOIN collegue ON seance.collegue = collegue.id
    JOIN enseignement ON seance.code = enseignement.code
    JOIN schedule ON seance.nomgroupe = schedule.nomgroupe
    JOIN ressourcegroupe rg ON schedule.nomgroupe = rg.nomgroupe
    WHERE horaire = ?
      AND schedule.version = 20
      AND (
         schedule.nomgroupe = ?
         OR schedule.nomgroupe = 'CM'
         OR schedule.nomgroupe LIKE 'TD%'
      )
      AND rg.semestre IN ($semestresString)
    LIMIT 1";

    $connexion = getConnectionBDD();
    $req = $connexion->prepare($sql);
    $req->execute([$dateTime, $classe]);

    $cours = $req->fetch(PDO::FETCH_ASSOC);

    if ($cours) {
        // Extraire la durée en minutes
        $dureeStr = $cours['duree'];

        // Gérer les deux formats possibles de durée
        if (strpos($dureeStr, 'years') !== false) {
            // Format: "0 years 0 mons 0 days X hours Y mins 0.0 secs"
            preg_match('/(\d+) hours (\d+) mins/', $dureeStr, $matches);
            if (!empty($matches)) {
                $dureeMinutes = (intval($matches[1]) * 60) + intval($matches[2]);
            }
        } else {
            // Format: "HH:MM:SS"
            $dureeParts = explode(':', $dureeStr);
            if (count($dureeParts) == 3) {
                $dureeMinutes = (intval($dureeParts[0]) * 60) + intval($dureeParts[1]);
            }
        }

        // Si aucun format n'a été reconnu, utiliser la durée par défaut
        if (!isset($dureeMinutes)) {
            $dureeMinutes = 90;
        }

        $discipline = strtolower(supprimerAccents($cours['discipline']));
        $discipline = preg_replace('/[^a-z0-9]+/', '-', $discipline);
        $discipline = trim($discipline, '-');

        $typeSeance = strtolower($cours['typeseance']);

        // Si cours dure 3h, on utilise le CSS adapté
        if ($dureeMinutes == 180){
            $classeCSS = "cours-" . $discipline . "-" . $typeSeance.'-3';
        }
        // Sinon le CSS de base
        else {
            $classeCSS = "cours-" . $discipline . "-" . $typeSeance;
        }

        // Si la salle est en amphi, on affiche uniquement "Amphi"
        if ($cours['salle']=='200'){
            $contenuHTML = "<div class='$classeCSS'>" .
                $cours['typeseance'] . "<br>" .
                $cours['matiere'] . "<br>" .
                $cours['prenom'][0] . ". " . $cours['nom'] . "<br>" .
                "Amphi " .
                "</div>";
        }

        // Sinon, on est dans la salle de TD, on affiche "Salle" et son nuémro
        else{
            $contenuHTML = "<div class='$classeCSS'>" .
                $cours['typeseance'] . "<br>" .
                $cours['matiere'] . "<br>" .
                $cours['prenom'][0] . ". " . $cours['nom'] . "<br>" .
                "Salle " . $cours['salle'] .
                "</div>";
        }

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