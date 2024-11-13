<?php

class Secretariat extends Compte
{
    public function __construct($nom, $prenom)
    {
        parent::__construct("secretariat", $nom, $prenom);
    }
}