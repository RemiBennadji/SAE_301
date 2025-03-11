<?php
session_start();

$dateActuelle = new DateTime(); // Récupérer la date actuelle sous forme d'objet DateTime
$dateActuelle->modify('monday this week'); // Définir la date sur le lundi de la semaine actuelle

if (isset($_SESSION['role'])) {
    if($_COOKIE['role'] != 'administrateur' && $_COOKIE['role'] != 'secretariat') {
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
    $dateActuelle->modify('-1 day');
} elseif (isset($_POST['suivant'])) {
    $dateActuelle->modify('+1 day');
}

?>

<html lang="fr">
<head>
    <title>EDTAbsence</title>
    <link rel="stylesheet" type="text/css" href="../CSS/CSSBasique.css">
</head>
<body>
<a href="EDT.php"><img src="../../Ressource/logouphf2.png" class="logoUPHF" alt="Logo UPHF"></a>
<script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf/2.5.1/jspdf.umd.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf-autotable/3.5.22/jspdf.plugin.autotable.min.js"></script>
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


<div class="changerSemaine">
    <button id="download-pdf" class="btn">Télécharger en PDF</button>
    <form action="TableauAbsence.php" method="post">
        <button type="submit" name="precedent" class="fleche">Précédent</button>

        <label for="selectionnerSemaine">Jour du</label>
        <input type="date" id="selectionnerSemaine" name="dateSelection" onchange="this.form.submit()"
               value="<?= htmlspecialchars($dateActuelle->format('Y-m-d'), ENT_QUOTES, 'UTF-8') ?>">

        <button type="submit" name="suivant" class="fleche">Suivant</button>
    </form>
</div>
<br><br><br>

<?php
include "../../Controller/ConnectionBDD.php";
require_once "../../Model/Classe/Edt.php";

$edt = new Edt();

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);



if (!isset($_SESSION['role'])) {
    header("Location: ../View/HTML/Deconnexion.html"); // Redirection si pas de rôle
    exit();
}

date_default_timezone_set('Europe/Paris');//Fuseau horaire

// Connexion à la base de données
try {
    $connexion = getConnectionBDD();
    $sql = "
        SELECT absences.enseignement, absences.profs, absences.absence, absences.justification, absences.timestamp, absences.duree
        FROM absences
        WHERE DATE(absences.timestamp) = ?
        ORDER BY absences.timestamp";

    // Utiliser la date actuelle pour la requête
    $resAbsence = $connexion->prepare($sql);
    $resAbsence->execute([$dateActuelle->format('Y-m-d')]);

    $listeAbsences = $resAbsence->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    echo "Erreur : " . $e->getMessage();
    exit;
}

?>

<?php
function genererTableau($data, $titre) {
    echo "<h2>$titre</h2>";
    echo "<table class='tableauresponsive'>
        <thead>
            <tr>
                <th>Professeur</th>
                <th>Enseignement</th>
                <th>Type absence</th>
                <th>Justification</th>
                <th>Horaire</th>
                <th>Durée (min)</th>
            </tr>
        </thead>
        <tbody>";
    foreach ($data as $ligne) {
        echo "<tr>
            <td>" . htmlspecialchars($ligne['profs']) . "</td>
            <td>" . htmlspecialchars($ligne['enseignement']) . "</td>
            <td>" . htmlspecialchars($ligne['absence']) . "</td>";
        if ($ligne['justification'] == 'true') {
            echo "<td style='background-color: #5af45a;'>" . "Oui" . "</td>";
        } else {
            echo "<td style='background-color: #ff4141;'>" . "Non" . "</td>";
        }
        echo "<td>" . htmlspecialchars($ligne['timestamp']) . "</td>
            <td>" . htmlspecialchars($ligne['duree']) . "</td>
        </tr>";
    }
    echo "</tbody>
    </table>";
}

// Appeler la fonction pour générer le tableau des absences
genererTableau($listeAbsences, "Liste des absences");
?>

<footer class="footer"><p>&copy; 2024 - SAE Emploi du temps. Rémi | Dorian | Matthéo | Bastien | Noah.</p></footer>

<script src="../../Model/JavaScript/ValideEdt.js"></script>
<script src="../../Model/JavaScript/MenuPrincipal.js"></script>
<script>afficherElement("<?php echo $_SESSION['role'] ?>")</script>
<script src="../../Model/JavaScript/CalendrierEDT.js"></script>
<script src="../../Model/JavaScript/GenererPDF.js"></script>
<script defer src="../../Model/JavaScript/menuHamburger.js"></script>
</body>
</html>