<?php
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $fichier = $_FILES["fichier"]["name"];
    if (pathinfo($_FILES['fichier']['type'], PATHINFO_EXTENSION) === 'csv') {
        if ((fopen($_FILES['fichier']['type'], "r")) !== FALSE){//Ouverture du fichier

        }

    }
}
?>