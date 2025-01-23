<html lang="fr">
<head>
    <title>EDTProf</title>
    <link rel="stylesheet" type="text/css" href="../View/CSS/CSSBasique.css">
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf/2.5.1/jspdf.umd.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf-autotable/3.5.22/jspdf.plugin.autotable.min.js"></script>
</head>
<body>
<a href="EDTprof.php"><img src="../Ressource/logouphf2.png" class="logoUPHF" alt="Logo UPHF"></a>
<header>
    <!-- Menu de navigation principal -->
    <nav>
        <!-- Menu burger (pour affichage mobile) -->
        <div class="burger">
            <span></span>
            <span></span>
            <span></span>
        </div>
        <ul class="menu">
            <!-- Lien vers différentes sections du site, avec affichage conditionnel -->
            <li><a id="edtProf" class="underline-animation" href="../Controller/EDTprof.php" style="display: none">EDT Professeur</a></li>
            <li><a id="edtCours" class="underline-animation" href="../Controller/EDTmatiereSelection.php" style="display: none">EDT Ressource</a></li>
            <li><a class="underline-animation" href="../Controller/EDTsalleLibres.php" id="afficheSalles">Salles disponibles</a></li>
            <li><a id="tableauEtudiant" class="underline-animation" href="../Controller/VoireEtudiant.php" style="display: none">Listes Étudiants</a></li>
            <li><a id="tableauAbsence" class="underline-animation" href="../Controller/TableauAbsence.php" style="display: none">Tableau Absence</a></li>
            <li><a id="tableauReport" class="underline-animation" href="../Controller/TableauReport.php" style="display: none">Tableau Report</a></li>
            <li><a class="underline-animation" href="../View/HTML/demandePage.php" id="demande" style="display: none">Faire une demande</a></li>
            <li><a class="underline-animation" href="../View/HTML/creationCompte.php" id="creationCompte" style="display: none">Créer un compte</a></li>
            <li><a id ="valideEDT" class="underline-animation" href="../Controller/ValideEdt.php" style="display: none">ValideEDT</a></li>
            <!-- Sélecteur d'année scolaire, affiché conditionnellement -->
            <label class="choixClasse" id="choixClasse" style="display: none">
                <select id="edtAdmin" class="edtAdmin">
                    <option selected disabled>Choisir Année</option>
                    <!-- Options pour l'année scolaire -->
                    <option class="label" disabled>Année 1</option>
                    <option value="A1">A1</option>
                    <option value="A2">A2</option>
                    <option value="B1">B1</option>
                    <option value="B2">B2</option>
                    <option value="C1">C1</option>
                    <option value="C2">C2</option>
                    <option class="label" disabled>Année 2</option>
                    <option value="FIA1">FIA1</option>
                    <option value="FIA2">FIA2</option>
                    <option value="2FIB">FIB</option>
                    <option value="2FA">FA</option>
                    <option class="label" disabled>Année 3</option>
                    <option value="FIA">FIA</option>
                    <option value="FIB">FIB</option>
                    <option value="FA">FA</option>
                </select>
            </label>
            <li><a class="underline-animation" href="../Controller/Deconnexion.php">Déconnexion</a></li>
        </ul>
    </nav>
</header>

<!-- Script pour faire fonctionner le menu burger (affichage mobile) -->
<script>
    const burger = document.querySelector('.burger');
    const menu = document.querySelector('.menu');
    burger.addEventListener("click", () => {
        menu.classList.toggle("active");
        burger.classList.toggle("toggle");
    });
</script>

<?php
// Inclusion des fichiers nécessaires pour la connexion à la base de données et la gestion de l'emploi du temps
include "../Controller/ConnectionBDD.php";
require_once "../Model/Classe/Edt.php";

// Création d'un objet Edt pour gérer l'emploi du temps
$edt = new Edt();

// Démarrage de la session pour gérer les variables utilisateur
session_start();

// Vérification si le rôle est défini, sinon rediriger vers la page de connexion
if (isset($_SESSION['role'])) {
    if($_COOKIE['role'] != 'professeur'){
        header("Location: ./Deconnexion.php"); // Redirection si pas de rôle
        exit();
    }
}

// Calcul de la date du début de la semaine (lundi)
$dateActuel = date('Y-m-d', strtotime('monday this week'));

//récupération du cookie du nom du professeur pour l'utiliser en condition de la requête pour afficher l'edt
$nomProf = $_COOKIE['nomProf'];

function AfficherEdtSemaine($dateDebut, $nomProf) {
    global $edt;
    $timestamp = strtotime($dateDebut);
    $lundi = date("Y-m-d", $timestamp);

    echo "<table>";
    echo "<tr><th>Heure</th>";

    //Liste pour afficher les jours dans l'axe des abscisses
    $joursSemaine = ['Lundi', 'Mardi', 'Mercredi', 'Jeudi', 'Vendredi'];
    $joursData = [];

    //La boucle sert à transformer en strtotime puis afficher les jours
    for ($i = 0; $i < 5; $i++) {
        $jourTimestamp = strtotime("+$i day", strtotime($lundi));
        $jour = date("Y-m-d", $jourTimestamp);
        $joursData[$i] = RecupererCoursParJour($jour, $nomProf);
        echo "<th>" . $joursSemaine[$i] . " " . date("d/m", $jourTimestamp) . "</th>";
    }
    echo "</tr>";

    //Liste des horaires qui seront afficher sur l'axe des ordonnées
    $listeHorraire = ['08:00', '09:30', '11:00', '12:30', '14:00', '15:30', '17:00'];
    $cellulesSautees = array_fill(0, 5, 0);

    for ($h = 0; $h < count($listeHorraire); $h++) {
        echo "<tr>";
        echo "<td style='vertical-align: top;'>$listeHorraire[$h]</td>";

        for ($j = 0; $j < 5; $j++) {
            if ($cellulesSautees[$j] > 0) {
                $cellulesSautees[$j]--;
                continue;
            }

            $horaireCourant = date("H:i:s", strtotime($listeHorraire[$h]));
            $coursDuJour = array_filter($joursData[$j], function($cours) use ($horaireCourant) {
                return date("H:i:s", strtotime($cours['date'])) === $horaireCourant;
            });

            if (!empty($coursDuJour)) {
                $cours = current($coursDuJour);

                $dureeStr = $cours['duree'];
                if (strpos($dureeStr, 'years') !== false) {
                    preg_match('/(\d+) hours (\d+) mins/', $dureeStr, $matches);
                    $dureeMinutes = !empty($matches) ? (intval($matches[1]) * 60) + intval($matches[2]) : 90;
                } else {
                    $dureeParts = explode(':', $dureeStr);
                    $dureeMinutes = count($dureeParts) == 3 ? (intval($dureeParts[0]) * 60) + intval($dureeParts[1]) : 90;
                }

                $nombreCreneaux = ceil($dureeMinutes / 90);

                $discipline = strtolower($edt->supprimerAccents($cours['discipline']));
                $discipline = preg_replace('/[^a-z0-9]+/', '-', $discipline);
                $discipline = trim($discipline, '-');

                $typeSeance = strtolower($cours['typeseance']);
                $salles = explode(',', $cours['salles']);

                //on vérifie le type de séance pour adapter l'affichage
                if ($typeSeance == 'ds') {
                    $classeCSS = "ds";
                    $sallesStr = "Amphi, Salle 110";

                }
                //on vérifie le type de séance pour adapter l'affichage
                elseif ($typeSeance == 'prj') {
                    $classeCSS = "sae";
                    $sallesStr = "Salle " . implode(", ", $salles);
                }
                //On vérifie si c'est un cours de 1h30 ou 3h pour adapter l'affichage
                else {
                    $classeCSS = $dureeMinutes == 180 ?
                        "cours-" . $discipline . "-" . $typeSeance . '-3' :
                        "cours-" . $discipline . "-" . $typeSeance;

                    if (count($salles) == 1 && $salles[0] == '200') {
                        $sallesStr = "Amphi";
                    } else {
                        $sallesStr = "Salle " . implode(", ", $salles);
                    }
                }

                $prenomProf = $cours['prenom'][0] . ".";
                if ($prenomProf == ".") {
                    $prenomProf = "";
                }

                $semestre = $cours['semestre'];
                $nomRessource = $cours['ressource'];

                //contenuHTML contient toutes les informations présentes dans chaques cases de l'emploi du temps
                $contenuHTML = "<div class='$classeCSS'>" .
                    $cours['typeseance'] . "<br>" .
                    $cours['code'] . " " . $cours['matiere'] . "<br>" .
                    $sallesStr . "<br>" .
                    "Semestre : ".$semestre . " | " . $nomRessource . "<br>" .
                    "</div>";

                echo "<td rowspan='$nombreCreneaux'>$contenuHTML</td>";
                $cellulesSautees[$j] = $nombreCreneaux - 1;
            } else {
                echo "<td></td>";
            }
        }
        echo "</tr>";
    }
    echo "</table>";
}

//requête permettant de récupérer toutes les informations à utiliser dans la méthode afficherEdtSemaine pour faire l'affichage dans les cases de l'emploi du temps
function RecupererCoursParJour($jour, $nomProf): array
{

    $sql = "
    SELECT
        seance.idseance, seance.typeseance, seance.duree,
        schedulesalle.salle as salles,
        collegue.prenom, collegue.nom,
        enseignement.court as matiere,
        enseignement.discipline, schedule.horaire as date, 
        enseignement.semestre, schedule.nomgroupe, enseignement.code, rg.nomressource as ressource
    FROM seance
        LEFT JOIN collegue ON seance.collegue = collegue.id
        JOIN enseignement USING (code, semestre)
        JOIN schedule USING (code, typeseance, nomgroupe, semestre, noseance)
        JOIN ressourcegroupe rg USING (nomgroupe, semestre)
        JOIN schedulesalle USING (code, typeseance, nomgroupe, semestre, noseance, version)
    WHERE DATE(horaire) = ?
        AND version = ?
        AND nom ILIKE ?
    ORDER BY horaire
    ";

    $connexion = getConnectionBDD();
    $req = $connexion->prepare($sql);
    $req->execute([$jour, $_COOKIE["version"], $nomProf]);
    return $req->fetchAll(PDO::FETCH_ASSOC);
}

// Gestion des actions POST, comme la sélection de la date ou le changement de semaine
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    if (isset($_POST["selectedDate"])) {
        // Convertir la date sélectionnée en date du lundi de la semaine
        $selectedDate = new DateTime($_POST["selectedDate"]);
        $dayOfWeek = $selectedDate->format('N'); // 1 (lundi) à 7 (dimanche)
        $daysToSubtract = $dayOfWeek - 1;
        $selectedDate->sub(new DateInterval("P{$daysToSubtract}D"));
        $dateActuel = $selectedDate->format('Y-m-d');
    } else {
        $dateActuel = $_POST["dateActuel"] ?? $dateActuel;
    }

    if (isset($_POST["precedent"])) {
        $dateActuel = $edt->decrementerSemaine($dateActuel);
    }

    if (isset($_POST["suivant"])) {
        $dateActuel = $edt->incrementerSemaine($dateActuel);
    }
}

// Affichage de la partie permettant de changer la semaine, incluant un calendrier
echo '<div class="changerSemaine">
    <button id="download-pdf" class="btn">Télécharger en PDF</button>
    <form action="EDTprof.php" method="post">
        <button type="submit" name="precedent">&lt;</button>
        
        <label for="selectionnerSemaine">Semaine du</label>
        <input type="date" id="selectionnerSemaine" name="selectedDate" onchange="this.form.submit()" 
               value="' . htmlspecialchars($dateActuel, ENT_QUOTES, 'UTF-8') . '">
        
        <input type="hidden"  name="dateActuel" 
               value="' . htmlspecialchars($dateActuel, ENT_QUOTES, 'UTF-8') . '">
        
        <button type="submit" name="suivant">&gt;</button>
    </form>
</div>';

// Affichage du footer avec les auteurs du projet
echo ('<footer class="footer">
    <p>&copy; 2024 - SAE Emploi du temps. Rémi | Dorian | Matthéo | Bastien | Noah.</p>
</footer>');

// Appel à la fonction qui affiche l'emploi du temps du professeur choisit et pour de la semaine
AfficherEdtSemaine($dateActuel, $nomProf);
?>

<!-- la génération de PDF -->
<script src="../Model/JavaScript/GenererPDF.js"></script>
<script src="../Model/JavaScript/MenuPrincipal.js"></script>
<script>afficherElement("<?php echo $_SESSION['role']; ?>");</script>
</body>