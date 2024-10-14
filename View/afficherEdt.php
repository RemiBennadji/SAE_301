<head>
    <title>EDT</title>
    <link rel="stylesheet" type="text/css" href="edt.css">
</head>

<?php
include "../Model/ConnectionBDD.php";

// Exemple + Test
$dateActuel = ' 2025-01-13';  // Date par défaut
$classeActuel = 'TPC1';       // Groupe par défaut (TPC1 en 1ère année)

// Fonction pour afficher l'emploi du temps de la semaine
function AfficherEdtSemaine($dateDebut, $classe) {
    // Conversion de la date de début en timestamp
    $timestamp = strtotime($dateDebut);

    // On s'assure que la date est bien au format YYYY-MM-DD (ex : lundi)
    $lundi = date("Y-m-d", $timestamp);

    // Titre de la semaine
    echo "<h3>Emploi du Temps - Semaine du " . date("d/m/Y", strtotime($lundi)) . "</h3>";

    // Tableau HTML pour afficher l'emploi du temps
    echo "<table>";
    echo "<tr><th>Heure</th>";

    // Liste des jours de la semaine et affichage des titres avec numéro de jour
    $joursSemaine = ['Lundi', 'Mardi', 'Mercredi', 'Jeudi', 'Vendredi'];
    for ($i = 0; $i < 5; $i++) {
        $jourTimestamp = strtotime("+$i day", strtotime($lundi));
        echo "<th>" . $joursSemaine[$i] . " " . date("d/m", $jourTimestamp) . "</th>";
    }
    echo "</tr>";

    // Liste des horaires pour chaque jour
    $listeHorraire = ['08:00', '09:30', '11:00', '12:30', '14:00', '15:30', '17:00'];

    // Boucle sur chaque horaire
    foreach ($listeHorraire as $horaire) {
        echo "<tr>";
        // Affichage de l'heure dans la première colonne, pour ne pas avoir de confusion d'horaire
        echo "<td style='vertical-align: top;'>$horaire</td>";

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

// Fonction pour retirer les accents et convertir en équivalents non accentués
function supprimerAccents($str) {
    return str_replace(
        ['é', 'è', 'ê', 'ë', 'à', 'â', 'ä', 'ù', 'û', 'ü', 'î', 'ï', 'ô', 'ö', 'ç', 'É', 'È', 'Ê', 'Ë', 'À', 'Â', 'Ä', 'Ù', 'Û', 'Ü', 'Î', 'Ï', 'Ô', 'Ö', 'Ç'],
        ['e', 'e', 'e', 'e', 'a', 'a', 'a', 'u', 'u', 'u', 'i', 'i', 'o', 'o', 'c', 'e', 'e', 'e', 'e', 'a', 'a', 'a', 'u', 'u', 'u', 'i', 'i', 'o', 'o', 'c'],
        $str
    );
}

// Fonction pour récupérer un cours pour un jour et une heure donnés
function RecupererCours($jour, $horaire, $classe) {
    $dateTime = $jour . ' ' . $horaire . ':00';
    $sql = "
       SELECT DISTINCT seance.idseance, seance.typeseance, duree, schedule.salle, collegue.prenom, collegue.nom, enseignement.court as matiere, enseignement.discipline, horaire as date, schedule.nomgroupe
       FROM seance
       JOIN collegue ON seance.collegue = collegue.id
       JOIN enseignement ON seance.code = enseignement.code
       JOIN schedule ON seance.nomgroupe = schedule.nomgroupe
       WHERE horaire = ?
         AND schedule.version = 20
         AND (
            schedule.nomgroupe = ?
            OR schedule.nomgroupe = 'CM'
            OR schedule.nomgroupe LIKE 'TD%'
         )
       LIMIT 1";

    $connexion = getConnectionBDD();
    $req = $connexion->prepare($sql);
    $req->execute([$dateTime, $classe]);

    $cours = $req->fetch(PDO::FETCH_ASSOC);

    if ($cours) {
        // Génération de la classe CSS basée sur la discipline et le type de séance
        $discipline = strtolower($cours['discipline']); // ex: 'Éco/Gestion'

        // Suppression des accents et des caractères non valides
        $discipline = supprimerAccents($discipline);
        $discipline = preg_replace('/[^a-z0-9]+/', '-', $discipline); // Remplacer les caractères non alphanumériques par des tirets
        $discipline = trim($discipline, '-'); // Supprimer les tirets en début et fin de chaîne

        $typeSeance = strtolower($cours['typeseance']); // ex: 'TD'

        // Générer une classe CSS
        $classeCSS = "cours-" . $discipline . "-" . $typeSeance;

        return "<div class='$classeCSS'>" .
            $cours['typeseance'] . "<br>" .
            $cours['matiere'] . "<br>" .
            $cours['prenom'][0] . ". " . $cours['nom'] . "<br>" .
            "Salle " . $cours['salle'] .
            "</div>";
    } else {
        return null;
    }
}

// Fonction pour incrémenter une semaine
function incrementerSemaine($ancienneDate) {
    $timestamp = strtotime($ancienneDate);
    $nouveauLundi = strtotime("+7 day", $timestamp);
    return date("Y-m-d", $nouveauLundi);
}

// Fonction pour décrémenter une semaine
function decrementerSemaine($ancienneDate) {
    $timestamp = strtotime($ancienneDate);
    $nouveauLundi = strtotime("-7 day", $timestamp);
    return date("Y-m-d", $nouveauLundi);
}

// Affichage du logo
echo('<img src="https://upload.wikimedia.org/wikipedia/commons/b/bd/UPHF_logo.svg" alt="Logo UPHF" width=10% height=10%"/>');

// Gestion des requêtes POST (navigation entre les semaines)
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Récupération de la date actuelle envoyée par le formulaire
    $dateActuel = isset($_POST["dateActuel"]) ? $_POST["dateActuel"] : $dateActuel;

    // Si le bouton semaine précédente est pressé
    if (isset($_POST["precedent"])) {
        $dateActuel = decrementerSemaine($dateActuel);
    }

    // Si le bouton semaine suivante est pressé
    if (isset($_POST["suivant"])) {
        $dateActuel = incrementerSemaine($dateActuel);
    }
}

// Affichage du titre et du formulaire de changement de semaine
echo ('<div class="changerSemaine"> 
    <form action="afficherEdt.php" method="post">
        <button type="submit" name="precedent"><</button>
        <label>Semaine du ' . date("d/m/Y", strtotime($dateActuel)) . '</label>
        <input type="hidden" name="dateActuel" value="'. $dateActuel .'">
        <button type="submit" name="suivant">></button>
    </form>
</div>');

// Affichage de l'emploi du temps pour la semaine choisie
AfficherEdtSemaine($dateActuel, $classeActuel);
?>