<html lang="fr">
<head>
    <!-- Titre de la page -->
    <title>EDT</title>
    <!-- Lien vers la feuille de style CSS de base -->
    <link rel="stylesheet" type="text/css" href="../CSS/CSSBasique.css">
</head>
<body>
<!-- Logo avec un lien vers la page EDT.php -->
<a href="EDT.php"><img src="../../Ressource/logouphf2.png" class="logoUPHF" alt="Logo UPHF"></a>

<!-- Inclusion de bibliothèques JavaScript pour la génération de PDF -->
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

<!-- Script pour faire fonctionner le menu burger (affichage mobile) -->
<script>
    const burger = document.querySelector('.burger');
    const menu = document.querySelector('.menu');
    burger.addEventListener("click", () => {
        menu.classList.toggle("active");
        burger.classList.toggle("toggle");
    });
</script>

<br><br>

<?php
// Inclusion des fichiers nécessaires
include "../../Controller/ConnectionBDD.php";
require_once "../../Model/Classe/EdtQuotiClass.php";
require_once "../../Model/Classe/Edt.php";



// Création d'un objet Edt
$edt = new EdtQuotiClass();

// Vérification des droits d'accès
if (isset($_SESSION['role']) && $_SESSION['role'] == 'professeur') {
    header("Location: ./EDTprof.php");
    exit();
}

// Vérification des cookies nécessaires
if (!isset($_COOKIE['groupe'])) {
    header("Location: Deconnexion.php");
    exit();
}

$classeActuel = $_COOKIE['groupe'];

if (!isset($_COOKIE['annee'])) {
    echo "Le cookie 'annee' n'est pas défini.";
    exit();
}

$anneeActuel = $_COOKIE['annee'];

// Vérification du cookie 'version'
$version = isset($_COOKIE["version"]) ? $_COOKIE["version"] : "default";

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

$dateActuel = $dateActuelle->format('Y-m-d');

// Affichage de l'interface
echo '
<div class="changerSemaine">
<form action="EDT.php" method="post">
    <br><br><button type="submit" class="quotidien">Vue hebdomadaire</button><br><br>
</form>
</div>
';

echo '<div class="changerSemaine">
    <br><br><button id="download-pdf" class="btn">Télécharger en PDF</button><br><br>
    <form action="edtQuotidien.php" method="post">
        <button type="submit" name="precedent" class="fleche">Précédent</button>

        <label for="selectionnerSemaine">Semaine du</label>
        <input type="date" id="selectionnerSemaine" name="dateSelection" onchange="this.form.submit()"
               value="' . htmlspecialchars($dateActuelle->format('Y-m-d'), ENT_QUOTES, 'UTF-8') . '">

        <button type="submit" name="suivant" class="fleche">Suivant</button>
    </form>
</div>';


// Affichage du groupe et de l'année
echo '<div class="big-container3"><div class="sub-container3"><label>'." Groupe : " . $classeActuel . " | Année : " . $anneeActuel .'</label></div></div>';

// Affichage du footer
echo '<footer class="footer">
    <p>&copy; 2024 - SAE Emploi du temps. Rémi | Dorian | Matthéo | Bastien | Noah.</p>
</footer>';

// Affichage de l'emploi du temps
$edt->AfficherEdtJour($dateActuel, $classeActuel, $anneeActuel, $version);
?>

<!-- Inclusion de scripts pour le menu, le calendrier et la génération de PDF -->
<script src="../../Model/JavaScript/MenuPrincipal.js"></script>
<script>afficherElement("<?php echo $_SESSION['role']; ?>");</script>
<script src="../../Model/JavaScript/CalendrierEDT.js"></script>
<script src="../../Model/JavaScript/GenererPDF.js"></script>
</body>
</html>
