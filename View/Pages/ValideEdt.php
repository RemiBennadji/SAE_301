<html lang="fr">
<head>
    <title>EDTValidation</title>
    <link rel="stylesheet" type="text/css" href="../CSS/CSSBasique.css">
</head>
<body>
<a href="EDT.php"><img src="../../Ressource/logouphf2.png" class="logoUPHF" alt="Logo UPHF"></a>
<script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf/2.5.1/jspdf.umd.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf-autotable/3.5.22/jspdf.plugin.autotable.min.js"></script>
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
<script defer src="../../Model/JavaScript/menuHamburger.js"></script>


<br><br><br>

<?php
include "../../Controller/ConnectionBDD.php";
require_once "../../Model/Classe/Edt.php";

$edt = new Edt();

//ini_set('display_errors', 1);
//ini_set('display_startup_errors', 1);
//error_reporting(E_ALL);

// Vérifier si le cookie "groupe" existe
session_start();
if (isset($_COOKIE['groupe'])) {
    $classeActuel = $_COOKIE['groupe'];
} else {
    header("Location: ../View/HTML/Deconnexion.html"); // Redirection si pas de rôle
}

if (!isset($_SESSION['role'])) {
    header("Location: ../View/HTML/Deconnexion.html"); // Redirection si pas de rôle (connexion via URL sans passer par l'identification)
    exit();
}

// Vérifier si le cookie "annee" existe
if (isset($_COOKIE['annee'])) {
    $anneeActuel = $_COOKIE['annee'];
} else {
    echo "Le cookie 'annee' n'est pas défini.";
}

//Vérification si le rôle est défini, sinon rediriger vers la page de connexion
if (isset($_SESSION['role'])) {
    if($_COOKIE['role'] != 'professeur' && $_COOKIE['role'] != 'administrateur'){
        header("Location: ./Deconnexion.php"); // Redirection si pas de rôle
        exit();
    }
//    if($_SESSION['role'] == 'administrateur'){
//        $sqlheur24h = "SELECT version.timestamp + INTERVAL '24 hours' AS timestamp_plus_24h, version.version FROM version WHERE version.version = (SELECT MAX(version) FROM version)";
//        $sqlcount = "select count(*) from validationedt where valider = false";
//        try {
//            $connexion = getConnectionBDD();
//            $conn1 = $connexion->prepare($sqlheur24h);
//            $conn1->execute();
//
//            $result1 = $conn1->fetch(PDO::FETCH_ASSOC);
//            $timestamp_plus_24h = $result1['timestamp_plus_24h'];
//            $version = $result1['version'];
//
//            $conn2 = $connexion->prepare($sqlcount);
//            $conn2->execute();
//
//            $result2 = $conn2->fetch(PDO::FETCH_ASSOC);
//            $countFalse = $result2['count'];
//
//            $DateActuel = new DateTime();
//
//            if ($DateActuel > $timestamp_plus_24h && $countFalse ==0) {
//                $insertion = "insert into versionvalideedt(version, datevalidation) values(?, ?)";
//                $stmt = $connexion->prepare($insertion);
//                $stmt->bindParam(1, $version);
//                $stmt->bindParam(2, $DateActuel);
//                $stmt->execute();
//            }
//        } catch (PDOException $e) {
//            echo 'Erreur : ' . $e->getMessage();
//        }
//    }
}

date_default_timezone_set('Europe/Paris');//Fuseau horaire
$dateActuel = date('Y-m-d', strtotime('monday this week'));
$timestamp = date('Y-m-d H:i:s');//Date actuel pour la mettre dans la BDD

//Pour avoir l'edt à valider
try {
    $sql2 = "select max(version) as total from schedulesalle;";

    $connexion = getConnectionBDD();

    $nouvelleVersion = $connexion->prepare($sql2);
    $nouvelleVersion->execute();
    $nouvelleVersion = $nouvelleVersion->fetch(PDO::FETCH_ASSOC)['total'];
}
catch (PDOException $e) {
    echo $e->getMessage();
}

function clearProfValidation()
{
    $clear = "delete from validationEDT;";
    try {
        $connexion = getConnectionBDD();
        $req = $connexion->query($clear);
        $req->execute();
        $req->fetchAll(PDO::FETCH_ASSOC);
    }
    catch (PDOException $e) {
        echo $e->getMessage();
    }
}

function ajoutProfValidation()
{
    $sql = "select * from prof;";
    $sql2 = "insert into validationEDT (nom,prenom,valider) values (?,?,?)";
    try {
        $connexion = getConnectionBDD();
        $req = $connexion->query($sql);
        $req->execute();
        foreach ($req->fetchAll(PDO::FETCH_ASSOC) as $prof) {//Parcours la BDD @Bastien
            $req2 = $connexion->prepare($sql2);
            $req2->execute([$prof['nom'], $prof['prenom'], "FALSE"]);
        }
    }
    catch (PDOException $e) {
        echo $e->getMessage();
    }
}

function adminValideVersion()//Si les profs qui ont validés > aux profs non validé, un bouton apparait pour mettre a jour la bdd avec ca version validé
{
    global $nouvelleVersion;
    $sql = "select count(nom) as total from validationEDT where valider = ?;";
    try {
        $connexion = getConnectionBDD();
        $accepter = $connexion->prepare($sql);
        $accepter->execute(['true']);
        $accepter = $accepter->fetch(PDO::FETCH_ASSOC)['total'];

        $pasaccepter = $connexion->prepare($sql);
        $pasaccepter->execute(['false']);
        $pasaccepter = $pasaccepter->fetch(PDO::FETCH_ASSOC)['total'];

        if(($pasaccepter > 0) && ($nouvelleVersion != $_COOKIE["version"])){

            echo "<form id='adminValide' action='ValideEdt.php' method='post'>
                    <div class='DivadminValide'>
                        <input type='hidden' name='actionAdminValide' value=''>
                        <button type='button' class='ValiderVersionAdmin' id='ValiderVersionAdmin' onclick='validationAdmin()'>Mise à jour version</button>
                    </div>
                </form>
                ";
        }
    }
    catch (PDOException $e) {
        echo $e->getMessage();
    }
}
function viderValidation()
{
    clearProfValidation();
    ajoutProfValidation();
}

function genererTableau($data, $titre) {
    echo "<h2>$titre</h2>";
    echo "<table>
        <thead>
            <tr>
                <th>Nom</th>
                <th>Prénom</th>
            </tr>
        </thead>
        <tbody>";
    foreach ($data as $ligne) {
        echo "<tr>
            <td>" . htmlspecialchars($ligne['nom']) . "</td>
            <td>" . htmlspecialchars($ligne['prenom']) . "</td>
        </tr>";
    }
    echo "</tbody>
    </table>";
}


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

echo '<div class="changerSemaine">
    <button id="download-pdf" class="btn">Télécharger en PDF</button>
    <form action="ValideEdt.php" method="post">
        <button type="submit" name="precedent">Précédent</button>
        
        <label for="selectionnerSemaine">Semaine du</label>
        <input type="date" id="selectionnerSemaine" name="selectedDate" onchange="this.form.submit()" 
               value="' . htmlspecialchars($dateActuel, ENT_QUOTES, 'UTF-8') . '">
        <input type="hidden" name="role" value="' . $_SESSION["role"] . '">
        <input type="hidden"  name="dateActuel" 
               value="' . htmlspecialchars($dateActuel, ENT_QUOTES, 'UTF-8') . '">
        
        <button type="submit" name="suivant">Suivant</button>
    </form>
</div><br><br><br>';


echo "<div class='container-edt'>
        <div class='edt-table'>
            <label>Version actuelle</label><br>
            <label>Version : ". $_COOKIE["version"] ."</label>";
$edt->AfficherEdtSemaine($dateActuel, $classeActuel, $anneeActuel, $_COOKIE["version"]);
echo "  </div>
        <div class='edt-table'>
            <label>Nouvelle version</label><br>
            <label>Version : ". $nouvelleVersion ."</label>";
$edt->AfficherEdtSemaine($dateActuel, $classeActuel, $anneeActuel, $nouvelleVersion);
echo "  </div>
      </div>";

echo "<form id='validation' action='ValideEdt.php' method='post'>
        <div class='DivValider'>
            <input type='hidden' name='action' value='valider'>
            <button type='button' class='ValiderVersion' id='ValiderVersion' onclick='confirmerAction()'>Valider Version Actuelle</button>
            <button type='button' class='AnnulerValidation' id='AnnulerValidation' onclick='annulerValidation()'>Annuler la validation</button>
            <button type='button' id='Vider' onclick='vider()'>Vider les validations</button>
        </div>
        <label id='validationMessage' style='display: none; color: green;'></label>
    </form>
";

adminValideVersion();

if (isset($_POST["action"])) {
    if ($_POST["action"] === "valider") {
        $sql = "UPDATE validationEDT SET valider = ?, dateValidation = ? WHERE nom ilike ?;";
        try {
            $nom = $_COOKIE['nomProf'];
            $connexion = getConnectionBDD();
            $req = $connexion->prepare($sql);
            $req->execute([true,$timestamp,$nom]);
        } catch (PDOException $e) {
            echo $e->getMessage();
        }
    } else if ($_POST["action"] === "annuler") {
        $sql = "UPDATE validationEDT SET valider = ?, dateValidation = ? WHERE nom ilike ?;";
        try {
            $nom = $_COOKIE['nomProf'];
            $connexion = getConnectionBDD();
            $req = $connexion->prepare($sql);
            $req->execute([false,null,$nom]);
        } catch (PDOException $e) {
            echo $e->getMessage();
        }
    }
    else if ($_POST["action"] === "vider") {
        viderValidation();
    }
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    if (isset($_POST["actionAdminValide"]) && $_POST["actionAdminValide"] === "adminValider") {
        $sql = "insert into versionValideEDT (version,dateValidation) values(?,?);";
        try {
            $req = $connexion->prepare($sql);
            $req->execute([$nouvelleVersion, $timestamp]);
            viderValidation();
//            setcookie("version", $nouvelleVersion, time() + (60 * 15), "/",);
//            echo "<script type='text/javascript'>window.location.href = 'ValideEdt.php';</script>";
//            exit;

        } catch (PDOException $e) {
            echo $e->getMessage();
        }
    }
}

$sql = "SELECT * FROM validationEDT;";
try {
    $connexion = getConnectionBDD();
    $req = $connexion->query($sql);
    $personnes = $req->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    echo "Erreur : " . $e->getMessage();
    exit;
}

$valides = [];
$nonValides = [];

// Séparation des données en deux groupes
foreach ($personnes as $prof) {
    if ($prof['valider']) {
        $valides[] = $prof;
    } else {
        $nonValides[] = $prof;
    }
}

echo "<h1>Liste des validations</h1>";

if($_COOKIE["role"] === "administrateur") {
    genererTableau($valides, "Validés");
    genererTableau($nonValides, "Non validés");
}



echo "</tbody>
</table>";
?>
<footer class="footer"><p>&copy; 2024 - SAE Emploi du temps. Rémi | Dorian | Matthéo | Bastien | Noah.</p></footer>

<script src="../../Model/JavaScript/ValideEdt.js"></script>
<script src="../../Model/JavaScript/MenuPrincipal.js"></script>
<script>afficherElement("<?php echo $_SESSION['role']; ?>");</script>
<script src="../../Model/JavaScript/CalendrierEDT.js"></script>
<script src="../../Model/JavaScript/GenererPDF.js"></script>
</body>
</html>