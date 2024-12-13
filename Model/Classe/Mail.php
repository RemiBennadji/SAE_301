<?php
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require 'PHPMailer/src/Exception.php';
require 'PHPMailer/src/PHPMailer.php';
require 'PHPMailer/src/SMTP.php';
require 'vendor/autoload.php';
require '../../Controller/ConnectionBDD.php';

class Mail
{
    private $expediteur;
    private $fonctionMail;
    private $destinataire;
    private $mdp;
    private $objet;
    private $message;
    private $param;

    public function __construct(){
        $this->expediteur = '';
        $this->destinataire = '';
        $this->objet = '';
        $this->message = '';
    }

    public function creerMail(){
        if($this->expediteur !='' and $this->destinataire !='' and $this->objet !='' and $this->message !='' ){
            if($this->getParam()){
                $this->fonctionMail->isHTML(true);
                $this->fonctionMail->body($this->message);
                $this->fonctionMail->subject($this->objet);
                $this->fonctionMail->send();
            }
        }else{
            echo 'Il manque une information';
        }
    }
    public function getExpediteur()
    {
        return $this->expediteur;
    }

    public function setExpediteur($expediteur)
    {
        $this->expediteur = $expediteur;
    }

    public function getDestinataire()
    {
        return $this->destinataire;
    }

    public function setDestinataire($destinataire)
    {
        $this->destinataire = $destinataire;
    }


    public function getObjet()
    {
        return $this->objet;
    }


    public function setObjet($objet)
    {
        $this->objet = $objet;
    }

    public function getMessage()
    {
        return $this->message;
    }

    public function setMessage($message)
    {
        $this->message = $message;
    }

    public function getMdp()
    {
        return $this->mdp;
    }

    public function setMdp($mdp)
    {
        $this->mdp = $mdp;
    }

    public function getParam()
    {
        return $this->param;
    }

    public function setParam()
    {
        $fonctionMail = new PHPMailer(true);
        $time = strtotime("now");

        $fonctionMail->isSMTP();
        $fonctionMail->Host = 'smtp.gmail.com';
        $fonctionMail->SMTPAuth = true;
        $fonctionMail->Username = $this->expediteur;
        $fonctionMail->Password = $this->mdp;
        $fonctionMail->SMTPSecure = PHPMailer::ENCRYPTION_SMTPS;
        $fonctionMail->Port = 465;
        $fonctionMail->setFrom($this->expediteur, 'Sae-EDT');
        $fonctionMail->addAddress($this->destinataire);
        $this->param = true;
    }





}