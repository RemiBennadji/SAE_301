<?php

class Administrateur extends Compte
{
    public function __construct($nom, $prenom)
    {
        parent::__construct();
        $this->nom=$nom;
        $this->prenom=$prenom;
    }
}