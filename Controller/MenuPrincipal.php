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

<header>
    <nav>
        <div class="burger">
            <span></span>
            <span></span>
            <span></span>
        </div>
        <ul class="menu">
            <li><a id="edt" class="underline-animation" href="../Controller/EDT.php">Emploi du temps</a></li>
            <li><a class="underline-animation" href="#">Messagerie</a></li>
            <li><a class="underline-animation" href="../View/HTML/creationCompte.html" id="creationCompte" style="display: none">Créer un compte</a></li>
            <li><a class="underline-animation" href="../Controller/affichageSalleCurrent.php" id="afficheSalles" style="display: none">Salles disponibles</a></li>
            <li><a class="underline-animation" href="../Controller/Deconnexion.php">Déconnexion</a></li>
            <label class="choixClasse">
                <select id="edtAdmin">
                    <option selected disabled>Emploi du temps</option>
                    <option class="label" disabled>Anee 1</option>
                    <option value="A1">A1</option>
                    <option value="A2">A2</option>
                    <option value="B1">B1</option>
                    <option value="B2">B2</option>
                    <option value="C1">C1</option>
                    <option value="C2">C2</option>
                    <option class="label" disabled>Anee 2</option>
                    <option value="FIA1">FIA1</option>
                    <option value="FIA2">FIA2</option>
                    <option value="2FIB">FIB</option>
                    <option value="2FA">FA</option>
                    <option class="label" disabled>Anee 3</option>
                    <option value="FIA">FIA</option>
                    <option value="FIB">FIB</option>
                    <option value="FA">FA</option>
                    <option class="label" disabled>MPH</option>
                    <option value="MPH">MPH</option>
                </select>
            </label>
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
    <p>&copy; 2024 - SAE Emploi du temps. Rémi | Dorian | Matthéo | Bastien | Noah.</p>
</footer>
</body>
</html>

