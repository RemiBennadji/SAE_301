<?php
include "ConnectionBDD.php";

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
    $version = 38;

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
        JOIN schedule USING (code, typeseance, typeformation, nomgroupe, semestre, noseance)
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
    $heure = substr($i['horaire'], 11, 5);
    $salle = 'Salle ' . $i['salle'] . ' (' . $i['enseignant'] . ')';
    $duree = substr($i['duree'], 22, 1) . ":" . substr($i['duree'], 30, 2);

    if (!isset($sallesParHoraire[$heure])) {
        $sallesParHoraire[$heure] = [];
    }

    if ((substr($duree, 0, 1) === "3")) {
        $h = 1 + (int)substr($heure, 0, 2);
        $m = 30 + (int)substr($heure, 3, 2);

        if ($m == 60) {
            $m = 0;
            $h++;
        }

        if (strlen($m) == 1) {
            $m .= "0";
        }

        $sallesParHoraire["$h:$m"][$i['salle']] = $salle;
    }
    $sallesParHoraire[$heure][$i['salle']] = $salle;
}

// Horaires et salles prédéfinis
$horaires = ['08:00', '09:30', '11:00', '12:30', '14:00', '15:30', '17:00', '18:30'];
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
    $nouveauJour = strtotime("+1 day", $timestamp);
    return date("Y-m-d", $nouveauJour);
}

function decrementerSemaine($ancienneDate) {
    $timestamp = strtotime($ancienneDate);
    $nouveauJour = strtotime("-1 day", $timestamp);
    return date("Y-m-d", $nouveauJour);
}

if ($_SERVER["METHOD"] == "POST") {
    $dateActuel = $_POST["date2"] ?? $dateActuelle;

    if (isset($_POST["precedent"])) {
        $dateActuel = decrementerSemaine($dateActuel);
    }

    if (isset($_POST["suivant"])) {
        $dateActuel = incrementerSemaine($dateActuel);
    }
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>EDT Salle</title>
    <link rel="stylesheet" type="text/css" href="../View/CSS/CSSBasique.css">
</head>
<body>

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

<!--<div class="changerJour">-->
<!--    <form action="EDTsalleLibres.php" method="post">-->
<!--        <label for="date">Changer la date :</label>-->
<!--        <input type="date" id="date" name="date" value="--><?php //= htmlspecialchars($dateActuelle->format('Y-m-d')) ?><!--" onchange="this.form.submit()">-->
<!--    </form>-->
<!--</div>-->




<div class="changerJour">
    <form action="EDTsalleLibres.php" method="post">
        <input type="hidden" name="date2" value="<?= htmlspecialchars($dateActuelle->format('Y-m-d')) ?>">
        <button type="submit" name="precedent"><</button>
        <label>Date du jour : <?= htmlspecialchars($dateDuJour) ?></label>
        <button type="submit" name="suivant">></button>


        <label for="date">Calendrier :</label>
        <input type="date" id="date" name="date" value="<?= htmlspecialchars($dateActuelle->format('Y-m-d')) ?>" onchange="this.form.submit()">
    </form>
</div>

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

<footer class="footer">
    <p>&copy; 2024 - SAE Emploi du temps. Rémi | Dorian | Matthéo | Bastien | Noah.</p>
</footer>

</body>
</html>