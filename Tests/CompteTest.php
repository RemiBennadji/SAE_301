<?php

use PHPUnit\Framework\TestCase;

// Inclure la classe Compte et ses dépendances
require_once "../Model/Classe/Compte.php";

class CompteTest extends TestCase
{
    private $compte;

    protected function setUp(): void
    {
        // Crée une classe concrète fictive pour tester Compte
        $this->compte = new Administrateur("admin"); // Exemple de rôle
    }

    public function testVerifMdp()
    {
        // Cas valide : mot de passe respectant les critères
        $mdpValide = "AbcR123!*";
        $this->assertTrue($this->compte->verifMdp($mdpValide), "Mot de passe valide non reconnu comme tel.");

        // Cas invalide : mot de passe trop court
        $mdpCourt = "Ab1!";
        $this->assertFalse($this->compte->verifMdp($mdpCourt), "Mot de passe trop court reconnu comme valide.");

        // Cas invalide : mot de passe sans caractère spécial
        $mdpSansCaraSpec = "Abc12345";
        $this->assertFalse($this->compte->verifMdp($mdpSansCaraSpec), "Mot de passe sans caractère spécial reconnu comme valide.");

        // Cas invalide : mot de passe sans chiffres
        $mdpSansChiffre = "Abcdefg!";
        $this->assertFalse($this->compte->verifMdp($mdpSansChiffre), "Mot de passe sans chiffre reconnu comme valide.");
    }

    public function testGenererMDP()
    {
        $mdp = $this->compte->genererMDP();

        // Vérifie que le mot de passe respecte les critères de verifMdp
        $this->assertTrue($this->compte->verifMdp($mdp), "Le mot de passe généré ne respecte pas les critères.");

        // Vérifie que la longueur du mot de passe est suffisante
        $this->assertGreaterThanOrEqual(8, strlen($mdp), "Le mot de passe généré est trop court.");
    }

    public function testGenererIdentifiant()
    {
        // Définit les valeurs de nom et prénom
        $this->compte->setNom("Dupont");
        $this->compte->setPrenom("Jean");

        // Génère l'identifiant
        $identifiant = $this->compte->genererIdentifiant();

        // Vérifie que l'identifiant est correctement formaté
        $this->assertEquals("jean.dupont", $identifiant, "L'identifiant généré est incorrect.");
    }
}
