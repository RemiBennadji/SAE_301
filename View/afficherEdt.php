<head>
    <title>EDT</title>
    <link rel="stylesheet" type="text/css" href="edt.css">
</head>

<?php

include "../Model/ConnectionBDD.php";

// Exemple + Test
$dateActuel = '2025-01-06';
$classeActuel = 'TDC';

/*
echo 'La semaine d avant : '.decrementerSemaine($dateActuel)."<br>";
echo 'La semaine actuel : '.$dateActuel."<br>";
echo 'La semaine d apres : '.incrementerSemaine($dateActuel)."<br>";**/

function AfficherEdtSemaine($dateDebut, $classe){
    // Conversion de la date de début en timestamp
    $timestamp = strtotime($dateDebut);

    // On s'assure que la date est bien au format YYYY-MM-DD (ex : lundi)
    $lundi = date("Y-m-d", $timestamp);

    // Tableau HTML pour afficher l'emploi du temps
    echo "<table>";
    echo "<tr><th>Heure</th><th>Lundi</th><th>Mardi</th><th>Mercredi</th><th>Jeudi</th><th>Vendredi</th></tr>";

    // Liste des horaires pour chaque jour
    $listeHorraire = ['08:00', '09:30', '11:00', '12:30', '14:00', '15:30', '17:00'];

    // Boucle sur chaque horaire
    foreach ($listeHorraire as $horaire) {
        echo "<tr>";
        // Affichage de l'heure dans la première colonne
        echo "<td>$horaire</td>";

        // Boucle pour chaque jour de la semaine (lundi à vendredi)
        for ($i = 0; $i < 5; $i++) {
            // Calcul de la date du jour (du lundi au vendredi)
            $jourTimestamp = strtotime("+$i day", strtotime($lundi));
            $jour = date("Y-m-d", $jourTimestamp);

            // Récupération du cours pour cette date et cette heure
            $cours = RecupererCours($jour, $horaire, $classe);

            // Affichage du cours (ou vide si pas de cours)
            if ($cours) {
                echo "<td>$cours</td>";
            } else {
                echo "<td></td>";
            }
        }
        echo "</tr>";
    }

    echo "</table>";
}

// Fonction pour récupérer un cours pour un jour et une heure donnés
function RecupererCours($jour, $horaire, $classe){
    // Concaténer la date et l'heure
    $dateTime = $jour . ' ' . $horaire . ':00'; // Format YYYY-MM-DD HH:MM:SS

    // Requête SQL avec l'ancienne structure, incluant plus d'informations
    $sql = "
    SELECT DISTINCT seance.idseance, seance.typeseance, duree, schedule.salle, collegue.prenom, collegue.nom, enseignement.court as matiere, horaire as date, schedule.nomgroupe
    FROM seance
    JOIN collegue ON seance.collegue = collegue.id
    JOIN enseignement ON seance.code = enseignement.code
    JOIN schedule ON seance.nomgroupe = schedule.nomgroupe
    WHERE horaire = ? AND schedule.nomgroupe = ?
    LIMIT 1";

    // Connexion à la base de données
    $connexion = getConnectionBDD();


    // Préparation de la requête avec les paramètres
    $req = $connexion->prepare($sql);
    $req->execute([$dateTime, $classe]);

    // Récupération du résultat
    $cours = $req->fetch(PDO::FETCH_ASSOC);

    // Si un cours est trouvé, on retourne les informations formatées sur plusieurs lignes
    if ($cours) {
        return $cours['typeseance'] . "<br>" .
            $cours['matiere'] . "<br>" .
            $cours['prenom'] . " " . $cours['nom'] . "<br>" .
            "Salle " . $cours['salle'];
    } else {
        // Si aucun cours, on retourne null
        return null;
    }
}

function incrementerSemaine($ancienneDate){
    // Formatage des dates et incrémentation d'une semaine
    $timestamp = strtotime($ancienneDate);
    $lundiActuel = date("Y-m-d", $timestamp);
    $nouveauLundi = strtotime("+7 day", strtotime($lundiActuel));
    return date("Y-m-d", $nouveauLundi);
}

function decrementerSemaine($ancienneDate){
    // Formatage des dates et décrementation d'une semaine
    $timestamp = strtotime($ancienneDate);
    $lundiActuel = date("Y-m-d", $timestamp);
    $nouveauLundi = strtotime("-7 day", strtotime($lundiActuel));
    return date("Y-m-d", $nouveauLundi);
}

echo('<img src="https://upload.wikimedia.org/wikipedia/commons/b/bd/UPHF_logo.svg" alt="Logo UPHF" width=20% height=20%"/>');

echo ('<h3> EDT </h3> <div class="changerSemaine"> 
    <form action="afficherEdt.php" method="post">
        <button type="submit" name="precedent"><</button>
    <label>Semaine du'.' '.$dateActuel.'</label>
    <button type="submit" name="suivant">></button>
</form>
</div>');

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Si le bouton semaine précédent a était pressé
    if(isset($_POST["precedent"])){
        $dateActuel = decrementerSemaine($dateActuel);
        // Faire en sorte que la dateActuel change vraiment et non temporairement
        AfficherEdtSemaine($dateActuel, $classeActuel);
    }

    // Si le bouton semaine précédent a était pressé
    if(isset($_POST["suivant"])){
        echo $dateActuel;
        $dateActuel = incrementerSemaine($dateActuel);
        // Faire en sorte que la dateActuel change vraiment et non temporairement
        AfficherEdtSemaine($dateActuel, $classeActuel);
    }
}
// Si acuun bouton pressé, on affiche l'EDT courant
else{
    AfficherEdtSemaine($dateActuel, $classeActuel);
}