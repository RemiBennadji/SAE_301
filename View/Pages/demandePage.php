<?php
include "../../Controller/demande.php";

// Connexion à la base de données
try {
    $connexion = getConnectionBDD();
    $sql = "SELECT nom, prenom from collegue join infoutilisateur using (mail);";

    // Utiliser la date actuelle pour la requête
    $nomPrenomProf = $connexion->prepare($sql);
    $nomPrenomProf->execute();

    $listeReport = $nomPrenomProf->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    echo "Erreur : " . $e->getMessage();
    exit;
}
echo $listeReport['nom'];
echo $listeReport['prenom'];
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Demander un report</title>
  <link href="../CSS/CSSBasique.css" rel="stylesheet">
</head>
<a href="EDTprof.php"><img src="../../Ressource/logouphf2.png" class="logoUPHF" alt="Logo UPHF"></a>
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
            <li><a id="edtProf" class="underline-animation" href="EDTprof.php" style="display: none">EDT Professeur</a></li>
            <li><a id="edtCours" class="underline-animation" href="EDTmatiereSelection.php" style="display: none">EDT Ressource</a></li>
            <li><a class="underline-animation" href="EDTsalleLibres.php" id="afficheSalles">Salles disponibles</a></li>
            <li><a id="tableauEtudiant" class="underline-animation" href="VoireEtudiant.php" style="display: none">Listes Étudiants</a></li>
            <li><a id="tableauAbsence" class="underline-animation" href="TableauAbsence.php" style="display: none">Tableau Absence</a></li>
            <li><a id="tableauReport" class="underline-animation" href="TableauReport.php" style="display: none">Tableau Report</a></li>
            <li><a class="underline-animation" href="demandePage.php" id="demande" style="display: none">Faire une demande</a></li>
            <li><a class="underline-animation" href="creationCompte.php" id="creationCompte" style="display: none">Créer un compte</a></li>
            <li><a id ="valideEDT" class="underline-animation" href="ValideEdt.php" style="display: none">ValideEDT</a></li>
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

<body>

<form action="../../Controller/demande.php" method="post" class="form-demande">

    <label for="typeDemande">Type de votre demande : </label>
    <select id="typeDemande" name="typeDemande">
        <option value="Report">Report</option>
        <option value="Contrainte">Contrainte</option>
    </select><br>

    <label for="dateReport">Date du cours</label><br>
    <input type="date" id="dateReport" name="dateReport" required placeholder=" "><br>

    <label for="heureReport">Heure de début du cours</label><br>
    <input type="time" id="heureStartReport" name="heureStartReport" required placeholder=" "><br>


    <label for="heureReport">Heure de fin du cours</label><br>
    <input type="time" id="heureEndReport" name="heureEndReport" required placeholder=" "><br>

    <label for="sujet">Raison</label><br>
    <input type="text" id="sujet" name="sujet" required placeholder="Pourquoi ?"><br>

    <input type="hidden" name="nom" value="<?php $listeReport['nom'] ?>">
    <input type="hidden" name="prenom" value="<?php $listeReport['prenom'] ?>">

    <input type="submit" value="Valider">
</form>

<script src="../../Model/JavaScript/DemandePage.js"></script>
<script src="../../Model/JavaScript/MenuPrincipal.js"></script>
<script src="../../Model/JavaScript/menuHamburger.js"></script>
<script>afficherElement("<?php echo $_SESSION['role']; ?>");</script>
</body>
</html>