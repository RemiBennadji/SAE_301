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

echo "<pre>";
print_r($_POST);  // Affiche toutes les données POST reçues
echo "</pre>";

if($type == "secretariat"){
    $pers = new Secretariat($nom, $prenom);
}elseif ($type == "etudiant"){
    $pers = new Etudiant($nom, $prenom);
}elseif ($type == "professeur"){
    $pers = new Professeur($nom, $prenom);
}elseif ($type == "administrateur"){
    $pers = new Administrateur($nom, $prenom);
}

$id = $pers->genererIdentifiant();
echo "L'identifiant généré : ".$id."<br>";
?>

<form>
    <label for="pwd"> Définir un mot de passe </label><br>
    <h4>Le mot de passe doit contenir minimum 8 caractères, 1 chiffre et un caractère spécial</h4>
    <input type="text" id="pwd" name="pwd"><br>
</form>
