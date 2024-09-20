<?php
echo "test";

require_once "Compte.php";
require_once "Administrateur.php";
require_once "Etudiant.php";
require_once "Professeur.php";
require_once "Secretariat.php";
echo "test";
$type = $_POST["type"];
$nom = $_POST["nom"];
$prenom = $_POST["prenom"];
$errorMessage = "";
echo "<pre>";
print_r($_POST);  // Affiche toutes les données POST reçues
echo "</pre>";

if($type == "secretariat"){
    $pers = new Secretariat();
}elseif ($type == "etudiant"){
    $pers = new Etudiant();
}elseif ($type == "professeur"){
    $pers = new Professeur();
}elseif ($type == "administrateur"){
    $pers = new Administrateur();
}
$pers->setNom($nom);
$pers->setPrenom($prenom);
$id = $pers->genererIdentifiant();
echo "L'identifiant généré : ".$id."<br>";

$pwd = $_POST["pwd"];
if($pers->verifMdp($pwd)){
    $pers->setMdp($pwd);
    echo "Mot de passe défini avec succès.";
} else {
    $errorMessage = "Le mot de passe ne respecte pas les critères.";
}
if ($errorMessage !== "" || $_SERVER["REQUEST_METHOD"] !== "POST") {

    if ($errorMessage !== "") {
        echo "<div style='color: red;'>$errorMessage</div>";
    }
}?>
    <head>
        <link href="../View/creationCompteStyle.css" rel="stylesheet">
    </head>
    <form method="post" action="">
        <div class="input-container">
            <input type="text" class="input" id="pwd" name="pwd" placeholder=" ">
            <label for="pwd" class="placeholder">Mot de passe</label>
        </div>
        <input type="submit" value="Soumettre">
    </form>