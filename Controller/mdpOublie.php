<?php
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require 'PHPMailer/src/Exception.php';
require 'PHPMailer/src/PHPMailer.php';
require 'PHPMailer/src/SMTP.php';
require 'vendor/autoload.php';
require 'ConnectionBDD.php';
include_once "../Model/Classe/Mail.php";


function sendCode($email, $code){

    $conn = getConnectionBDDEDTIdentification();
    $time = strtotime("now");
    $sql1 = 'insert into codeverif values(email=:email, codeV=:code, date=:time)';
    try {
        $result = $conn->prepare($sql1);
        $result->bindParam(':email', $email);
        $result->bindParam(':code', $code);
        $result->bindParam(':time', $time);
        $result->execute();

        $mail = new Mail();
        $mail->setMdp('wxwxfhvqmswxufni');
        $mail->setDestinataire($email);
        $mail->setExpediteur('saeedt301@gmail.com');
        $mail->setObjet('Code de Vérification');
        $message = 'Voici votre code de vérification : '.$code;
        $mail->setMessage($message);
        $mail->creerMail();
        header('location: changeMDP.html');
            exit();
    }catch (PDOException $e){
        echo "Erreur lors de l'envoi de l'email : ";
    }


//    $mail = new PHPMailer(true);
//    $time = strtotime("now");
//    $sql1 = 'insert into codeverif values(email::email, codeV::code, date::time)';
//    try {
//        $mail->isSMTP();
//        $mail->Host = 'smtp.gmail.com';
//        $mail->SMTPAuth = true;
//        $mail->Username = 'saeedt301@gmail.com';
//        $mail->Password = 'wxwxfhvqmswxufni';
//        $mail->SMTPSecure = PHPMailer::ENCRYPTION_SMTPS;
//        $mail->Port = 465;
//        $mail->setFrom('saeedt301@gmail.com', 'Sae-EDT');
//        $mail->addAddress($email);
//
//        $conn = getConnectionBDDEDTIdentification();
//        $result = $conn->prepare($sql1);
//        $result->bindParam(':email', $email);
//        $result->bindParam(':code', $code);
//        $result->bindParam(':time', $time);
//        $result->execute();
//
//        $mail->isHTML(true);
//        $mail->Subject = 'Code de Vérification';
//        $mail->Body = 'Voici votre code de vérification : '.$code;
//
//        if($mail->send()){
//            header('location: changeMDP.html');
//            exit();
//        }
//    } catch (PDOException $e) {
//        $erreur = "Erreur lors de l'envoi de l'email : " . $mail->ErrorInfo;
//    }
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $email = isset($_POST["email"]) ? htmlspecialchars($_POST["email"]) : "";
    $code = rand(0,999999);
    $code = sprintf('%06d', $code);
    sendCode($email, $code);
}

?>