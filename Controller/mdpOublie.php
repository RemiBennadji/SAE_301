<?php

use PHPMailer\PHPMailer\PHPMailer;

require 'ConnectionBDD.php';
include_once "../Model/Classe/Mail.php";

//$conn = getConnectionBDD();

function sendCode($email, $code){
    $time = time();
    $expiration = $time + (10*60);
//    $sql1 = 'INSERT INTO codeverif (email, codev, date, expiration) VALUES (:email, :code, TO_TIMESTAMP(:time), TO_TIMESTAMP(:expiration))';
    try {
//        $result = $conn->prepare($sql1);
//        $result->bindParam(':email', $email);
//        $result->bindParam(':code', $code);
//        $result->bindParam(':time', $time);
//        $result->bindParam(':expiration', $expiration);
//        $result->execute();
        insertCodeVerif();

        $mail = new Mail();
        $mail->setMdp('xthbhnhaiazxbebp');
        $mail->setDestinataire($email);
        $mail->setExpediteur('saeedts301@gmail.com');
        $mail->setObjet('Code de Verification');
        $message = 'Voici votre code de verification : '.$code;
        $mail->setMessage($message);
        $mail->setParam();
        $mail->creerMail();
    }catch (PDOException $e){
        echo "Erreur lors de l'envoi de l'email : ". $e->getMessage(); ;
    }
}

if ($_SERVER["REQUEST_METHOD"] == "POST" and isset($_POST["email"]) and !isset($_POST["inputCode"])) {
    $email = isset($_POST["email"]) ? htmlspecialchars($_POST["email"]) : "";
    $code = rand(0,999999);
    $code = sprintf('%06d', $code);
//    $listeMail = "SELECT mail FROM mailidentifiant";
//    $listeMail = $conn->prepare($listeMail);
//    $listeMail->execute();
//    $listeMail = $listeMail->fetchAll(PDO::FETCH_ASSOC);
    $listeMail = selectMail();
    $mailAll = [];
    foreach ($listeMail as $mail) {
        $mailAll[] = $mail["mail"];
    }
    if(in_array($email, $mailAll)){
        sendCode($email, $code);
        session_start();
        $_SESSION['mail'] = $email;
        header("location: ../View/Pages/codeVerif.html");
    }else{
        header("location: ../View/Pages/mdpOublie.html");
    }
}
?>