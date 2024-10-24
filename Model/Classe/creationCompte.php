<?php
include "../../Controller/ConnectionBDD.php";

if (isset($_FILES['fichier']) && $_FILES['fichier']['error'] === UPLOAD_ERR_OK) {
    $nomFichier = $_FILES['fichier']['name'];

    // Vérifier si c'est un fichier CSV
    if ($_FILES['fichier']['type'] === 'text/csv' || pathinfo($nomFichier, PATHINFO_EXTENSION) === 'csv') {
        echo "Le fichier est un CSV.<br>";
        $lecture = fopen($_FILES['fichier']['tmp_name'], "r");

        if ($lecture !== FALSE) {//test si le fichier est lisable
            try {
                $conn = getConnectionBDDEDTIdentification();
                $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

                while (($res = fgetcsv($lecture, 1000, ";")) !== FALSE) {
                    $nom = $res[1];
                    $prenom = $res[2];
                    echo $nom.'<br>';
                    // Vérification de l'existence de la ligne dans la BDD
                    $sql1 = $conn->prepare("SELECT COUNT(*) FROM etudiants WHERE nom = :nom AND prenom = :prenom");
                    $sql1->bindParam(':nom', $nom);
                    $sql1->bindParam(':prenom', $prenom);
                    $sql1->execute();

                    if ($sql1->fetchColumn() == 0 && ($nom != "nom")) { // Pas de doublon
                        echo 'Insertion des valeurs.<br>';
                        $insertStmt = $conn->prepare("INSERT INTO etudiants (civilite, nom, prenom, semestre, nom_ressource, email) VALUES (:civilite, :nom, :prenom, :semestre, :nom_ressource, :email)");
                        $insertStmt->execute([
                            'civilite' => $res[0],
                            'nom' => $nom,
                            'prenom' => $prenom,
                            'semestre' => $res[3], // Assurez-vous que l'index est correct
                            'nom_ressource' => $res[4], // Assurez-vous que l'index est correct
                            'email' => $res[5] // Assurez-vous que l'index est correct
                        ]);
                    } else {
                        echo "Le doublon existe déjà pour $nom $prenom.<br>";
                    }
                }

                fclose($lecture);
            } catch (PDOException $e) {
                echo "Erreur de base de données : " . $e->getMessage();
            }
        } else {
            echo "Impossible de lire le fichier.";
        }
    } else {
        echo "Le fichier n'est pas au format CSV.";
    }
} else {
    echo "Erreur lors du téléchargement du fichier.";
}
?>
