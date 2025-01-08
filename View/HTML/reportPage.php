<?php
include "../../Controller/report.php";
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Demander un report</title>
  <link href="../CSS/CSSBasique.css" rel="stylesheet">
    <?php if ($message != ""): ?>
        <meta http-equiv="refresh" content="3; url=../../Controller/EDT.php"> <!-- Remplacez par la page où vous souhaitez rediriger l'utilisateur -->
    <?php endif; ?>
</head>
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

<div class="body-blur <?= $message != "" ? 'show' : '' ?>"></div>

<?php if ($message != ""): ?>
    <div class="messageReport <?= $typeMess ?>">
        <?= $message ?>
        <? header('Location: ../../Controller/EDT.php') ?>
    </div>
<?php endif; ?>

<form action="" method="post">
  <label for="dateReport">Date du cours</label><br>
  <input type="date" id="dateReport" name="dateReport" required placeholder=" "><br>
  <label for="sujet">Raison</label><br>
  <input type="text" id="sujet" name="sujet" required placeholder="Pourquoi ?"><br>
  <input type="submit" value="Valider">
</form>

</body>
</html>