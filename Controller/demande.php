<?php
require "ConnectionBDD.php";
session_start();

if($_SERVER["REQUEST_METHOD"] == "POST") {
    //Récupération des données du formulaire @Noah
    $date = $_POST["date"];
    $date = strtotime($date); // Conversion nécessaire pour le format correct @Dorian
    $date = date("Y-m-d", $date);
    $heureDemande = $_POST["heure"];
    $raison = $_POST["sujet"];
    $type = $_POST["typeDemande"];
    $mail = $_SESSION["mail"];

    //Variable qui servira à afficher un message d'errreur ou de succès @Noah
    $message = "";
    $typeMess = "";

    //Requête pour récupérer nom et prénom du professeur @Noah
    $info = "SELECT nom, prenom FROM collegue WHERE mail = :MAIL";

    try {
        $conn = getConnectionBDD();

        //Requête préparée @Noah
        $getInfo = $conn->prepare($info);
        $getInfo->bindParam(":MAIL", $mail);
        $getInfo->execute();

        $row = $getInfo->fetch(PDO::FETCH_ASSOC);
        if ($row) {
            $nom = $row["nom"];
            $prenom = $row["prenom"];

            // Requête d'insertion avec la bonne syntaxe SQL @Dorian
            $sql = "INSERT INTO Demande (dateDemande, raison, nom, prenom, heureDemande, typeDemande) 
                    VALUES (:DATEDEMANDE, :RAISON, :NOM, :PRENOM, :HEUREDEMANDE, :TYPEDEMANDE)";

            //Insertion des données @Noah
            $insertion = $conn->prepare($sql);
            $insertion->bindParam(":DATEDEMANDE", $date);
            $insertion->bindParam(":RAISON", $raison);
            $insertion->bindParam(":NOM", $nom);
            $insertion->bindParam(":PRENOM", $prenom);
            $insertion->bindParam(":HEUREDEMANDE", $heureDemande);
            $insertion->bindParam(":TYPEDEMANDE", $type);
            $insertion->execute();

            //Indique un message de succès @Noah
            $message = "Votre demande a été envoyée avec succès !";
            $typeMess = "success";
        } else {
            // Message d'erreur si le professeur n'est pas trouvé @Dorian
            $message = "Professeur non trouvé";
            $typeMess = "error";
        }
    } catch (Exception $e) {
        //Indique un message d'erreur @Noah
        $message = $e->getMessage();
        $typeMess = "error";
    }
}

// Ajout de la partie affichage des absences @Dorian
try {
    $conn = getConnectionBDD();

    // Récupération des absences du jour courant @Dorian
    $dateAujourdhui = date("Y-m-d");
    $sql = "SELECT d.dateDemande, d.heureDemande, d.raison, d.nom, d.prenom, d.typeDemande 
            FROM Demande d 
            WHERE d.dateDemande = :DATE 
            ORDER BY d.heureDemande ASC";

    $stmt = $conn->prepare($sql);
    $stmt->bindParam(":DATE", $dateAujourdhui);
    $stmt->execute();

    // Affichage du message de status si présent @Dorian
    if (isset($message)) {
        echo "<div class='message " . $typeMess . "'>" . $message . "</div>";
    }

    // Construction du tableau HTML @Dorian
    echo "<h2>Professeurs absents aujourd'hui</h2>";
    echo "<table>
            <tr>
                <th>Nom</th>
                <th>Prénom</th>
                <th>Heure</th>
                <th>Raison</th>
                <th>Type d'absence</th>
            </tr>";

    while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
        echo "<tr>
                <td>" . htmlspecialchars($row['nom']) . "</td>
                <td>" . htmlspecialchars($row['prenom']) . "</td>
                <td>" . htmlspecialchars($row['heureDemande']) . "</td>
                <td>" . htmlspecialchars($row['raison']) . "</td>
                <td>" . htmlspecialchars($row['typeDemande']) . "</td>
            </tr>";
    }

    echo "</table>";

} catch (Exception $e) {
    // Affichage des erreurs potentielles lors de la récupération @Dorian
    echo "<div class='message error'>Erreur lors de la récupération des absences : " . $e->getMessage() . "</div>";
}
?>