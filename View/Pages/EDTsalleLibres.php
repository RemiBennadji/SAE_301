<?php
include "../../Controller/ConnectionBDD.php";
require_once "../../Model/Classe/Edt.php";

$edt = new Edt();
session_start();


//Vérification si le rôle est défini, sinon rediriger vers la page de connexion
if (isset($_SESSION['role'])) {
    if($_COOKIE['role'] != 'administrateur' && $_COOKIE['role'] != 'secretariat' && $_COOKIE['role'] != 'professeur' && $_COOKIE['role'] != 'etudiant'){
        header("Location: ./Deconnexion.php"); // Redirection si pas de rôle
        exit();
    }
}


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
    $version = $_COOKIE["version"];

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
        RIGHT JOIN schedule USING (code, typeseance, nomgroupe, semestre, noseance)
        JOIN ressourcegroupe rg USING (nomgroupe, semestre)
        JOIN schedulesalle USING (code, typeseance, nomgroupe, semestre, noseance, version)
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
    <link rel="stylesheet" type="text/css" href="../CSS/CSSBasique.css">
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf/2.5.1/jspdf.umd.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf-autotable/3.5.22/jspdf.plugin.autotable.min.js"></script>
</head>
<body>
<a href="EDT.php"><img src="../../Ressource/logouphf2.png" class="logoUPHF" alt="Logo UPHF"></a>
<!-- Menu hamburger -->
<header>
    <!-- Menu de navigation principal -->
    <nav>
        <!-- Menu burger (pour affichage mobile) -->
        <div class="burger">
            <span></span>
            <span></span>
            <span></span>
        </div>
        <ul class="menu">
            <!-- Lien vers différentes sections du site, avec affichage conditionnel -->
            <li><a id="edtProf" class="underline-animation" href="EDTprof.php" style="display: none">EDT Professeur</a></li>
            <li><a id="edtCours" class="underline-animation" href="EDTmatiereSelection.php" style="display: none">EDT Ressource</a></li>
            <li><a class="underline-animation" href="EDTsalleLibres.php" id="afficheSalles">Salles disponibles</a></li>
            <li><a id="tableauEtudiant" class="underline-animation" href="VoireEtudiant.php" style="display: none">Listes Étudiants</a></li>
            <li><a id="tableauAbsence" class="underline-animation" href="TableauAbsence.php" style="display: none">Tableau Absence</a></li>
            <li><a id="tableauReport" class="underline-animation" href="TableauReport.php" style="display: none">Tableau Report</a></li>
            <li><a class="underline-animation" href="demandePage.php" id="demande" style="display: none">Faire une demande</a></li>
            <li><a class="underline-animation" href="creationCompte.php" id="creationCompte" style="display: none">Créer un compte</a></li>
            <li><a id ="valideEDT" class="underline-animation" href="ValideEdt.php" style="display: none">ValideEDT</a></li>
            <!-- Sélecteur d'année scolaire, affiché conditionnellement -->
            <label class="choixClasse" id="choixClasse" style="display: none">
                <select id="edtAdmin" class="edtAdmin">
                    <option selected disabled>Choisir Année</option>
                    <!-- Options pour l'année scolaire -->
                    <option class="label" disabled>Année 1</option>
                    <option value="A1">A1</option>
                    <option value="A2">A2</option>
                    <option value="B1">B1</option>
                    <option value="B2">B2</option>
                    <option value="C1">C1</option>
                    <option value="C2">C2</option>
                    <option class="label" disabled>Année 2</option>
                    <option value="FIA1">FIA1</option>
                    <option value="FIA2">FIA2</option>
                    <option value="2FIB">FIB</option>
                    <option value="2FA">FA</option>
                    <option class="label" disabled>Année 3</option>
                    <option value="FIA">FIA</option>
                    <option value="FIB">FIB</option>
                    <option value="FA">FA</option>
                </select>
            </label>
            <li><a class="underline-animation" href="../../Controller/Deconnexion.php">Déconnexion</a></li>
        </ul>
    </nav>
</header>


<!-- Affichage de la partie permettant de changer la journée, incluant un calendrier -->
<div class="changerSemaine">
    <br><br><button id="download-pdf" class="btn">Télécharger en PDF</button><br><br>
    <form action="EDTsalleLibres.php" method="post">
        <button type="submit" name="precedent" class="fleche">Précédent</button>

        <label for="selectionnerSemaine">Semaine du</label>
        <input type="date" id="selectionnerSemaine" name="dateSelection" onchange="this.form.submit()"
               value="<?= htmlspecialchars($dateActuelle->format('Y-m-d'), ENT_QUOTES, 'UTF-8') ?>">

        <button type="submit" name="suivant" class="fleche">Suivant</button>
    </form>
</div>

<table class="edtresponsive">
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
                <?php if (($heureCourante >= $horaire) && (isset($horaires[$index + 1]) && ($heureCourante < $horaires[$index + 1]) && ($dateActuelle->format('Y-m-d') == (new DateTime())->format('Y-m-d')))): ?>
                    <td style="background-color: lightskyblue;"><?= htmlspecialchars($cellule) ?></td>
                <?php else: ?>
                    <td><?= htmlspecialchars($cellule) ?></td>
                <?php endif; ?>
            <?php endforeach; ?>
        </tr>
    <?php endforeach; ?>
    </tbody>
</table>

<script src="../../Model/JavaScript/GenererPDF.js"></script>
<script src="../../Model/JavaScript/MenuPrincipal.js"></script>
<script src="../../Model/JavaScript/menuHamburger.js"></script>
<script>afficherElement("<?php echo $_SESSION['role']; ?>");</script>

<footer class="footer">
    <p>&copy; 2024 - SAE Emploi du temps. Rémi | Dorian | Matthéo | Bastien | Noah.</p>
</footer>

</body>
</html>
