<html lang="fr">
<head>
    <title>VoireEtudiant</title>
    <link rel="stylesheet" type="text/css" href="../CSS/CSSBasique.css">
</head>
<body id="tableau" class="tableau">
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


<br><br><br>

<?php
include "../../Controller/ConnectionBDD.php";
require_once "../../Model/Classe/Edt.php";

$edt = new Edt();

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

// Vérifier si le cookie "groupe" existe
session_start();

//Vérification si le rôle est défini, sinon rediriger vers la page de connexion
if (isset($_SESSION['role'])) {
    if($_COOKIE['role'] != 'administrateur' && $_COOKIE['role'] != 'secretariat' && $_COOKIE['role'] != 'professeur'){
        header("Location: ./Deconnexion.php"); // Redirection si pas de rôle
        exit();
    }
}

date_default_timezone_set('Europe/Paris');//Fuseau horaire

// Connexion à la base de données
try {
    $connexion = getConnectionBDD();
    $sql = "select * from etudiants order by nom;";

    // Utiliser la date actuelle pour la requête
    $resReport = $connexion->prepare($sql);
    $resReport->execute();

    $listeReport = $resReport->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    echo "Erreur : " . $e->getMessage();
    exit;
}

?>

<?php
function genererTableau($data, $titre) {
    echo "<h2>$titre</h2>";
    echo "<table id='azerty' class='tableauresponsive'>
        <thead>
            <tr>
                <th>Nom</th>
                <th>Prenom</th>
                <th>Email</th>
            </tr>
        </thead>
        <tbody>";
    foreach ($data as $ligne) {
        echo "<tr>
            <td>" . htmlspecialchars($ligne['nom']) . "</td>
            <td>" . htmlspecialchars($ligne['prenom']) . "</td>
            <td>" . htmlspecialchars($ligne['email']) . "</td>";
    }
    echo "</tbody>
    </table>";
}

// Appeler la fonction pour générer le tableau des absences
genererTableau($listeReport, "Liste des etudiants");
?>

<footer class="footer"><p>&copy; 2024 - SAE Emploi du temps. Rémi | Dorian | Matthéo | Bastien | Noah.</p></footer>

<script src="../../Model/JavaScript/ValideEdt.js"></script>
<script src="../../Model/JavaScript/MenuPrincipal.js"></script>
<script src="../../Model/JavaScript/menuHamburger.js"></script>
<script>afficherElement("<?php echo $_SESSION['role']; ?>");</script>
<script src="../../Model/JavaScript/CalendrierEDT.js"></script>
<script src="../../Model/JavaScript/GenererPDF.js"></script>
</body>
</html>
