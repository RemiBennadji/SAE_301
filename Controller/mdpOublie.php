<?php

use PHPMailer\PHPMailer\PHPMailer;

require 'ConnectionBDD.php';
include_once "../Model/Classe/Mail.php";

$conn = getConnectionBDD();

function sendCode($email, $code, $conn){
    $time = time();
    $sql1 = 'INSERT INTO codeverif (email, codev, date) VALUES (:email, :code, TO_TIMESTAMP(:time))';
    try {
        $result = $conn->prepare($sql1);
        $result->bindParam(':email', $email);
        $result->bindParam(':code', $code);
        $result->bindParam(':time', $time);
        $result->execute();

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
    $listeMail = "SELECT mail FROM mailidentifiant";
    $listeMail = $conn->prepare($listeMail);
    $listeMail->execute();
    $listeMail = $listeMail->fetchAll(PDO::FETCH_ASSOC);
    $mailAll = [];
    foreach ($listeMail as $mail) {
        $mailAll[] = $mail["mail"];
    }
    if(in_array($email, $mailAll)){
        sendCode($email, $code, $conn);
        header("location: ../View/HTML/codeVerif.html");
    }else{
        header("location: ../View/HTML/mdpOublie.html");
    }
}
?>