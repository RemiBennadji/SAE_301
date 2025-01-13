<?php
session_start();

// Vérification si le rôle est défini, sinon rediriger vers la page de connexion
if (!isset($_SESSION['role'])) {
    header("Location: ../View/HTML/Identification.html"); // Redirection si pas de rôle
    exit();
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Menu Principal Admin</title>
    <link href="../View/CSS/CSSBasique.css" rel="stylesheet">
</head>
<body>
<a href="MenuPrincipal.php"><img src="../Ressource/logouphf2.png" class="logoUPHF" alt="Logo UPHF"></a>

<!-- Menu hamburger -->
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
            <li><a class="underline-animation" href="../View/HTML/messagerie.html">Messagerie</a></li>
            <li><a class="underline-animation" href="../View/HTML/creationCompte.html" id="creationCompte" style="display: none">Créer un compte</a></li>
            <li><a class="underline-animation" href="../Controller/EDTsalleLibres.php" id="afficheSalles">Salles disponibles</a></li>
            <li><a class="underline-animation" href="../Controller/Deconnexion.php">Déconnexion</a></li>
            <label class="choixClasse" id="choixClasse" style="display: none">
                <select id="edtAdmin" class="edtAdmin">
                    <option selected disabled>Administration</option>
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
        </ul>
    </nav>
</header>

<script><!-- script pour que le menu soit responsive -->
    const burger = document.querySelector('.burger');
    const menu = document.querySelector('.menu');

    burger.addEventListener("click", () => {
        menu.classList.toggle("active");
        burger.classList.toggle("toggle");
    });
</script>

<br><br>



<script src="../Model/JavaScript/MenuPrincipal.js"></script>

<script>afficherElement("<?php echo $_SESSION['role']; ?>");</script>

<footer class="footer">
    <p>&copy; 2024 - SAE Emploi du temps. Rémi | Dorian | Matthéo | Bastien | Noah.</p>
</footer>
</body>
</html>

