<?php
include "../Model/ConnectionBDD.php";

$ID = $_POST["id"];
echo "id = ".$ID."<br>";
$PWD = $_POST["pwd"];
echo "pwd = ".$PWD;
$sql1 ="select role from infoutilisateur where identifiant=:ID and mdp=:PWD";


try {
    $connection = getConnectionBDDEDTIdentification();

    $result = $connection->prepare($sql1);
    $result->bindParam(':ID', $ID);
    $result->bindParam(':PWD', $PWD);
    $result->execute();
    $result =$result->fetch(PDO::FETCH_ASSOC);

    if ($result) {
        $role = $result['role'];
        if ($role == "administrateur") {
            header("location:../View/menuPrincipalAdmin.html");
        }
        else if ($role == "etudiant") {
            header("location:../View/menuPrincipalEleve.html");
        }
        else if ($role == "professeur") {
            header("location:../View/menuPrincipalProf.html");
        }
    }

} catch (PDOException $e) {
    echo $e->getMessage();
}