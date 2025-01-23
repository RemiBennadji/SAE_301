<?php
include "../../Controller/demande.php";
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Demander un report</title>
  <link href="../CSS/CSSBasique.css" rel="stylesheet">
</head>
<a href="../../Controller/EDTprof.php"><img src="../../Ressource/logouphf2.png" class="logoUPHF" alt="Logo UPHF"></a>
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
            <li><a id="edtProf" class="underline-animation" href="../../Controller/EDTprof.php" style="display: none">EDT Professeur</a></li>
            <li><a id="edtCours" class="underline-animation" href="../../Controller/EDTmatiereSelection.php" style="display: none">EDT Ressource</a></li>
            <li><a class="underline-animation" href="../../Controller/EDTsalleLibres.php" id="afficheSalles">Salles disponibles</a></li>
            <li><a id="tableauEtudiant" class="underline-animation" href="../../Controller/VoireEtudiant.php" style="display: none">Listes Étudiants</a></li>
            <li><a id="tableauAbsence" class="underline-animation" href="../../Controller/TableauAbsence.php" style="display: none">Tableau Absence</a></li>
            <li><a class="underline-animation" href="../../View/HTML/demandePage.php" id="demande" style="display: none">Faire une demande</a></li>
            <li><a class="underline-animation" href="../../View/HTML/creationCompte.html" id="creationCompte" style="display: none">Créer un compte</a></li>
            <li><a id ="valideEDT" class="underline-animation" href="../../Controller/ValideEdt.php" style="display: none">ValideEDT</a></li>
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

<!-- Script pour faire fonctionner le menu burger (affichage mobile) -->
<script>
    const burger = document.querySelector('.burger');
    const menu = document.querySelector('.menu');
    burger.addEventListener("click", () => {
        menu.classList.toggle("active");
        burger.classList.toggle("toggle");
    });
</script>
<body>

<form action="../../Controller/demande.php" method="post">
    <label for="typeDemande">Type de votre demande : </label>
    <select id="typeDemande" name="typeDemande">
        <option value="Report">Report</option>
        <option value="Contrainte">Contrainte</option>
    </select><br>
  <label for="dateReport">Date du cours</label><br>
  <input type="date" id="dateReport" name="dateReport" required placeholder=" "><br>
    <label for="heureReport">Heure du cours</label><br>
    <input type="time" id="heureReport" name="heureReport" required placeholder=" "><br>
  <label for="sujet">Raison</label><br>
  <input type="text" id="sujet" name="sujet" required placeholder="Pourquoi ?"><br>
  <input type="submit" value="Valider">
</form>

<script src="../../Model/JavaScript/MenuPrincipal.js"></script>
<script>afficherElement("<?php echo $_SESSION['role']; ?>");</script>
</body>
</html>