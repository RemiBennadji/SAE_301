<?php
//include "../Controller/ConnectionBDD.php";

session_start();
//
//// Vérification si le rôle est défini, sinon rediriger vers la page de connexion
//if (!isset($_SESSION['role'])) {
//    header("Location: ../View/HTML/Identification.html"); // Redirection si pas de rôle
//    exit();
//}
//?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>affichageSalle</title>
    <link href="../View/CSS/CSSBasique.css" rel="stylesheet">
</head>
<body>
<a href="../../Controller/MenuPrincipal.php"><img src="../../Ressource/logouphf2.png" class="logoUPHF" alt="Logo de l'UPHF"></a>
<header>
    <nav>
        <div class="burger">
            <span></span>
            <span></span>
            <span></span>
        </div>
        <ul class="menu">
            <li><a class="underline-animation" href="../../Controller/affichageSalleCurrent.php">Salles libres actuelles</a></li>
            <li><a class="underline-animation" href="../../Controller/EDTsalleLibres.php">Emploi du temps des salles libres</a></li>
            <li><a class="underline-animation" href="../../Controller/EDT.php">Emploie du temps</a></li>
            <li><a class="underline-animation" href="#">Messagerie</a></li>
            <li><a class="underline-animation" href="creationCompte.html">Créer un compte</a></li>
            <li><a class="underline-animation" href="../../Controller/Deconnexion.php">Deconnexion</a></li>
        </ul>
    </nav>
</header>
<script>
    const burger = document.querySelector('.burger');
    const menu = document.querySelector('.menu');

    burger.addEventListener('click', () => {
    menu.classList.toggle('active');
    burger.classList.toggle('toggle');
});
</script>

<br><br><br><br><br>
<div class="big-container">
<div class="sub-container">
<form action="EDTmatiere.php" method="POST">
    <h1><u>Entrez le nom de la ressource</u></h1><br><br><br>
    <label class="choixClasse"></label>
    <input type="text" name="codeRessource" placeholder="Code de la ressource"><br><br>
    <input class="buttonSeConfirmer" type="submit" value="Confirmer">
</form>
</div>
</div>
<br><br>

<!--<div class="horaireActuel">-->
<!--    <a class="salleLibreAncre" href="../../Controller/affichageSalleCurrent.php"><button class="buttonMDRForgotten">Horaire actuel</button></a>-->
<!--</div>-->

<footer class="footer">
    <p>&copy; 2024 - SAE Emploi du temps. Rémi | Dorian | Matthéo | Bastien | Noah.</p>
</footer>
</body>
</html>