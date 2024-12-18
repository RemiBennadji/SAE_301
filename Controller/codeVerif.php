<?php
header('Content-Type: application/json');

require 'ConnectionBDD.php';


ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

session_start();

$conn = getConnectionBDD();

if($_SERVER["REQUEST_METHOD"] == "POST" and isset($_POST["code"])) {
    $codeExpire = "DELETE FROM codeverif WHERE expiration < NOW()";
    $conn->prepare($codeExpire)->execute();


    $codeVerif = htmlspecialchars($_POST["code"]);
    $recupCode = "SELECT codev FROM codeverif WHERE codev = :code";
    $recupCode = $conn->prepare($recupCode);
    $recupCode->bindParam(':code', $codeVerif);
    $recupCode->execute();
    $recupCode = $recupCode->fetchAll(PDO::FETCH_ASSOC);
    if ($recupCode) {
        if ($codeVerif == $recupCode[0]["codev"]) {
            $sup = "DELETE FROM codeverif WHERE codev = :code";
            $sup = $conn->prepare($sup);
            $sup->bindParam(':code', $codeVerif);
            $sup->execute();
            ECHO json_encode(["redirect" => "../../View/HTML/changeMDP.html"]);
        } else {
            echo json_encode(['error' => 'errorConnexion']);
        }
    } else {
        echo json_encode(['error' => 'errorConnexion']);
    }
}