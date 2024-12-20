<?php
include "ConnectionBDD.php";
session_start();
//$dateActuel : utilisé pour les fleches
//$dateActuelle : utilisé pour le calendrier

// Gestion de la date actuelle ou sélectionnée
date_default_timezone_set('Europe/Paris');
if (isset($_POST['date'])) {
    $dateActuelle = new DateTime($_POST['date']);
} else {
    $dateActuelle = new DateTime();
}

$dateDuJour = $dateActuelle->format('d/m/Y');
$horaire = $dateActuelle->format('Y-m-d');

try {
    $version = 39;

    // Requête pour récupérer les horaires, les salles et les noms des professeurs
    $connection = getConnectionBDD();
    $sql = "
        SELECT DISTINCT
            duree,
            schedulesalle.salle,
            COALESCE(nom || ' ' || prenom, 'SAE') AS enseignant,
            horaire
        FROM seance
        LEFT JOIN collegue ON seance.collegue = collegue.id
        JOIN enseignement USING (code, semestre)
        right join schedule USING (code, typeseance, typeformation, nomgroupe, semestre, noseance)
        JOIN ressourcegroupe rg USING (nomgroupe, typeformation, semestre)
        JOIN schedulesalle USING (code, typeseance, typeformation, nomgroupe, semestre, noseance, version)
        WHERE date(horaire) = :horaire
          AND version = :version
        ORDER BY horaire, salle";

    $resultSalles = $connection->prepare($sql);
    $resultSalles->bindParam(':horaire', $horaire);
    $resultSalles->bindParam(':version', $version);
    $resultSalles->execute();

    $listeSalles = $resultSalles->fetchAll(PDO::FETCH_ASSOC);

} catch (PDOException $e) {
    echo $e->getMessage();
}

// Organisation des données
$sallesParHoraire = [];
foreach ($listeSalles as $i) {
    // Vérifiez si 'horaire' n'est pas null avant d'utiliser substr()
    $heure = !empty($i['horaire']) ? substr($i['horaire'], 11, 5) : '';

    // Construisez la chaîne pour la salle, avec une vérification sur 'salle' et 'enseignant'
    $salle = 'Salle ' . ($i['salle'] ?? 'Inconnue') . ' (' . ($i['enseignant'] ?? 'Inconnu') . ')';

    // Vérifiez si 'duree' n'est pas null avant de l'utiliser avec substr()
    if (!empty($i['duree'])) {
        $duree = substr($i['duree'], 22, 1) . ":" . substr($i['duree'], 30, 2);
    } else {
        $duree = '';
    }

    // Initialisez un horaire si nécessaire
    if (!isset($sallesParHoraire[$heure])) {
        $sallesParHoraire[$heure] = [];
    }

    // Gestion spéciale pour les durées de 3 heures (ou autres cas spécifiques)
    if (substr($duree, 0, 1) === "3") {
        $h = 1 + (int)substr($heure, 0, 2);
        $m = 30 + (int)substr($heure, 3, 2);

        if ($m == 60) {
            $m = 0;
            $h++;
        }

        if (strlen((string)$m) == 1) {
            $m = "0$m";
        }

        $sallesParHoraire["$h:$m"][$i['salle'] ?? 'Inconnue'] = $salle;
    }

    // Ajoutez la salle à l'horaire principal
    $sallesParHoraire[$heure][$i['salle'] ?? 'Inconnue'] = $salle;
}



// Horaires et salles prédéfinis
$horaires = ['08:00', '09:30', '11:00', '12:30', '14:00', '15:30', '17:00'];
$lesSalles = ['101', '103', '105', '106', '107', '108', '109', '110', '111', '112', '113', '114', '115', '117', '118', '200'];

// Création de la structure du tableau final
$resultat = [];
foreach ($horaires as $horaire) {
    $ligne = [];
    foreach ($lesSalles as $salle) {
        $ligne[] = $sallesParHoraire[$horaire][$salle] ?? '';
    }
    $resultat[] = $ligne;
}
$salles = $resultat;

$currentHour = date('H:i', strtotime('+1 hour'));

function incrementerSemaine($ancienneDate) {
    $timestamp = strtotime($ancienneDate);
    $nouveauLundi = strtotime("+1    day", $timestamp);
    return date("Y-m-d", $nouveauLundi);
}

function decrementerSemaine($ancienneDate) {
    $timestamp = strtotime($ancienneDate);
    $nouveauLundi = strtotime("-1 day", $timestamp);
    return date("Y-m-d", $nouveauLundi);
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $dateActuel = 0;
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
    <form action="EDTsalleLibres.php" method="post">
        <button type="submit" name="precedent">&lt;</button>
        
        <label for="selectionnerSemaine">Semaine du</label>
        <input type="date" id="selectionnerSemaine" name="selectedDate" onchange="this.form.submit()" 
               value="' . htmlspecialchars($dateActuel, ENT_QUOTES, 'UTF-8') . '">
        <input type="hidden" name="role" value="' . $_POST["role"] . '">
        <input type="hidden"  name="dateActuel" 
               value="' . htmlspecialchars($dateActuel, ENT_QUOTES, 'UTF-8') . '">
        
        <button type="submit" name="suivant">&gt;</button>
    </form>
</div>';
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>EDT Salle</title>
    <link rel="stylesheet" type="text/css" href="../View/CSS/CSSBasique.css">
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf/2.5.1/jspdf.umd.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf-autotable/3.5.22/jspdf.plugin.autotable.min.js"></script>

    <script>
        // JavaScript pour soumettre automatiquement le formulaire lorsque la date est modifiée
        document.getElementById("date").addEventListener("change", function() {
            this.form.submit();
        });
    </script>
</head>
<body>
<a href="EDT.php"><img src="../Ressource/logouphf2.png" class="logoUPHF" alt="Logo UPHF"></a>
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

<!--<div class="changerSemaine">-->
<!--    <button id="download-pdf" class="btn">Télécharger en PDF</button>-->
<!--    <form action="EDTsalleLibres.php" method="post">-->
<!--        <button type="submit" name="precedent">&lt;</button>-->
<!---->
<!--        <label for="selectionnerSemaine">Semaine du</label>-->
<!--        <input type="date" id="selectionnerSemaine" name="selectedDate" onchange="this.form.submit()"-->
<!--               value="' . htmlspecialchars($dateActuel, ENT_QUOTES, 'UTF-8') . '">-->
<!---->
<!--        <input type="hidden"  name="dateActuel"-->
<!--               value="' . htmlspecialchars($dateActuel, ENT_QUOTES, 'UTF-8') . '">-->
<!---->
<!--        <button type="submit" name="suivant">&gt;</button>-->
<!--    </form>-->
<!--</div>-->

<table>
    <thead>
    <tr>
        <th>Heure</th>
        <?php foreach ($lesSalles as $salle): ?>
            <th><?= htmlspecialchars($salle) ?></th>
        <?php endforeach; ?>
    </tr>
    </thead>
    <tbody>
    <?php foreach ($horaires as $index => $horaire): ?>
        <tr>
            <td><?= htmlspecialchars($horaire) ?></td>
            <?php foreach ($salles[$index] as $cellule): ?>
                <?php if (($currentHour >= $horaire) && (isset($horaires[$index + 1]) && ($currentHour) < $horaires[$index + 1])): ?>
                    <td style="background-color: lightskyblue;"><?= htmlspecialchars($cellule) ?></td>
                <?php else: ?>
                    <td><?= htmlspecialchars($cellule) ?></td>
                <?php endif; ?>
            <?php endforeach; ?>
        </tr>
    <?php endforeach; ?>
    </tbody>
</table>



<script src="../Model/JavaScript/GenererPDF.js"></script>

<footer class="footer">
    <p>&copy; 2024 - SAE Emploi du temps. Rémi | Dorian | Matthéo | Bastien | Noah.</p>
</footer>

</body>
</html>
