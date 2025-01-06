<?php
include "ConnectionBDD.php";
require_once "../Model/Classe/Edt.php";

$edt = new Edt();
session_start();

// Vérification si le rôle est défini, sinon rediriger vers la page de connexion
if (!isset($_SESSION['role'])) {
    header("Location: ../View/HTML/Identification.html"); // Redirection si pas de rôle
    exit();
}

date_default_timezone_set('Europe/Paris');

// Gestion de la date actuelle ou sélectionnée
if (isset($_POST['dateSelection'])) {
    $dateActuelle = new DateTime($_POST['dateSelection']);
} else {
    $dateActuelle = new DateTime();
}

// Gestion de la navigation avec les flèches
if (isset($_POST['precedent'])) {
    $dateActuelle->modify('-1 days');
} elseif (isset($_POST['suivant'])) {
    $dateActuelle->modify('+1 days');
}

$dateDuJour = $dateActuelle->format('d/m/Y');
$horaire = $dateActuelle->format('Y-m-d');

try {
    $version = 39;

    // Requête pour récupérer les données
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
        RIGHT JOIN schedule USING (code, typeseance, typeformation, nomgroupe, semestre, noseance)
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
    if (!empty($i['horaire'])) {
        // Extrait l'heure et les minutes à partir de la valeur de 'horaire' (position 11 à 16)
        $heure = substr($i['horaire'], 11, 5); // Exemple : '2025-01-01 08:30:00' -> '08:30'
    } else {
        // Si 'horaire' est vide, attribue une chaîne vide
        $heure = '';
    }
    if (isset($i['salle'])) {
        $nomSalle = $i['salle'];
    } else {
        $nomSalle = 'Inconnue';
    }

    if (isset($i['enseignant'])) {
        $nomEnseignant = $i['enseignant'];
    } else {
        $nomEnseignant = 'Inconnu';
    }

    $salle = 'Salle ' . $nomSalle . ' (' . $nomEnseignant . ')';

    if (isset($i['salle']) && $i['salle'] != null) {
        $sallesParHoraire[$heure][$i['salle']] = $salle;
    } else {
        $sallesParHoraire[$heure]['Inconnue'] = $salle;
    }

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

$heureCourante = date('H:i', strtotime('+1 hour'));

?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>EDT Salle</title>
    <link rel="stylesheet" type="text/css" href="../View/CSS/CSSBasique.css">
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf/2.5.1/jspdf.umd.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf-autotable/3.5.22/jspdf.plugin.autotable.min.js"></script>
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
            <li><a class="underline-animation" href="../View/HTML/messagerie.html">Messagerie</a></li>
            <li><a class="underline-animation" href="../View/HTML/creationCompte.html" id="creationCompte" style="display: none">Créer un compte</a></li>
            <li><a class="underline-animation" href="../Controller/Deconnexion.php">Déconnexion</a></li>
        </ul>
    </nav>
</header>

<div class="changerSemaine">
    <button id="download-pdf" class="btn">Télécharger en PDF</button>
    <form action="EDTsalleLibres.php" method="post">
        <button type="submit" name="precedent">&lt;</button>

        <label for="selectionnerSemaine">Semaine du</label>
        <input type="date" id="selectionnerSemaine" name="dateSelection" onchange="this.form.submit()"
               value="<?= htmlspecialchars($dateActuelle->format('Y-m-d'), ENT_QUOTES, 'UTF-8') ?>">

        <button type="submit" name="suivant">&gt;</button>
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
                <?php if (($heureCourante >= $horaire) && (isset($horaires[$index + 1]) && ($heureCourante < $horaires[$index + 1]) && ($horaire == (new DateTime())->format('Y-m-d')))): ?>
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
