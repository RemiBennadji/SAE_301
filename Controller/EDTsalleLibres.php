<!DOCTYPE html>
<html>
<head>
    <title>EDT Salle</title>
    <link rel="stylesheet" type="text/css" href="../View/CSS/CSSBasique.css">
</head>
<body>
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

<?php
// Définir les horaires et jours
$horaires = ['08h00', '09h30', '11h00', '12h30', '14h00', '15h30', '17h00'];
$jours = ['Lundi', 'Mardi', 'Mercredi', 'Jeudi', 'Vendredi'];

$salles = [
    ['Salle 101', 'Salle 102', 'Salle 103', 'Salle 104', 'Salle 105'],//08h00
    ['Salle 201', 'Salle 202', 'Salle 203', 'Salle 204', 'Salle 205'],//09h30
    ['Salle 203'],//11h00
    ['Salle 301', 'Salle 302', 'Salle 303', 'Salle 304', 'Salle 305'],//12h30
    ['Salle 401', 'Salle 402', 'Salle 403', 'Salle 404', 'Salle 405'],//14h00
    ['Salle 501', 'Salle 502', 'Salle 503'],//15h30
    ['Salle 503']//17h00
];
?>

<div class="changerSemaine">
    <form action="EDT.php" method="post">
        <button type="submit" name="precedent"><</button>
        <label> Semaine du : 01/01/2025 </label>
        <button type="submit" name="suivant">></button>
    </form>
</div>

<table>
    <thead>
    <tr>
        <th>Heure</th>
        <?php foreach ($jours as $jour): ?>
            <th><?php echo htmlspecialchars($jour); ?></th>
        <?php endforeach; ?>
    </tr>
    </thead>
    <tbody>
    <?php foreach ($horaires as $indexHoraire => $horaire): ?>
        <tr>
            <td><?php echo htmlspecialchars($horaire); ?></td>
            <?php foreach ($jours as $indexJour => $jour): ?>
                <td>
                    <?php echo htmlspecialchars(isset($salles[$indexHoraire][$indexJour]) ? $salles[$indexHoraire][$indexJour] : ''); ?>
                </td>
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
