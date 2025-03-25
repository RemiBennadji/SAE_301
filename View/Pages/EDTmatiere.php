<html lang="fr">
<head>
    <title>EDTMatiere</title>
    <link rel="stylesheet" type="text/css" href="../CSS/CSSBasique.css">
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf/2.5.1/jspdf.umd.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf-autotable/3.5.22/jspdf.plugin.autotable.min.js"></script>
</head>
<body>
<a href="EDT.php"><img src="../../Ressource/logouphf2.png" class="logoUPHF" alt="Logo UPHF"></a>
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
            <li><a id="edtProf" class="underline-animation" href="EDTprof.php" style="display: none">EDT Professeur</a></li>
            <li><a id="edtCours" class="underline-animation" href="EDTmatiereSelection.php" style="display: none">EDT Ressource</a></li>
            <li><a class="underline-animation" href="EDTsalleLibres.php" id="afficheSalles">Salles disponibles</a></li>
            <li><a id="tableauEtudiant" class="underline-animation" href="VoireEtudiant.php" style="display: none">Listes Étudiants</a></li>
            <li><a id="tableauAbsence" class="underline-animation" href="TableauAbsence.php" style="display: none">Tableau Absence</a></li>
            <li><a id="tableauReport" class="underline-animation" href="TableauReport.php" style="display: none">Tableau Report</a></li>
            <li><a class="underline-animation" href="demandePage.php" id="demande" style="display: none">Faire une demande</a></li>
            <li><a class="underline-animation" href="creationCompte.php" id="creationCompte" style="display: none">Créer un compte</a></li>
            <li><a id ="valideEDT" class="underline-animation" href="ValideEdt.php" style="display: none">ValideEDT</a></li>
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
            <li><a class="underline-animation" href="../../Controller/Deconnexion.php">Déconnexion</a></li>
        </ul>
    </nav>
</header>

<br><br><br>

<?php
// Inclusion des fichiers nécessaires pour la connexion à la base de données et la gestion de l'emploi du temps
use function Sodium\add;

include "../../Controller/ConnectionBDD.php";
require_once "../../Model/Classe/Edt.php";

// Création d'un objet Edt pour gérer l'emploi du temps
$edt = new Edt();

// Démarrage de la session pour gérer les variables utilisateur
session_start();
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

// Vérification si le rôle est défini, sinon rediriger vers la page de connexion
if (isset($_SESSION['role'])) {
    if($_COOKIE['role'] != 'administrateur' && $_COOKIE['role'] != 'professeur' && $_COOKIE['role'] != 'secretariat'){
        header("Location: ./Deconnexion.php"); // Redirection si pas de rôle
        exit();
    }
}

// Calcul de la date du début de la semaine (lundi)
$dateActuel = date('Y-m-d', strtotime('monday this week'));

//récupération du nom de la ressource pour l'utiliser en condition de la requête pour afficher l'edt
$nomProf = $_POST["codeRessource"];

function estExistant(Array $listeExistant){
    $listeInterm = [];
    for($i=0; $i<count($listeExistant); $i++){
        array_push($listeInterm, $listeExistant[$i]);
    }
    return array_unique($listeInterm);
};



function AfficherEdtSemaine($dateDebut, $nomProf) {
    global $edt;
    $timestamp = strtotime($dateDebut);
    $lundi = date("Y-m-d", $timestamp);

    echo "<table class='edtresponsive'>";
    echo "<tr><th>Heure</th>";

    //Liste pour afficher les jours dans l'axe des abscisses
    $joursSemaine = ['Lundi', 'Mardi', 'Mercredi', 'Jeudi', 'Vendredi'];
    $joursData = [];

    //La boucle sert à transformer en strtotime puis afficher les jours
    for ($i = 0; $i < 5; $i++) {
        $jourTimestamp = strtotime("+$i day", strtotime($lundi));
        $jour = date("Y-m-d", $jourTimestamp);
        $joursData[$i] = RecupererCoursParJour($jour, $nomProf);




//        // Pour déboguer le contenu de $joursData
//        foreach ($joursData as $jour => $coursJour) {
//            echo "Jour $jour : <br>";
//            foreach ($coursJour as $cours) {
//                echo "- Heure: " . date('H:i', strtotime($cours['date'])) . "<br>";
//                echo "  Type: " . $cours['typeseance'] . "<br>";
//                echo "  Matière: " . $cours['matiere'] . "<br>";
//                echo "  Salle: " . $cours['salles'] . "<br>";
//                echo "  Nombre de cours: " . $cours['nombre_cours'] . "<br>";
//                echo "  Semestre: " . $cours['semestre'] . "<br>";
//                echo "<hr>";
//            }
//        }



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

                //Todo
                //print_r($cours);

//                $data = [];
//                $nomRessource = $cours['ressource'];
//                $nomGroupe = trim($nomRessource[0]);
//                if (in_array($nomGroupe, $data)) {//Si le groupe est deja dans la boucle il passe a la prochaine iteration
//                    continue;
//                }
//                array_push($data, $nomGroupe);//Ajout du groupe dans $data


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

                if(isset($cours['prenom'][0])){
                    $prenomProf = $cours['prenom'][0] . ".";
                }
                if ($prenomProf == ".") {
                    $prenomProf = "";
                }


                $semestre = $cours['semestre'];
                $nomRessource = $cours['ressource'];

                //contenuHTML contient toutes les informations présentes dans chaques cases de l'emploi du temps
                $contenuHTML = "<div class='$classeCSS'>" .
                    $cours['typeseance'] . "<br>" .
                    $cours['code'] . " " . $cours['matiere'] . "<br>" .
                    $sallesStr . "<br>"
                    . $cours['prenom'][0] . ". " . $cours['nom'] . "<br>";
                if($cours['typeseance'] == "CM"){
                    $contenuHTML .= "Semestre : ".$semestre . " | BUT : " . $_COOKIE["annee"] . "<br>" . "</div>";
                }
                elseif ($cours['typeseance'] == "TD"){
                    $contenuHTML .= "Semestre : ".$semestre . " | Groupe : " . $nomRessource[0] . "<br>" . "</div>";
                }
                elseif ($cours['typeseance'] == "TP"){
                    $contenuHTML .= "Semestre : ".$semestre . " | Groupe : " . $nomRessource . "<br>" . "</div>";
                }


                $nombreCours = $cours['nombre_cours'];  // Récupère le nombre de cours parallèles
                if ($nombreCours == 1) {
                    echo "<td rowspan='$nombreCreneaux'><span class='cours'>$contenuHTML</span></td>";
                }
                elseif ($cours['typeseance'] == "CM"){
                    echo "<td rowspan='$nombreCreneaux'><span class='cours'>$contenuHTML</span></td>";
                }
                elseif ($cours['typeseance'] == "TD") {
                    echo "<td rowspan='$nombreCreneaux' class='case'>";
                    $data = [];
                    $nomGroupe = trim($nomRessource[0]);

                    if (!in_array($nomGroupe, $data)) {
                        $data[] .= $nomGroupe;

                        $donnee = "<div class='$classeCSS'>" .
                            $cours['typeseance'] . "<br>" .
                            $cours['code'] . " " . $cours['matiere'] . "<br>" .
                            $sallesStr . "<br>" .
                            $cours['prenom'][0] . ". " . $cours['nom'] . "<br>" .
                            "Semestre : ".$semestre . " | Groupe : " . $nomGroupe . "<br>" . "</div>";

                        echo "<span style='padding: 2px'>$donnee</span>";

                    }
                    echo "</td>";
                }

                elseif ($cours['typeseance'] == "TP"){
                    $salleCase = next($coursDuJour)["salles"];

                    echo "<td rowspan='$nombreCreneaux' class='case'>";
                    for ($k = 0; $k < 1; $k++) {

                        //$salleCase = next($coursDuJour)["salles"];
                        if ($k > 0) {
                            //Todo Changer $sallesStr et $nomRessource[0]
                            $contenuHTML = "<div class='$classeCSS'>" .
                                $cours['typeseance'] . "<br>" .
                                $cours['code'] . " " . $cours['matiere'] . "<br>" .
                                $salleCase . "<br>" .
                                "Semestre : " . $semestre . " | Groupe : " . $nomRessource . "<br>"
                                . $cours['prenom'][0] . ". " . $cours['nom'] . "</div>";
                        } else {
                            echo "<span style='padding: 2px'>$contenuHTML</span>";
                        }
                    }
                }

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
WITH CoursParalleles AS (
    SELECT 
        seance.idseance, 
        seance.typeseance, 
        seance.duree,
        STRING_AGG(DISTINCT schedulesalle.salle::text, ',') as salles,  -- Correction ici
        collegue.prenom, 
        collegue.nom,
        enseignement.court as matiere,
        enseignement.discipline, 
        schedule.horaire as date,
        enseignement.semestre, 
        schedule.nomgroupe,
        enseignement.code, 
        rg.nomressource as ressource,
        COUNT(*) OVER (
            PARTITION BY 
                schedule.horaire,
                enseignement.code,
                seance.typeseance
        ) as nombre_cours
    FROM seance
        LEFT JOIN collegue ON seance.collegue = collegue.id
        JOIN enseignement USING (code, semestre)
        JOIN schedule USING (code, typeseance, nomgroupe, semestre, noseance)
        JOIN ressourcegroupe rg USING (nomgroupe, semestre)
        JOIN schedulesalle USING (code, typeseance, nomgroupe, semestre, noseance, version)
    WHERE DATE(horaire) = ?
        AND version = ?
        AND enseignement.code ILIKE ?
    GROUP BY 
        seance.idseance, 
        seance.typeseance, 
        seance.duree,
        collegue.prenom, 
        collegue.nom,
        enseignement.court,
        enseignement.discipline,
        schedule.horaire,
        enseignement.semestre,
        schedule.nomgroupe,
        enseignement.code,
        rg.nomressource
)
SELECT * FROM CoursParalleles
ORDER BY date, salles
    ";


    $connexion = getConnectionBDD();
    $req = $connexion->prepare($sql);
    $req->execute([$jour,$_COOKIE["version"], $nomProf]);
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
    <br><br><button id="download-pdf" class="btn">Télécharger en PDF</button><br><br>
    <form action="EDTmatiere.php" method="post">
        <button type="submit" name="precedent" class="fleche">Précédent</button>
        
        <label for="selectionnerSemaine">Semaine du</label>
        <input type="date" id="selectionnerSemaine" name="selectedDate" onchange="this.form.submit()" 
               value="' . htmlspecialchars($dateActuel, ENT_QUOTES, 'UTF-8') . '">
        
        <input type="hidden" name="codeRessource" value="' . $_POST["codeRessource"] . '">
        <input type="hidden"  name="dateActuel" 
               value="' . htmlspecialchars($dateActuel, ENT_QUOTES, 'UTF-8') . '">
        
        <button type="submit" name="suivant" class="fleche">Suivant</button>
    </form>
</div>';

// Affichage du footer avec les auteurs du projet
echo ('<footer class="footer">
    <p>&copy; 2024 - SAE Emploi du temps. Rémi | Dorian | Matthéo | Bastien | Noah.</p>
</footer>');

// Appel à la fonction qui affiche l'emploi du temps de la ressource choisie et pour de la semaine
AfficherEdtSemaine($dateActuel, $nomProf);
?>

<!-- Inclusion de scripts pour le calendrier et la génération de PDF -->
<script src="../../Model/JavaScript/GenererPDF.js"></script>
<script src="../../Model/JavaScript/CalendrierEDT.js"></script>
<script src="../../Model/JavaScript/MenuPrincipal.js"></script>
<script defer src="../../Model/JavaScript/menuHamburger.js"></script>
<script>afficherElement("<?php echo $_SESSION['role']; ?>");</script>
</body>
</html>