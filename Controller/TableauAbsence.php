<?php
$dateActuelle = new DateTime(); // Récupérer la date actuelle sous forme d'objet DateTime
$dateActuelle->modify('monday this week'); // Définir la date sur le lundi de la semaine actuelle

if (isset($_SESSION['role'])) {
    if($_COOKIE['role'] != 'administrateur'){
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
    <title>EDTValidation</title>
    <link rel="stylesheet" type="text/css" href="../View/CSS/CSSBasique.css">
</head>
<body>
<a href="EDT.php"><img src="../Ressource/logouphf2.png" class="logoUPHF" alt="Logo UPHF"></a>
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
            <li><a id="edtCours" class="underline-animation" href="../Controller/EDTmatiereSelection.php" style="display: none">EDT Ressource</a></li>
            <li><a id="edtProf" class="underline-animation" href="../Controller/EDTprof.php" style="display: none">EDT Professeur</a></li>
            <li><a id="edt" class="underline-animation" href="../Controller/EDT.php">Emploi du temps</a></li>
            <li><a class="underline-animation" href="#">Messagerie</a></li>
            <li><a class="underline-animation" href="../View/HTML/creationCompte.html" id="creationCompte" style="display: none">Créer un compte</a></li>
            <li><a class="underline-animation" href="../Controller/EDTsalleLibres.php" id="afficheSalles">Salles disponibles</a></li>
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

<div class="changerSemaine">
    <button id="download-pdf" class="btn">Télécharger en PDF</button>
    <form action="TableauAbsence.php" method="post">
        <button type="submit" name="precedent">&lt;</button>

        <label for="selectionnerSemaine">Jour du</label>
        <input type="date" id="selectionnerSemaine" name="dateSelection" onchange="this.form.submit()"
               value="<?= htmlspecialchars($dateActuelle->format('Y-m-d'), ENT_QUOTES, 'UTF-8') ?>">

        <button type="submit" name="suivant">&gt;</button>
    </form>
</div>
<br><br><br>

<?php
include "ConnectionBDD.php";
require_once "../Model/Classe/Edt.php";

$edt = new Edt();

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

// Vérifier si le cookie "groupe" existe
session_start();

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
    echo "<table>
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

<script src="../Model/JavaScript/ValideEdt.js"></script>
<script src="../Model/JavaScript/MenuPrincipal.js"></script>
<script>afficherElement("<?php echo $_SESSION['role'] ?>")</script>
<script src="../Model/JavaScript/CalendrierEDT.js"></script>
<script src="../Model/JavaScript/GenererPDF.js"></script>
</body>
</html>