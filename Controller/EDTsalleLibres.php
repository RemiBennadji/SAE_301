<?php
include "ConnectionBDD.php";

/*
session_start();
if (!isset($_SESSION['role'])) {
    header("Location: ../View/HTML/Identification.html");
    exit();
}
*/

// Récupération de la date et gestion des boutons de navigation
if (isset($_POST['suivant'])) {
    $dateActuelle = new DateTime($_POST['date']);
    $dateActuelle->modify('+1 day');
} elseif (isset($_POST['precedent'])) {
    $dateActuelle = new DateTime($_POST['date']);
    $dateActuelle->modify('-1 day');
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
    $heure = substr($i['horaire'], 11, 5);//Extrait l'heure
    $salle = 'Salle ' . $i['salle'] . ' (' . $i['enseignant'] . ')';
    $duree = substr($i['duree'], 22, 1).":".substr($i['duree'], 30, 2);//Extrait la duree

    if((substr($duree, 0, 1) === "3") || (substr($heure, 3, 1) === "3")) {
        $h = 1 + (int)substr($heure, 0, 2);
        $m = 30 + (int)substr($heure, 3, 2);
        //echo "$h:$m|";

        // Gestion des dépassements de minutes
        if($m == 60) {
            $m = 0; // Retirer 60 minutes
            $h++;     // Ajouter 1 heure
        }

        //Si les minutes est de taille 1 (9h0 devient 9h00
        if(strlen($m) == 1){
            $m = $m."0";
        }

        $sallesParHoraire["$h:$m"][$i['salle']] = $salle;
        //echo "$h:$m|";
    }

    //chaque salle est bien assignée à sa propre colonne dans le tableau final à l'affichage
    if (!isset($sallesParHoraire[$heure])) {//Vérifie si un tableau existe déjà pour l'horaire
        $sallesParHoraire[$heure] = [];
    }
    $sallesParHoraire[$heure][$i['salle']] = $salle;
}

// Horaires prédéfinis
$horaires = ['08:00', '09:30', '11:00', '12:30', '14:00', '15:30', '17:00', '18h30'];
$lesSalles = ['101', '103', '105', '106', '107', '108', '109', '110', '111', '112', '113', '114', '115', '117', '118', '200'];

// Création de la structure du tableau final
$resultat = [];
foreach ($horaires as $horaire) {
    $ligne = [];
    foreach ($lesSalles as $salle) {
        $ligne[] = $sallesParHoraire[$horaire][$salle] ?? ''; // Ajouter la salle ou une case vide
    }
    $resultat[] = $ligne;
}
$salles = $resultat;
?>

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

<div class="changerJour">
    <form action="EDTsalleLibres.php" method="post">
        <input type="hidden" name="date" value="<?= htmlspecialchars($dateActuelle->format('Y-m-d')) ?>">
        <button type="submit" name="precedent"><</button>
        <label>Date du jour : <?= htmlspecialchars($dateDuJour) ?></label>
        <button type="submit" name="suivant">></button>
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
                <td><?= htmlspecialchars($cellule) ?></td>
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
