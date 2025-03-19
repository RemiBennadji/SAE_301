<?php

ini_set('display_errors', 1);
error_reporting(E_ALL);

include_once "../Model/Classe/Compte.php";
include_once "../Model/Classe/Etudiant.php";
include_once "ConnectionBDD.php";

session_start();
// Vérification si le rôle est défini, sinon rediriger vers la page de connexion
    if (isset($_SESSION['role'])) {
        if($_COOKIE['role'] != 'administrateur'){
            header("Location: ./Deconnexion.php"); // Redirection si pas de rôle
            exit();
        }
    }
    $count = 0;
    if (isset($_FILES['fichier']) && $_FILES['fichier']['error'] === UPLOAD_ERR_OK) {
        $nomFichier = $_FILES['fichier']['name'];

        // Vérifier si c'est un fichier CSV
        if ($_FILES['fichier']['type'] === 'text/csv' || pathinfo($nomFichier, PATHINFO_EXTENSION) === 'csv') {
            $lecture = fopen($_FILES['fichier']['tmp_name'], "r");

            if ($lecture !== FALSE) {//test si le fichier est lisable
                try {
                    $conn = getConnectionBDD();
                    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

                    while (($res = fgetcsv($lecture, 1000, ";")) !== FALSE) {
                        $nom = $res[1];
                        $prenom = $res[2];
                        // Vérification de l'existence de l'étudiant dans la BDD
                        $sql1 = verifEtu($nom, $prenom);// $sql1 = ("SELECT COUNT(*) FROM etudiants WHERE nom = :nom AND prenom = :prenom");
                        //echo json_encode($sql1->fetchColumn());

                        if ($sql1->fetchColumn() ==0){
                            if ($nom != "nom") { // Test si le nom de la ligne n'est pas égal à "nom" pour le titre du csv
                                insertStmt($res,$nom,$prenom); //("INSERT INTO etudiants (civilite, nom, prenom, semestre, nom_ressource, email) VALUES (:civilite, :nom, :prenom, :semestre, :nom_ressource, :email)");
                                $etu = new Etudiant();
                                $etu->setPrenom($prenom);
                                $etu->setNom($nom);
                                $etu->setMail($res[5]);
                                $etu->insererDonnees();
                                $count++;
                            }
                        }
                    }
                    fclose($lecture);
                } catch (PDOException $e) {
                    echo "Erreur de base de données : " . $e->getMessage();
                }
            }
        }
        echo json_encode([
            'success' => true,
            'count' => $count]);
    }else{
        echo json_encode([
            'success' => false,
            'message' => 'Aucun fichier CSV valide n\'a été téléchargé.'
        ]);
    }


?>