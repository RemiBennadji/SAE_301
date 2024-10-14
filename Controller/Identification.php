<?php
include "../Controller/ConnectionBDD.php";

$ID = $_POST["id"];
$PWD = $_POST["pwd"];
$sql1 ="select role from infoutilisateur where identifiant=:ID and motdepasse=:PWD";
$sql2 ="select identifiant, motDePasse from infoutilisateur";


try {
    $connection = getConnectionBDDEDTIdentification();

    $result = $connection->prepare($sql1);
    $result->bindParam(':ID', $ID);
    $result->bindParam(':PWD', $PWD);
    $result->execute();
    $result =$result->fetch(PDO::FETCH_ASSOC);

    $result2 =$connection->prepare($sql2);
    $result2->execute();
    $result2 =$result2->fetchall(PDO::FETCH_ASSOC);

    if (!empty($result2)) {
        foreach ($result2 as $row) {
            echo "Identifiant: " . $row['identifiant'] . ", Mot de Passe: " . $row['motdepasse'] . "<br>";
        }
    } else {
        echo "Aucun résultat trouvé.";
    }

    if ($result) {
        $role = $result['role'];

        session_start();
        $_SESSION['role'] = $role;
        $_SESSION['ID'] = $ID;

        setcookie("role", $role, time() + (60 * 15), "/");
        setcookie("ID", $ID, time() + (60 * 15), "/");

        if (isset($role)) {
            header("location:../Controller/MenuPrincipal.php");
            exit();
        }
    }

} catch (PDOException $e) {
    echo $e->getMessage();
}