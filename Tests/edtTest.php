<?php

use PHPUnit\Framework\TestCase;
require_once "../Model/Classe/Edt.php";
class EdtTest extends TestCase
{
    private $edt;

    protected function setUp(): void
    {
        $this->edt = new Edt();
    }

    public function testIncrementerSemaine()
    {
        // Cas de base
        $dateInitiale = "2025-01-01"; // Un mercredi
        $dateAttendue = "2025-01-08"; // Le mercredi suivant
        $dateAttendueFausseBase = "2025-01-09";
        $dateResultat = $this->edt->incrementerSemaine($dateInitiale);

        $this->assertEquals($dateAttendue, $dateResultat, "L'incrémentation d'une semaine depuis $dateInitiale a échoué.");

        // Test avec un lundi
        $dateInitiale = "2025-01-06"; // Un lundi
        $dateAttendue = "2025-01-13"; // Le lundi suivant
        $dateAttendueFausseLundi = "2025-01-14";
        $dateResultat = $this->edt->incrementerSemaine($dateInitiale);

        $this->assertEquals($dateAttendue, $dateResultat, "L'incrémentation d'une semaine depuis $dateInitiale a échoué.");

        $this->assertNotEquals($dateAttendueFausseBase, $dateResultat, "L'incrémentation d'une semaine depuis a réussit donc le test est faux.");

        $this->assertNotEquals($dateAttendueFausseLundi, $dateResultat, "L'incrémentation d'une semaine depuis a réussit donc le test est faux.");
    }

    public function testDecrementerSemaine()
    {
        // Cas de base
        $dateInitiale = "2025-01-08"; // Un mercredi
        $dateAttendue = "2025-01-01"; // Le mercredi précédent
        $dateAttendueFausseBase = "2025-01-02";
        $dateResultat = $this->edt->decrementerSemaine($dateInitiale);

        $this->assertEquals($dateAttendue, $dateResultat, "La décrémentation d'une semaine depuis $dateInitiale a échoué.");

        // Test avec un lundi
        $dateInitiale = "2025-01-13"; // Un lundi
        $dateAttendue = "2025-01-06"; // Le lundi précédent
        $dateAttendueFausseLundi = "2025-01-07";
        $dateResultat = $this->edt->decrementerSemaine($dateInitiale);

        $this->assertEquals($dateAttendue, $dateResultat, "La décrémentation d'une semaine depuis $dateInitiale a échoué.");

        $this->assertNotEquals($dateAttendueFausseBase, $dateResultat, "La décrémentation d'une semaine a réussit donc le test est faux.");

        $this->assertNotEquals($dateAttendueFausseLundi, $dateResultat, "La décrémentation d'une semaine a réussit donc le test est faux.");
    }

    public function testSupprimerAccents()
    {
        $strInitial = "Rémi est séduisant";
        $strAttendue = "Remi est seduisant";
        $strAttendueFaux = "Rémi est séduisant";
        $strResultat = $this->edt->supprimerAccents($strInitial);

        $this->assertEquals($strAttendue, $strResultat);
        $this->assertNotEquals($strAttendueFaux, $strResultat);



    }
}
