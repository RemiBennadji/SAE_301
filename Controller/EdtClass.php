<?php

class EdtClass
{
    private $annee;
    private $date;
    private $groupe;
    private $edt;//Liste pour stocker toutes les semaines de l edt

    function __construct(){
        $this->annee = 1;
        $this->groupe = 'C1';
        $this->date = ' 2025-01-06';

        //$this->annee = $_COOKIE['annee'];
        //$this->groupe = $_COOKIE['groupe'];
        //$this->date = current;
    }

    function recupererEdt (){

    }

    public function getAnnee(){
        return $this->annee;
    }

    public function getDate(){
        return $this->date;
    }

    public function getGroupe(){
        return $this->groupe;
    }
}