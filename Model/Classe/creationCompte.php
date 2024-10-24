<?php
include "../../Controller/ConnectionBDD.php";

if (isset($_FILES['fichier']) && $_FILES['fichier']['error'] === UPLOAD_ERR_OK) {// Vérifier si le fichier a été téléchargé
    $nomFichier = $_FILES['fichier']['name'];

    // Vérifier si c'est un fichier CSV
    if ($_FILES['fichier']['type'] === 'text/csv' || pathinfo($nomFichier, PATHINFO_EXTENSION) === 'csv') {
        echo "ficier est un csv.<br>";
        // Lire le fichier CSV
        $lecture = fopen($_FILES['fichier']['tmp_name'], "r");
        if ($lecture !== FALSE) {
            echo 'lecture fichier.<br>';
            try {
                $conn = getConnectionBDDEDTIdentification();
                $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
                $testEmpty = 'select count(*) from etudiants';
                echo $testEmpty.'<br>';

                if($testEmpty->fetchColumn()==0){ //Si la table est vide
                    echo 'table vide.<br>';
                    $liste = [];
                    while (($res = fgetcsv($lecture, 1000, ",")) !== FALSE) {
                        $liste[] = $res; // Ajouter chaque ligne du CSV dans le tableau
                        echo htmlspecialchars($res[0]) . "<br>"; // Affiche la première colonne de chaque ligne pour vérifier
                    }
                } else{
                    echo 'table pas vide.<br>';
                    while (($res = fgetcsv($lecture, 1000, ",")) !== FALSE) {
                        $nom = $res[2];
                        $prenom = $res[3];

                        // Vérification ligne existant dans BDD
                        $sql1 = $conn->prepare("SELECT COUNT(*) FROM etudiants WHERE nom = :nom AND prenom = :prenom");
                        echo $sql1;
                        $sql1->bindParam(':nom', $nom);
                        $sql1->bindParam(':prenom', $prenom);
                        $sql1->execute(['nom' => $nom, 'prenom' => $prenom]);

                        if ($sql1->fetchColumn() == 0) { // Pas de doublon
                            echo 'insert des valeur.<br>';
                            $insertStmt = $conn->prepare("INSERT INTO etudiants (id, civilite, nom, prenom, semestre, nom_ressource, email) VALUES (:id, , :civilite, :nom, :prenom, :semestre, :email)");
                            $insertStmt->execute(['id'=>($id=$res[0]),'civilite'=>($civilite=$res[1]), 'nom' => $nom, 'prenom' => $prenom, 'semestre'=>($semestre=$res[2]), 'nom_ressource'=> ($nom_ressource=$res[3]), 'email'=>($email= $res[4])]);
                        }
                    }
                }

            fclose($lecture);
            } catch (PDOException $e) {
                echo $e->getMessage();
            }
        }else {
            echo "Impossible de lire le fichier.";
        }
    } else {
        echo "Le fichier n'est pas au format CSV.";
    }
} else {
    echo "Erreur lors du téléchargement du fichier.";
}

try {
    $conn = getConnectionBDDEDTIdentification();
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    // Ouvre le fichier téléchargé
    $fichierETU = fopen($_FILES['fichier']['tmp_name'], 'r');

    while (($data = fgetcsv($fichierETU, 1000, ',')) !== FALSE) {
        $nom = $data[2];
        $prenom = $data[3];

        // Vérification ligne existant dans BDD
        $stmt = $conn->prepare("SELECT COUNT(*) FROM etudiants WHERE nom = :nom AND prenom = :prenom");
        $stmt->bindParam(':nom', $nom);
        $stmt->bindParam(':prenom', $prenom);
        $stmt->execute(['nom' => $nom, 'prenom' => $prenom]);

        if ($stmt->fetchColumn() == 0) { // Pas de doublon
            $insertStmt = $conn->prepare("INSERT INTO etudiants (id, civilite, nom, prenom, semestre, nom_ressource, email) VALUES (:id, , :civilite, :nom, :prenom, :semestre, :email)");
            $insertStmt->execute(['id'=>($id=$data[0]),'civilite'=>($civilite=$data[1]), 'nom' => $nom, 'prenom' => $prenom, 'semestre'=>($semestre=$data[2]), 'nom_ressource'=> ($nom_ressource=$data[3]), 'email'=>($email= $data[4])]);
        }
    }

    fclose($fichierETU);
    echo "Importation terminée.";
} catch (PDOException $e) {
    echo "Erreur : " . $e->getMessage();
}
?>
