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


<br><br>

<?php
// Inclusion des fichiers nécessaires pour la connexion à la base de données et la gestion de l'emploi du temps
include "../../Controller/ConnectionBDD.php";
require_once "../../Model/Classe/Edt.php";

//ini_set('display_errors', 1);
//ini_set('display_startup_errors', 1);
//error_reporting(E_ALL);

// Création d'un objet Edt pour gérer l'emploi du temps
$edt = new Edt();

// Démarrage de la session pour gérer les variables utilisateur
session_start();

// Vérification si le rôle est défini, sinon redirection vers la page de connexion
if (isset($_SESSION['role'])) {
    if($_COOKIE['role'] == 'professeur'){
        header("Location: ./EDTprof.php"); // Redirection si pas de rôle
        exit();
    }
}

// Vérification si le cookie 'groupe' existe
if (isset($_COOKIE['groupe'])) {
    $classeActuel = $_COOKIE['groupe'];
} else {
    header("Location: Deconnexion.php"); // Redirection si pas de rôle
}

// Vérification si le cookie 'annee' existe
if (isset($_COOKIE['annee'])) {
    $anneeActuel = $_COOKIE['annee'];
} else {
    echo "Le cookie 'annee' n'est pas défini."; // Message d'erreur si le cookie 'annee' n'existe pas
}

// Calcul de la date du début de la semaine (lundi)
$dateActuel = date('Y-m-d', strtotime('monday this week'));

// Gestion des actions POST, comme la sélection de la date ou le changement de semaine
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    if (isset($_POST["selectedDate"])) {
        // Traitement de la date sélectionnée et ajustement pour le lundi de la semaine
        $selectedDate = new DateTime($_POST["selectedDate"]);
        $dayOfWeek = $selectedDate->format('N'); // 1 (lundi) à 7 (dimanche)
        $daysToSubtract = $dayOfWeek - 1;
        $selectedDate->sub(new DateInterval("P{$daysToSubtract}D"));
        $dateActuel = $selectedDate->format('Y-m-d');
    } else {
        $dateActuel = $_POST["dateActuel"] ?? $dateActuel; // Utilisation de la date actuelle si aucune date n'est sélectionnée
    }
}

//echo '
//<div class="changerSemaine">
//<form action="edtQuotidien.php" method="post">
//    <br><br><button type="submit" class="quotidien">Vue quotidienne </button>
//</form>
//</div>
//<br><br>
//';

// Affichage de la partie permettant de changer la semaine, incluant un calendrier
echo '<div class="changerSemaine">
    <br>
    <div class="divBouton">
        <form action="edtQuotidien.php" method="post">
            <br><br><button type="submit" class="quotidien">Vue quotidienne </button>
        </form>
        <button id="download-pdf" class="btn">Télécharger en PDF</button>
    </div>
    <br><br><form method="post">
        <button type="button" id ="precedent" name="precedent" class="fleche">Précédent</button>
        
        <label for="selectionnerSemaine">Semaine du</label>
        <input type="date" id="selectionnerSemaine" name="selectedDate" 
               value="' . htmlspecialchars($dateActuel, ENT_QUOTES, 'UTF-8') . '">
        <input type="hidden" name="role" value="' . $_SESSION["role"] . '">
        <input type="hidden"  name="dateActuel" 
               value="' . htmlspecialchars($dateActuel, ENT_QUOTES, 'UTF-8') . '">
        
        <button type="button" id="suivant" name="suivant" class="fleche">Suivant</button>
    </form>
</div>';

// Affichage du groupe et de l'année choisis par l'administrateur via les cookies
echo '<div class="big-container3"><div class="sub-container3"><label>'." Groupe : " . $_COOKIE["groupe"] . " | Année : " . $_COOKIE["annee"] .'</label></div></div>';

// Affichage du footer avec les auteurs du projet
echo ('<footer class="footer">
    <p>&copy; 2024 - SAE Emploi du temps. Rémi | Dorian | Matthéo | Bastien | Noah.</p>
</footer>');

?>

<div id="edtContainer"> <?php $edt->AfficherEdtSemaine($dateActuel, $classeActuel, $anneeActuel,$_COOKIE["version"]) ?></div>



<!-- Inclusion de scripts pour le menu, le calendrier et la génération de PDF -->
<script src="../../Model/JavaScript/MenuPrincipal.js"></script>
<script src="../../Model/JavaScript/menuHamburger.js"></script>
<script>afficherElement("<?php echo $_SESSION['role']; ?>");</script>
<script src="../../Model/JavaScript/CalendrierEDT.js"></script>
<script src="../../Model/JavaScript/GenererPDF.js"></script>
</body>
</html>
