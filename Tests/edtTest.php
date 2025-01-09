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
        $dateResultat = $this->edt->incrementerSemaine($dateInitiale);

        $this->assertEquals($dateAttendue, $dateResultat, "L'incrémentation d'une semaine depuis $dateInitiale a échoué.");

        // Test avec un lundi
        $dateInitiale = "2025-01-06"; // Un lundi
        $dateAttendue = "2025-01-13"; // Le lundi suivant
        $dateResultat = $this->edt->incrementerSemaine($dateInitiale);

        $this->assertEquals($dateAttendue, $dateResultat, "L'incrémentation d'une semaine depuis $dateInitiale a échoué.");
    }

    public function testDecrementerSemaine()
    {
        // Cas de base
        $dateInitiale = "2025-01-08"; // Un mercredi
        $dateAttendue = "2025-01-01"; // Le mercredi précédent
        $dateResultat = $this->edt->decrementerSemaine($dateInitiale);

        $this->assertEquals($dateAttendue, $dateResultat, "La décrémentation d'une semaine depuis $dateInitiale a échoué.");

        // Test avec un lundi
        $dateInitiale = "2025-01-13"; // Un lundi
        $dateAttendue = "2025-01-06"; // Le lundi précédent
        $dateResultat = $this->edt->decrementerSemaine($dateInitiale);

        $this->assertEquals($dateAttendue, $dateResultat, "La décrémentation d'une semaine depuis $dateInitiale a échoué.");
    }
}
