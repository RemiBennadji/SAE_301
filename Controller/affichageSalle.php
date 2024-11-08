<?php

include "ConnectionBDD.php";

//récupération des données du formulaire @Noah
$jour = $_POST["idJour"];
$heure = $_POST["idHeure"];

//Initialisation d'une variable qui nous permettra d'avoir l'horaire précedant l'horaire choisi par l'utilisateur @Noah
$heureInf = "00:00";

//Attribue l'heure précédente @Noah
if($heure == '9:30'){
    $heureInf = '8:00';
}elseif ($heure == '11:00'){
    $heureInf = '9:30';
}elseif ($heure == '14:00'){
    $heureInf = '12:30';
}elseif ($heure == '15:30'){
    $heureInf = '14:00';
}elseif ($heure == '17:00'){
    $heureInf = '15:30';
}
//convertion de la date au format timestamp @Noah
$timestamp = strtotime($jour);
$date = date("Y-m-d", $timestamp).' '.$heure.':00';
$dateInf = date("Y-m-d", $timestamp).' '.$heureInf.':00';

//requête permettant d'accéder aux salles utilisées à l'horaire saisi @Noah
$sql1 ="select distinct salle from schedule where horaire =:DATE";

//requête qui permet d'avoir toutes les salles @Noah
$sql2 ="select distinct nosalle from listesalles";

//requête pour avoir les salles utilisées pour 3h l'heure d'avant @Noah
$sql3 ="select distinct salle from schedule where horaire =:HEURE and duration='0 years 0 mons 0 days 3 hours 0 mins 0.0 secs'";

//liste qui va stocker les salles utilisées @Noah
$sallesAll = array();

//connexion BDD @Noah
try {
    $connection = getConnectionBDD();//Utilisation des informations de connexion entrer dans le fichier ConnexionBDD.php

    //execution de la requête 1 et ajoute les salles à la liste sallesAll @Noah
    $resultSalles = $connection->prepare($sql1);
    $resultSalles->bindParam(':DATE', $date);
    $resultSalles->execute();
    $listeSalles =$resultSalles->fetchAll(PDO::FETCH_ASSOC);

    //execution de la requête 3 et ajoute les salles utilisées pour 3h à celles d'avant @Noah
    $sallesInf = $connection->prepare($sql3);
    $sallesInf->bindParam(':HEURE', $dateInf);
    $sallesInf->execute();
    $salleInf = $sallesInf->fetchAll(PDO::FETCH_ASSOC);

    //récupère les salles utilisées pour 1h30 @Noah
    foreach ($listeSalles as $salle) {
        $sallesAll[] = $salle['salle'];
    }

    //récupère les salles utilisées pour 3h à l'heure d'avant @Noah
    foreach ($salleInf as $salleIndispo){
        $sallesAll[] = $salleIndispo['salle'];
    }

    //execution de la requête 2 et affiche les salles grâce à une comparaison avec sallesAll @Noah
    $salles = $connection->prepare($sql2);
    $salles->execute();
    $sallesDispo = $salles->fetchAll(PDO::FETCH_ASSOC);
    $sallesLibres = array();

    //permet d'avoir les salles libres en la comparant à la liste de salles utilisées @Noah
    foreach ($sallesDispo as $nosalle) {
        if(!in_array($nosalle['nosalle'], $sallesAll)) {
            $sallesLibres[] = $nosalle['nosalle'];
        }
    }

} catch (PDOException $e) {
    echo $e->getMessage();
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
            <li><a class="underline-animation" href="../Controller/EDT.php">Emploi du temps</a></li>
            <li><a class="underline-animation" href="#">Messagerie</a></li>
            <li><a class="underline-animation" href="../View/HTML/creationCompte.html" id="creationCompte" style="display: none">Créer un compte</a></li>
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

<br>

<div class="big-container">
    <div class="sub-container"><br>
        <h2>Les salles libres sont : </h2><br><br>
        <ul class="salles-libre">

            <?php
            foreach ($sallesLibres as $n) {
                echo '<li>' . htmlspecialchars($n) . '</li>'; // Utiliser htmlspecialchars pour éviter les problèmes de sécurité
            }
            ?>
        </ul>

    </div>
</div>


<footer class="footer">
    <p>&copy; 2024 - SAE Emploi du temps. Rémi | Dorian | Matthéo | Bastien.</p>
</footer>
</body>
</html>
