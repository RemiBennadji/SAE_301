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
    header("Location: ../View/HTML/Identification.html"); // Redirection si pas de rôle
    exit();
}

date_default_timezone_set('Europe/Paris');//Fuseau horaire

// Connexion à la base de données
try {
    $connexion = getConnectionBDD();
    $sql = "
        SELECT *
        FROM demande
        ORDER BY datedemande desc
        LIMIT 20";

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
    echo "<table>
        <thead>
            <tr>
                <th>Nom</th>
                <th>Prenom</th>
                <th>Date demande</th>
                <th>Raison</th>
                <th>Typedemande</th>
            </tr>
        </thead>
        <tbody>";
    foreach ($data as $ligne) {
        echo "<tr>
            <td>" . htmlspecialchars($ligne['nom']) . "</td>
            <td>" . htmlspecialchars($ligne['prenom']) . "</td>
            <td>" . htmlspecialchars($ligne['datedemande']) . "</td>
            <td>" . htmlspecialchars($ligne['raison']) . "</td>
            <td>" . htmlspecialchars($ligne['typedemande']) . "</td>";
    }
    echo "</tbody>
    </table>";
}

// Appeler la fonction pour générer le tableau des absences
genererTableau($listeReport, "Liste des reports");
?>

<footer class="footer"><p>&copy; 2024 - SAE Emploi du temps. Rémi | Dorian | Matthéo | Bastien | Noah.</p></footer>

<script src="../Model/JavaScript/ValideEdt.js"></script>
<script src="../Model/JavaScript/MenuPrincipal.js"></script>
<script>afficherElement("<?php echo $_SESSION['role'] ?>")</script>
<script src="../Model/JavaScript/CalendrierEDT.js"></script>
<script src="../Model/JavaScript/GenererPDF.js"></script>
</body>
</html>
