<?php
if (isset($_FILES['fichier']) && $_FILES['fichier']['error'] === UPLOAD_ERR_OK) {// Vérifier si le fichier a été téléchargé
    $nomFichier = $_FILES['fichier']['name'];

    // Vérifier si c'est un fichier CSV
    if ($_FILES['fichier']['type'] === 'text/csv' || pathinfo($nomFichier, PATHINFO_EXTENSION) === 'csv') {
        // Lire le fichier CSV
        $lecture = fopen($_FILES['fichier']['tmp_name'], "r");
        if ($lecture !== FALSE) {
            $liste = [];
            $res = fgetcsv($lecture, 1000, ",");
            while ($res !== FALSE) {
                $liste[] = $res; // Ajouter chaque ligne du CSV dans le tableau
                echo htmlspecialchars($res[0]) . "<br>"; // Affiche la première colonne de chaque ligne pour vérifier
            }
            fclose($lecture);
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
