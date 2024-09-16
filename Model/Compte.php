<?php

abstract class Compte
{
private $id;
private $mdp;

public function __construct(){
    $this->id = "None";
    $this->mdp = "";
}
public function getId(){
    return $this->id;
}
public function getMdp(){
    return $this->mdp;
}
public function setMdp($mdp){
    $this->mdp = $mdp;
}

}