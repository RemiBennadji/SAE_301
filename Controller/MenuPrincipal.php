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
    <link href="../View/CSS/menuPrincipalAdmin.css" rel="stylesheet">
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
            <li><a class="underline-animation" href="../View/HTML/affichageSalle.html" id="afficheSalles" style="display: none">Salles disponibles</a></li>
            <li><a class="underline-animation" href="../Controller/Deconnexion.php">Déconnexion</a></li>
        </ul>
    </nav>
</header>

<script><!-- script pour que les liens href soi responsive -->
    const burger = document.querySelector('.burger');
    const menu = document.querySelector('.menu');

    burger.addEventListener("click", () => {
        menu.classList.toggle("active");
        burger.classList.toggle("toggle");
    });
</script>

<br><br>

<div class="container_test"><!-- container avec les inputs de l'identient, password et button submit -->
    <div class="red-box">
        <form>
            <h2>Inscription</h2>
            <label for="username">Nom d'utilisateur:</label>
            <input type="text" id="username" name="username" required><br>

            <label for="email">Email:</label>
            <input type="email" id="email" name="email" required><br>

            <label for="password">Mot de passe:</label>
            <input type="password" id="password" name="password" required><br>

            <button type="submit">S'inscrire</button>
        </form>
    </div>

    <div class="blue-box">
        <a href="change_password.html">Changer de mot de passe</a>
    </div>
</div>

<script src="../Model/JavaScript/MenuPrincipal.js"></script>

<script>afficherElement("<?php echo $_SESSION['role']; ?>");</script>

<footer class="footer">
    <p>&copy; 2024 - SAE Emploi du temps. Rémi | Dorian | Matthéo | Bastien.</p>
</footer>
</body>
</html>