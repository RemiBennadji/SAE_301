<?php
header('Content-Type: application/json');

require 'ConnectionBDD.php';


ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

session_start();

$conn = getConnectionBDD();

if($_SERVER["REQUEST_METHOD"] == "POST" and isset($_POST["code"])) {
//    $codeExpire = codeExpire();
//    $conn->prepare($codeExpire)->execute();
    codeExpire();


    $codeVerif = htmlspecialchars($_POST["code"]);
//    $recupCode = recupererCode();
//    $recupCode = $conn->prepare($recupCode);
//    $recupCode->bindParam(':code', $codeVerif);
//    $recupCode->execute();
//    $recupCode = $recupCode->fetchAll(PDO::FETCH_ASSOC);
    $recupCode = recupererCode($codeVerif);
    $mail = $recupCode[0]["email"];
    if ($recupCode) {
        if ($codeVerif == $recupCode[0]["codev"]) {
//            $sup = suppCode();
//            $sup = $conn->prepare($sup);
//            $sup->bindParam(':code', $codeVerif);
//            $sup->execute();
            suppCode($codeVerif);
            session_destroy();
            session_start();
            $_SESSION['from'] = true;
            $_SESSION['mail'] = $mail;
            ECHO json_encode(["redirect" => "../../View/Pages/changeMDP.html"]);
        } else {
            echo json_encode(['error' => 'errorConnexion']);
        }
    } else {
        echo json_encode(['error' => 'errorConnexion']);
    }
}