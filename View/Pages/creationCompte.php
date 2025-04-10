<!DOCTYPE html>
<html lang="fr">


<head>
    <meta charset="UTF-8">
    <title>Création d'un compte</title>
    <link href="../CSS/CSSBasique.css" rel="stylesheet">
</head>
<body>
<a href="EDT.php"><img src="../../Ressource/logouphf2.png" class="logoUPHF" alt="Logo de l'UPHF"></a>
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
            <li><a id="edtCours" class="underline-animation" href="EDTmatiereSelection.php">EDT Ressource</a></li>
            <li><a class="underline-animation" href="EDTsalleLibres.php" id="afficheSalles">Salles disponibles</a></li>
            <li><a id="tableauEtudiant" class="underline-animation" href="VoireEtudiant.php">Listes Étudiants</a></li>
            <li><a id="tableauAbsence" class="underline-animation" href="TableauAbsence.php">Tableau Absence</a></li>
            <li><a id="tableauReport" class="underline-animation" href="TableauReport.php">Tableau Report</a></li>
            <li><a class="underline-animation" href="creationCompte.php" id="creationCompte">Créer un compte</a></li>
            <li><a id ="valideEDT" class="underline-animation" href="ValideEdt.php">ValideEDT</a></li>
            <li><a class="underline-animation" href="../../Controller/Deconnexion.php">Déconnexion</a></li>
        </ul>
    </nav>
</header>

<br><br>
<div class="big-container">
    <div class="sub-container">
        <form class="form-import" action="../../Controller/creationCompte.php" method="post" enctype="multipart/form-data">
            <div class="upload-file">
                <h1>Importation du CSV</h1><br><br><br>
                <div class="button-upload">
                    <input class ="select-file" type="file" name="fichier" id="fichier" accept=".csv" required>
                    <button type="submit">Uploader</button>
                </div><br>
                <p id="feedback" style="display: none"></p>
            </div>
        </form>
    </div>
</div>


<footer class="footer">
    <p>&copy; 2024 - SAE Emploi du temps. Rémi | Dorian | Matthéo | Bastien | Noah.</p>
</footer>


<script src="../../Model/JavaScript/MenuPrincipal.js"></script>
<script src="../../Model/JavaScript/menuHamburger.js"></script>
</body>
</html>