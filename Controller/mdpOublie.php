<?php

use PHPMailer\PHPMailer\PHPMailer;

require 'ConnectionBDD.php';
include_once "../Model/Classe/Mail.php";


function sendCode($email, $code, $conn){
//
//    $conn = getConnectionBDD();
    $time = strtotime("now");
    $sql1 = 'INSERT INTO codeverif (email, codev, date) VALUES (:email, :code, :time)';
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
        header('location: ../View/HTML/changeMDP.html');
        exit();
    }catch (PDOException $e){
        echo "Erreur lors de l'envoi de l'email : ". $e->getMessage(); ;
    }


//    $mail = new PHPMailer(true);
//    $time = strtotime("now");
//    $sql1 = 'insert into codeverif values(email::email, codeV::code, date::time)';
//    try {
//        $result = $conn->prepare($sql1);
//        $result->bindParam(':email', $email);
//        $result->bindParam(':code', $code);
//        $result->bindParam(':time', $time);
//        $result->execute();
//
//        $mail = new Mail();
//        $mail->setMdp('wxwxfhvqmswxufni');
//        $mail->setDestinataire($email);
//        $mail->setExpediteur('saeedt301@gmail.com');
//        $mail->setObjet('Code de Vérification');
//        $message = 'Voici votre code de vérification : '.$code;
//        $mail->setMessage($message);
//        $mail->setParam();
//        $mail->creerMail();
//        header('location: changeMDP.html');
//            exit();
//        }
//    } catch (PDOException $e) {
//        $erreur = "Erreur lors de l'envoi de l'email : " . $mail->ErrorInfo;
//    }


//    $mail = new PHPMailer(true);
//    $time = strtotime("now");
//    $sql1 = 'insert into codeverif values(email::email, codeV::code, date::time)';
//    try {
//        $mail->SMTPDebug = 2;
//        $mail->isSMTP();
//        $mail->Host = 'smtp.gmail.com';
//        $mail->SMTPAuth = true;
//        $mail->Username = 'saeedts301@gmail.com';
//        $mail->Password = 'xthbhnhaiazxbebp';
//        $mail->SMTPSecure = PHPMailer::ENCRYPTION_SMTPS;
//        $mail->Port = 465;
//        $mail->setFrom('saeedts301@gmail.com', 'Sae-EDT');
//        $mail->addAddress($email);

//        $conn = getConnectionBDDEDTIdentification();
//        $result = $conn->prepare($sql1);
//        $result->bindParam(':email', $email);
//        $result->bindParam(':code', $code);
//        $result->bindParam(':time', $time);
//        $result->execute();

//        $mail->isHTML(true);
//        $mail->Subject = 'Code de Verification';
//        $mail->Body = 'Voici votre code de verification : '.$code;
//
//        if($mail->send()){
//            header('location: ../View/HTML/changeMDP.html');
//            exit();
//        }
//    } catch (PDOException $e) {
//        $erreur = "Erreur lors de l'envoi de l'email : " . $mail->ErrorInfo;
//    } catch (Exception $e) {
//    }
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $email = isset($_POST["email"]) ? htmlspecialchars($_POST["email"]) : "";
    $code = rand(0,999999);
    $code = sprintf('%06d', $code);
    $conn = getConnectionBDD();
    $listeMail = "SELECT mail FROM mailidentifiant";
    $listeMail = $conn->prepare($listeMail);
    $listeMail->execute();
    if(in_array($email, $listeMail->fetchAll(PDO::FETCH_ASSOC))){
        sendCode($email, $code, $conn);
    }else{
        echo "Erreur : le mail n'existe pas";
    }

}

?>