<?php

ini_set('display_errors', 1);
error_reporting(E_ALL);

include_once "../../Controller/ConnectionBDD.php";
include_once "Compte.php";
include_once "Etudiant.php";

if (isset($_FILES['fichier']) && $_FILES['fichier']['error'] === UPLOAD_ERR_OK) {
    $nomFichier = $_FILES['fichier']['name'];

    // Vérifier si c'est un fichier CSV
    if ($_FILES['fichier']['type'] === 'text/csv' || pathinfo($nomFichier, PATHINFO_EXTENSION) === 'csv') {
        $lecture = fopen($_FILES['fichier']['tmp_name'], "r");

        if ($lecture !== FALSE) {//test si le fichier est lisable
            try {
                $conn = getConnectionBDDEDTIdentification();
                $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

                while (($res = fgetcsv($lecture, 1000, ";")) !== FALSE) {
                    $nom = $res[1];
                    $prenom = $res[2];
                    // Vérification de l'existence de la ligne dans la BDD
                    $sql1 = $conn->prepare("SELECT COUNT(*) FROM etudiants WHERE nom = :nom AND prenom = :prenom");
                    $sql1->bindParam(':nom', $nom);
                    $sql1->bindParam(':prenom', $prenom);
                    $sql1->execute();
                    if ($sql1->fetchColumn() == 0 && ($nom != "nom")) { // Test si la ligne existe deja dans la BDD et si le nom de la ligne n'est pas égal à nom
                        $insertStmt = $conn->prepare("INSERT INTO etudiants (civilite, nom, prenom, semestre, nom_ressource) VALUES (:civilite, :nom, :prenom, :semestre, :nom_ressource, :email)");
                        $insertStmt->execute([
                            'civilite' => $res[0],
                            'nom' => $nom,
                            'prenom' => $prenom,
                            'semestre' => $res[3],
                            'nom_ressource' => $res[4]
                        ]);
                        $etu = new Etudiant();
                        $etu->setPrenom($prenom);
                        $etu->setNom($nom);
                        $etu->insererDonnees();

                    }
                }

                fclose($lecture);
            } catch (PDOException $e) {
                echo "Erreur de base de données : " . $e->getMessage();
            }
        }
    }
}
?>