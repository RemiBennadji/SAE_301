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
<script><!-- script pour que les liens href soit responsive -->
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

session_start();
// Vérification si le rôle est défini, sinon rediriger vers la page de connexion
if (!isset($_SESSION['role'])) {
    header("Location: ../View/HTML/Identification.html"); // Redirection si pas de rôle
    exit();
}

// Exemple + Test
$dateActuel = ' 2025-01-06';  // Date par défaut
$classeActuel = 'C1';         // Groupe par défaut (TPC1 en 1ère année)
$anneeActuel = 1;             // Année par défaut (1ère année)

// Fonction pour afficher l'emploi du temps de la semaine
function AfficherEdtSemaine($dateDebut) {
    $timestamp = strtotime($dateDebut);
    $lundi = date("Y-m-d", $timestamp);
    $salle103 = '103';

    echo "<table>";
    echo "<tr><th>Heure</th>";

    $joursSemaine = ['103', '105', '106', '107', '108', '109', '110', '111', '112', '113','114', '115', '117', '118', '200'];
    for ($i = 0; $i < 15; $i++) {
        echo "<th>" . $joursSemaine[$i] . "</th>";
    }
    echo "</tr>";

    $listeHorraire = ['08:00', '09:30', '11:00', '12:30', '14:00', '15:30', '17:00'];

    // Tableau pour stocker les cellules à sauter
    $cellulesSautees = array_fill(0, 15, 0);

    // Boucle sur chaque horaire
    for ($h = 0; $h < count($listeHorraire); $h++) {
        echo "<tr>";
        echo "<td style='vertical-align: top;'>$listeHorraire[$h]</td>";

        // Boucle pour chaque jour
        for ($j = 0; $j < 15; $j++) {
            if ($cellulesSautees[$j] > 0) {
                $cellulesSautees[$j]--;
                continue;
            }

//            $jourTimestamp = strtotime("+$j day", strtotime($lundi));
//            $jour = date("Y-m-d", $jourTimestamp);

            $coursInfo = RecupererCours($salle103, $listeHorraire[$h]);

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
function RecupererCours($jourParam) {
    $jour = date("Y-m-d");
    $heure = date("H:i:s");
    $timestamp = strtotime($jour);
    $date = date("Y-m-d", $timestamp).' '.$heure;

    for ($i=0; $i < 7; ++$i) {
        //requête permettant d'accéder aux salles utilisées à l'horaire saisi
        $sql1 = "SELECT DISTINCT salle FROM schedulesalle JOIN schedule 
        USING (code, typeseance, typeformation, noseance, semestre, version)  
        WHERE horaire = :DATE and version = 38";

//requête pour avoir toutes les salles
        $sql2 = "SELECT DISTINCT nosalle FROM listesalles";

//requête pour avoir les salles utilisées pour 3h l'heure d'avant
        $sql3 = "SELECT DISTINCT salle FROM schedulesalle JOIN schedule
        USING (code, typeseance, typeformation, noseance, semestre, version)
        WHERE horaire = :HEURE and version = 38 and duration = '0 years 0 mons 0 days 3 hours 0 mins 0.0 secs'";

        //liste qui va stocker les salles utilisées
        $sallesAll = array();

        $connection = getConnectionBDD(); //Connexion à la base de données

        //execution de la requête 1 et ajoute les salles à la liste sallesAll
        $resultSalles = $connection->prepare($sql1);
        $resultSalles->bindParam(':DATE', $date);
        $resultSalles->execute();
        $listeSalles = $resultSalles->fetchAll(PDO::FETCH_ASSOC);

        //execution de la requête 3 et ajoute les salles utilisées pour 3h à celles d'avant
        $sallesInf = $connection->prepare($sql3);
        $sallesInf->bindParam(':HEURE', $dateInf);
        $sallesInf->execute();
        $salleInf = $sallesInf->fetchAll(PDO::FETCH_ASSOC);

        //récupère les salles utilisées pour 1h30
        foreach ($listeSalles as $salle) {
            $sallesAll[] = $salle['salle'];
        }

        //récupère les salles utilisées pour 3h à l'heure d'avant
        foreach ($salleInf as $salleIndispo) {
            $sallesAll[] = $salleIndispo['salle'];
        }

        //execution de la requête 2 et affiche les salles grâce à une comparaison avec sallesAll
        $salles = $connection->prepare($sql2);
        $salles->execute();
        $sallesDispo = $salles->fetchAll(PDO::FETCH_ASSOC);
        $sallesLibres = array();

        //permet d'avoir les salles libres en les comparant avec la liste de salles utilisées
        foreach ($sallesDispo as $nosalle) {
            if (!in_array($nosalle['nosalle'], $sallesAll)) {
                $sallesLibres[] = $nosalle['nosalle'];
            }
        }

        // Trier les salles libres par leur numéro (ordre croissant)
        sort($sallesLibres, SORT_NUMERIC);
    }


}


// Fonction pour incrémenter une semaine
function incrementerSemaine($ancienneDate) {
    $timestamp = strtotime($ancienneDate);
    $nouveauLundi = strtotime("+1 day", $timestamp);
    return date("Y-m-d", $nouveauLundi);
}

// Fonction pour décrémenter une semaine
function decrementerSemaine($ancienneDate) {
    $timestamp = strtotime($ancienneDate);
    $nouveauLundi = strtotime("-1 day", $timestamp);
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
       <label>Jour : ' . date("d/m/Y", strtotime($dateActuel)) . '</label>
       <input type="hidden" name="dateActuel" value="'. $dateActuel .'">
       <button type="submit" name="suivant">></button>
   </form>
</div>');

echo ('<footer class="footer">
    <p>&copy; 2024 - SAE Emploi du temps. Rémi | Dorian | Matthéo | Bastien | Noah.</p>
</footer>');

// Affichage de l'emploi du temps pour la semaine choisie
AfficherEdtSemaine($dateActuel);
?>
</body>
</html>
