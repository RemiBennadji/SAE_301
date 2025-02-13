<?php

ini_set('display_errors', 1);
error_reporting(E_ALL);

include_once "../Model/Classe/Compte.php";
include_once "../Model/Classe/Etudiant.php";

session_start();
// Vérification si le rôle est défini, sinon rediriger vers la page de connexion
if (isset($_SESSION['role'])) {
    if($_COOKIE['role'] != 'administrateur'){
        header("Location: ./Deconnexion.php"); // Redirection si pas de rôle
        exit();
    }
}

if (isset($_FILES['fichier']) && $_FILES['fichier']['error'] === UPLOAD_ERR_OK) {
    $nomFichier = $_FILES['fichier']['name'];

    // Vérifier si c'est un fichier CSV
    if ($_FILES['fichier']['type'] === 'text/csv' || pathinfo($nomFichier, PATHINFO_EXTENSION) === 'csv') {
        $lecture = fopen($_FILES['fichier']['tmp_name'], "r");

        if ($lecture !== FALSE) {//test si le fichier est lisable
            try {
                $conn = getConnectionBDD();
                $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

                while (($res = fgetcsv($lecture, 1000, ";")) !== FALSE) { //lecture du fichier CSV
                    $nom = $res[1];
                    $prenom = $res[2];
                    $sql1 = verifEtu(); // Vérification de l'existence de la ligne dans la BDD
                    if ($sql1->fetchColumn() == 0 && ($nom != "nom")) { // Test si la ligne existe deja dans la BDD et si le nom de la ligne n'est pas égal à nom
//                        $insertStmt = $conn->prepare("INSERT INTO etudiants (civilite, nom, prenom, semestre, nom_ressource, email) VALUES (:civilite, :nom, :prenom, :semestre, :nom_ressource, :email)");
//                        $insertStmt->execute([
//                            'civilite' => $res[0],
//                            'nom' => $nom,
//                            'prenom' => $prenom,
//                            'semestre' => $res[3],
//                            'nom_ressource' => $res[4],
//                            'email'=>$res[5]
//                        ]);
                        insertStmt($res,$nom,$prenom);
                        $etu = new Etudiant();
                        $etu->setPrenom($prenom);
                        $etu->setNom($nom);
                        $etu->setMail($res[5]);
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