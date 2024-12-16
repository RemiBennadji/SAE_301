<?php
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;


require __DIR__ . '/../../vendor/autoload.php';

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
        if($this->expediteur !='' && $this->destinataire !='' && $this->objet !='' && $this->message !=''){
            try {
                $this->setParam(); // Configure PHPMailer
                if($this->param){
                    $this->fonctionMail->isHTML(true);
                    $this->fonctionMail->Body = $this->message;
                    $this->fonctionMail->Subject = $this->objet;
                    $this->fonctionMail->send();
                    echo 'E-mail envoyé avec succès.';
                }
            } catch (Exception $e) {
                echo 'Erreur lors de l\'envoi : ' . $this->fonctionMail->ErrorInfo;
            }
        } else {
            echo 'Il manque une information.';
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
        $this->fonctionMail = new PHPMailer(true);

        $this->fonctionMail->isSMTP();
        $this->fonctionMail->Host = 'smtp.gmail.com';
        $this->fonctionMail->SMTPAuth = true;
        $this->fonctionMail->Username = $this->expediteur;
        $this->fonctionMail->Password = $this->mdp;
        $this->fonctionMail->SMTPSecure = PHPMailer::ENCRYPTION_SMTPS;
        $this->fonctionMail->Port = 465;
        $this->fonctionMail->setFrom($this->expediteur, 'Sae-EDT');
        $this->fonctionMail->addAddress($this->destinataire);

        $this->param = true;
    }


    public function getFonctionMail()
    {
        return $this->fonctionMail;
    }





}