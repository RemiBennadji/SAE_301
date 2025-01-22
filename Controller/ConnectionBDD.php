<?php

function getConnectionBDD(){
    {
        try {
//            $dbname = "edt";
//            $user = "iutinfo301";
//            $password = "YAH+rfI3";
//            $dsn = "pgsql:host=iutinfo-sgbd.uphf.fr;dbname=$dbname";

            $dsn = "jdbc:postgresql://192.168.38.45:5432/sae301";
	    $dbname="sae301";
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
function getConnectionBDDEDTIdentification()
    {
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
