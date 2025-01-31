<?php

function getConnectionBDD(){
    {
        try {
//            $dbname = "edt";
//            $user = "iutinfo301";
//            $password = "YAH+rfI3";
//            $dsn = "pgsql:host=iutinfo-sgbd.uphf.fr;dbname=$dbname";

            $dsn = "pgsql:host=192.168.38.45";
            $dbname= "sae301";
            $user = "postgres";
            $password = "root";

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
            $dbname = "sae301";
            $user = "postgres";
            $password = "root";
            $dsn = "pgsql:host=192.168.38.45";

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
}

//changeMDP
function getMail(){
    return "SELECT email FROM codeverif WHERE codev =:code";
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



