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
    echo "Le mot de passe ne respecte pas les critères.";
}
