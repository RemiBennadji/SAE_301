<?php
include "../Contoller/ConnectionBDD.php";

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

        session_start();
        $_SESSION['role'] = $role;
        $_SESSION['ID'] = $ID;

        setcookie("role", $role, time() + (60 * 15), "/");
        setcookie("ID", $ID, time() + (60 * 15), "/");

        if (isset($role)) {
            header("location:../View/MenuPrincipal.php");
            exit();
        }
    }

} catch (PDOException $e) {
    echo $e->getMessage();
}