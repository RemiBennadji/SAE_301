<?php

function getConnectionBDD(){
    {
        try {
            $dbname = "edt";
            $user = "iutinfo340";
            $password = "jWBfxD1E";

            $dsn = "pgsql:host=iutinfo-sgbd.uphf.fr; dbname=$dbname";

            //$dsn = "pgsql:host=2a02:842a:81db:d601:88a7:f394:4625:e9ff;dbname=sae";
            //$user = "lecteur";
            //$password = "root";

            $pdo = new PDO($dsn, $user, $password);
            $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

            return $pdo;
        } catch (PDOException $e) {
            echo "Erreur de connexion : " . $e->getMessage();
            exit();
        }
    }
}
function getConnectionBDDEDTIdentification(){
        try {
            $dbname = "iutinfo301";
            $user = "iutinfo301";
            $password = "YAH+rfI3";
            $dsn = "pgsql:host=iutinfo-sgbd.uphf.fr;dbname=$dbname";

            //$dsn = "pgsql:host=2a02:842a:81db:d601:88a7:f394:4625:e9ff;dbname=sae";
            //$user = "lecteur";
            //$password = "root";

            $pdo = new PDO($dsn, $user, $password);
            $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

            return $pdo;
        } catch (PDOException $e) {
            echo "Erreur de connexion : " . $e->getMessage();
            exit();
        }
}

//changeMDP
function recupererInfo(){
    return "SELECT mail, identifiant, role FROM infoutilisateur WHERE mail=:mail";
    $conn = getConnectionBDD();
    $connect = $conn->prepare($crea);
    $connect->bindParam(':mail', $_SESSION['mail']);
    $connect->execute();
    $connect = $connect->fetchAll(PDO::FETCH_ASSOC);
    return $connect;
}

//changeMDP
function getMail(){
    return "SELECT email FROM codeverif WHERE codev =:code";
    $conn = getConnectionBDD();
    $recup = $conn->prepare($recupMail);
    $recup->bindParam('code', $_SESSION['code']);
    $recup->execute();
    $recup = $recup->fetchAll(PDO::FETCH_ASSOC);
    return $recup;
}

//codeVerif
function codeExpire(){
    return "DELETE FROM codeverif WHERE expiration < NOW()";
}

//codeVerif
function recupererCode(){
    return "SELECT codev, email FROM codeverif WHERE codev = :code";
}

//codeVerif
function suppCode(){
    return "DELETE FROM codeverif WHERE codev = :code";
}

//creationCompte.php
function insertStmt($res,$nom,$prenom)
{
    $conn = getConnectionBDD();
    $insertStmt = $conn->prepare("INSERT INTO etudiants (civilite, nom, prenom, semestre, nom_ressource, email) VALUES (:civilite, :nom, :prenom, :semestre, :nom_ressource, :email)");
    $insertStmt->execute([
        'civilite' => $res[0],
        'nom' => $nom,
        'prenom' => $prenom,
        'semestre' => $res[3],
        'nom_ressource' => $res[4],
        'email'=>$res[5]
    ]);
    
    //creationCompte.php
    function verifEtu()
    {
        $conn = getConnectionBDD();
        $sql1 = $conn->prepare("SELECT COUNT(*) FROM etudiants WHERE nom = :nom AND prenom = :prenom");
        $sql1->bindParam(':nom', $nom);
        $sql1->bindParam(':prenom', $prenom);
        $sql1->execute();
        return $sql1;
    }
    

    //Demande.PHP
    function recupNomPrenomProf($mail)
    {
        $info = "SELECT nom, prenom FROM collegue WHERE mail = :MAIL";
        $conn = getConnectionBDD();
        $getInfo = $conn->prepare($info);
        $getInfo->bindParam(":MAIL", $mail);
        $getInfo->execute();

        return $getInfo->fetch(PDO::FETCH_ASSOC);
    }

    //Demande.PHP
    function insertDemande($timestamp,$raison,$nom,$prenom,$type)
    {
        $sql = "INSERT INTO demande(dateDemande, raison, nom, prenom, typeDemande) 
            VALUES(:DATEDEMANDE, :RAISON, :NOM, :PRENOM, :TYPEDEMANDE)";
        $conn = getConnectionBDD();
        $insertion = $conn->prepare($sql);
        $insertion->bindParam(":DATEDEMANDE", $timestamp);
        $insertion->bindParam(":RAISON", $raison);
        $insertion->bindParam(":NOM", $nom);
        $insertion->bindParam(":PRENOM", $prenom);
        $insertion->bindParam(":TYPEDEMANDE", $type);
        $insertion->execute();
    }


}