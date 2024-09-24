<?php

require_once "Compte.php";
require_once "Administrateur.php";
require_once "Etudiant.php";
require_once "Professeur.php";
require_once "Secretariat.php";

$type = $_POST["type"];
$nom = $_POST["nom"];
$prenom = $_POST["prenom"];
$errorMessage = "";

if($type == "secretariat"){
    $pers = new Secretariat();
    $pers->setRole('secretariat');
}elseif ($type == "etudiant"){
    $pers = new Etudiant();
    $pers->setRole('etudiant');
}elseif ($type == "professeur"){
    $pers = new Professeur();
    $pers->setRole('professeur');
}elseif ($type == "administrateur"){
    $pers = new Administrateur();
    $pers->setRole('administrateur');
}

$pwd = $_POST["pwd"];
if($pers->verifMdp($pwd)){
    $pers->setMdp($pwd);
    $pers->setNom($nom);
    $pers->setPrenom($prenom);
    $id = $pers->genererIdentifiant();
    echo "L'identifiant généré : ".$id."<br>";
    $pers->insererDonnees();
    echo "Mot de passe défini avec succès.";

} else {
    echo "Le mot de passe ne respecte pas les critères.";
    echo '<a href="../View/creationCompte.html">Réessayer</a>';
}
?>
