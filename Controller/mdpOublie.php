<?php
include "BDD.php";

session_start();

if (!isset($_SESSION["id"])) {
    die("Erreur : Vous devez être connecté pour modifier votre compte.");
}

$id = $_POST["id"];
$pwd1 = $_POST['mdp'];
$pwd2 = $_POST['mdp2'];

try {
    $conn = getConnectionBDDEDTIdentification();

} catch (PDOException $e) {
    $erreur = "Erreur SQL : " . $e->getMessage();
}
?>