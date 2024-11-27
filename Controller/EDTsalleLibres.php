<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>EDT Salle</title>
    <link rel="stylesheet" type="text/css" href="../View/CSS/CSSBasique.css">
</head>
<body>

<!-- Lien vers le menu principal avec logo -->
<a href="MenuPrincipal.php"><img src="../Ressource/logouphf2.png" class="logoUPHF" alt="Logo UPHF"></a>

<header>
    <!-- Menu de navigation -->
    <nav>
        <div class="burger">
            <span></span>
            <span></span>
            <span></span>
        </div>
        <ul class="menu">
            <li><a class="underline-animation" href="affichageSalleCurrent.php">Salles libres actuelles</a></li>
            <li><a class="underline-animation" href="../View/HTML/affichageSalle.html">Choisir horaire salles libres</a></li>
            <li><a class="underline-animation" href="../Controller/EDT.php">Emploi du temps</a></li>
            <li><a class="underline-animation" href="#">Messagerie</a></li>
            <li><a class="underline-animation" href="../View/HTML/creationCompte.html" id="creationCompte" style="display: none">Créer un compte</a></li>
            <li><a class="underline-animation" href="../Controller/Deconnexion.php">Déconnexion</a></li>
        </ul>
    </nav>
</header>

<!-- Script pour l'animation du menu burger -->
<script>
    const burger = document.querySelector('.burger');
    const menu = document.querySelector('.menu');
    burger.addEventListener("click", () => {
        menu.classList.toggle("active");
        burger.classList.toggle("toggle");
    });
</script>

<?php
// Connexion à la base de données
include "ConnectionBDD.php";

session_start();
// Vérification si le rôle est défini, sinon rediriger vers la page de connexion
if (!isset($_SESSION['role'])) {
    header("Location: ../View/HTML/Identification.html"); // Redirection si pas de rôle
    exit();
}

// Récupérer la date actuelle et naviguer par jour
if (isset($_POST['suivant'])) {
    $dateActuelle = new DateTime($_POST['date']); // Si la date est postée, on prend cette date
    $dateActuelle->modify('+1 day'); // Ajouter un jour
} elseif (isset($_POST['precedent'])) {
    $dateActuelle = new DateTime($_POST['date']); // Si la date est postée, on prend cette date
    $dateActuelle->modify('-1 day'); // Soustraire un jour
} else {
    $dateActuelle = new DateTime(); // Si aucune date n'est fournie, on prend la date du jour
}

// Format de la date du jour (ex : "01/01/2025")
$dateDuJour = $dateActuelle->format('d/m/Y');

// Récupération des horaires et salles depuis la base de données
try {
    $version = 38;
    $horaire = $dateActuelle->format('Y-m-d'); // La date au format "YYYY-MM-DD"

    // Connexion à la base de données
    $connection = getConnectionBDD();
    $sql = "SELECT distinct salle, horaire
            FROM schedulesalle
            JOIN schedule 
                USING(code, typeseance, typeformation, noseance, semestre, version)
            WHERE date(horaire) = :horaire and version = :version
            ORDER BY horaire";

    $resultSalles = $connection->prepare($sql);
    $resultSalles->bindParam(':version', $version);
    $resultSalles->bindParam(':horaire', $horaire);
    $resultSalles->execute();

    // Stockage des résultats dans un tableau
    $listeSalles = $resultSalles->fetchAll(PDO::FETCH_ASSOC);

} catch (PDOException $e) {
    echo $e->getMessage();
}

// Organiser les salles par horaire
$sallesParHoraire = [];
foreach ($listeSalles as $i) {
    $heure = substr($i['horaire'], 11, 5);  // Extraction de l'heure "HH:MM"

    // Ajout de la salle à l'heure qui correspond
    if (!isset($sallesParHoraire[$heure])) {
        $sallesParHoraire[$heure] = [];
    }
    $sallesParHoraire[$heure][] = 'Salle ' . $i['salle'];
}

// Liste des horaires prédéfinis
$horaires = ['08:00', '09:30', '11:00', '12:30', '14:00', '15:30', '17:00'];

// Liste des salles disponibles
$lesSalles = ['101', '103', '105', '106', '107', '108', '109', '110', '111', '112', '113', '114', '115', '117', '118', '200'];

// Organiser les résultats pour chaque horaire, avec des salles vides
$resultat = [];
foreach ($horaires as $horaire) {
    if (isset($sallesParHoraire[$horaire])) {
        // Si des salles sont assignées à cet horaire, les ajouter
        $resultat[] = $sallesParHoraire[$horaire];
    } else {
        // Si aucune salle n'est assignée, remplir avec des chaînes vides pour chaque salle
        $resultat[] = array_fill(0, count($lesSalles), '');
    }
}

$salles = $resultat;
?>

<!-- Section pour changer de jour -->
<div class="changerJour">
    <form action="EDTsalleLibres.php" method="post">
        <!-- Cacher la date dans un champ caché -->
        <input type="hidden" name="date" value="<?php echo $dateActuelle->format('Y-m-d'); ?>">

        <button type="submit" name="precedent"><</button>
        <label> Date du jour : <?php echo $dateDuJour; ?> </label>
        <button type="submit" name="suivant">></button>
    </form>
</div>

<!-- Tableau de l'emploi du temps -->
<table>
    <thead>
    <tr>
        <th>Heure</th>
        <!-- Affiche les salles -->
        <?php foreach ($lesSalles as $Salle): ?>
            <th><?php echo htmlspecialchars($Salle); ?></th>
        <?php endforeach; ?>
    </tr>
    </thead>
    <tbody>
    <!-- Remplir le tableau avec les horaires et les salles -->
    <?php foreach ($horaires as $indexHoraire => $horaire): ?>
        <tr>
            <td><?php echo htmlspecialchars($horaire); ?></td>
            <?php foreach ($lesSalles as $indexSalle => $Salle): ?>
                <td>
                    <?php
                    // Vérifier si la salle est présente pour cet horaire
                    if (in_array('Salle ' . $Salle, $salles[$indexHoraire])) {
                        echo 'Salle ' . $Salle;
                    } else {
                        echo ''; // Case vide si la salle n'est pas disponible
                    }
                    ?>
                </td>
            <?php endforeach; ?>
        </tr>
    <?php endforeach; ?>
    </tbody>
</table>

<!-- Footer -->
<footer class="footer">
    <p>&copy; 2024 - SAE Emploi du temps. Rémi | Dorian | Matthéo | Bastien | Noah.</p>
</footer>

</body>
</html>