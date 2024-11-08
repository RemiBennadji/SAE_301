<?php

class Professeur extends Compte
{
    public function __construct($nom, $prenom)
    {
        parent::__construct("professeur", $nom, $prenom);
    }
}