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
<a href="../../Controller/EDTprof.php"><img src="../Ressource/logouphf2.png" class="logoUPHF" alt="Logo UPHF"></a>
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
      <li><a class="underline-animation" href="messagerie.html">Messagerie</a></li>
      <li><a class="underline-animation" href="creationCompte.html">Créer un compte</a></li>
      <li><a class="underline-animation" href="../../Controller/Deconnexion.php">Deconnexion</a></li>
    </ul>
  </nav>
</header>
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

</body>
</html>