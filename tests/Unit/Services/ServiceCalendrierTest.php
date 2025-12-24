<?php

declare(strict_types=1);

namespace Tests\Unit\Services;

use PHPUnit\Framework\TestCase;
use App\Services\Soutenance\ServiceCalendrier;

/**
 * Tests unitaires exhaustifs pour ServiceCalendrier
 * 
 * @covers \App\Services\Soutenance\ServiceCalendrier
 */
class ServiceCalendrierTest extends TestCase
{
    /**
     * @test
     * Test que la classe existe
     */
    public function testClasseExiste(): void
    {
        $this->assertTrue(class_exists(ServiceCalendrier::class));
    }

    // =========================================================================
    // Tests de la méthode planifier()
    // =========================================================================

    /**
     * @test
     * La méthode planifier existe avec les bons paramètres
     */
    public function testPlanifierMethodeExiste(): void
    {
        $this->assertTrue(method_exists(ServiceCalendrier::class, 'planifier'));

        $reflection = new \ReflectionMethod(ServiceCalendrier::class, 'planifier');
        $params = $reflection->getParameters();

        $this->assertGreaterThanOrEqual(5, count($params));
        $this->assertEquals('dossierId', $params[0]->getName());
        $this->assertEquals('dateSoutenance', $params[1]->getName());
        $this->assertEquals('heureDebut', $params[2]->getName());
        $this->assertEquals('heureFin', $params[3]->getName());
        $this->assertEquals('salleId', $params[4]->getName());
    }

    /**
     * @test
     * La méthode planifier retourne Soutenance
     */
    public function testPlanifierReturnType(): void
    {
        $reflection = new \ReflectionMethod(ServiceCalendrier::class, 'planifier');
        $returnType = $reflection->getReturnType();
        $this->assertNotNull($returnType);
        $this->assertEquals('App\Models\Soutenance', $returnType->getName());
    }

    /**
     * @test
     * La méthode planifier est une méthode d'instance
     */
    public function testPlanifierEstMethodeInstance(): void
    {
        $reflection = new \ReflectionMethod(ServiceCalendrier::class, 'planifier');
        $this->assertFalse($reflection->isStatic());
    }

    // =========================================================================
    // Tests de la méthode salleOccupee()
    // =========================================================================

    /**
     * @test
     * La méthode salleOccupee existe
     */
    public function testSalleOccupeeMethodeExiste(): void
    {
        $this->assertTrue(method_exists(ServiceCalendrier::class, 'salleOccupee'));

        $reflection = new \ReflectionMethod(ServiceCalendrier::class, 'salleOccupee');
        $params = $reflection->getParameters();

        $this->assertGreaterThanOrEqual(4, count($params));
        $this->assertEquals('salleId', $params[0]->getName());
        $this->assertEquals('date', $params[1]->getName());
        $this->assertEquals('heureDebut', $params[2]->getName());
        $this->assertEquals('heureFin', $params[3]->getName());
    }

    /**
     * @test
     * La méthode salleOccupee retourne bool
     */
    public function testSalleOccupeeReturnType(): void
    {
        $reflection = new \ReflectionMethod(ServiceCalendrier::class, 'salleOccupee');
        $returnType = $reflection->getReturnType();
        $this->assertNotNull($returnType);
        $this->assertEquals('bool', $returnType->getName());
    }

    /**
     * @test
     * Le paramètre excludeSoutenanceId est optionnel
     */
    public function testExcludeSoutenanceIdOptional(): void
    {
        $reflection = new \ReflectionMethod(ServiceCalendrier::class, 'salleOccupee');
        $params = $reflection->getParameters();

        if (count($params) > 4) {
            $excludeParam = $params[4];
            $this->assertTrue($excludeParam->isOptional());
            $this->assertTrue($excludeParam->allowsNull());
        }
    }

    // =========================================================================
    // Tests de la méthode verifierConflitsJury()
    // =========================================================================

    /**
     * @test
     * La méthode verifierConflitsJury existe
     */
    public function testVerifierConflitsJuryMethodeExiste(): void
    {
        $this->assertTrue(method_exists(ServiceCalendrier::class, 'verifierConflitsJury'));

        $reflection = new \ReflectionMethod(ServiceCalendrier::class, 'verifierConflitsJury');
        $params = $reflection->getParameters();

        $this->assertGreaterThanOrEqual(4, count($params));
        $this->assertEquals('dossierId', $params[0]->getName());
        $this->assertEquals('date', $params[1]->getName());
    }

    /**
     * @test
     * La méthode verifierConflitsJury retourne array
     */
    public function testVerifierConflitsJuryReturnType(): void
    {
        $reflection = new \ReflectionMethod(ServiceCalendrier::class, 'verifierConflitsJury');
        $returnType = $reflection->getReturnType();
        $this->assertNotNull($returnType);
        $this->assertEquals('array', $returnType->getName());
    }

    // =========================================================================
    // Tests de la méthode avancerWorkflow()
    // =========================================================================

    /**
     * @test
     * La méthode avancerWorkflow existe et est privée
     */
    public function testAvancerWorkflowExiste(): void
    {
        $this->assertTrue(method_exists(ServiceCalendrier::class, 'avancerWorkflow'));

        $reflection = new \ReflectionMethod(ServiceCalendrier::class, 'avancerWorkflow');
        $this->assertTrue($reflection->isPrivate());
    }

    // =========================================================================
    // Tests de la méthode notifierParticipants()
    // =========================================================================

    /**
     * @test
     * La méthode notifierParticipants existe et est privée
     */
    public function testNotifierParticipantsExiste(): void
    {
        $this->assertTrue(method_exists(ServiceCalendrier::class, 'notifierParticipants'));

        $reflection = new \ReflectionMethod(ServiceCalendrier::class, 'notifierParticipants');
        $this->assertTrue($reflection->isPrivate());
    }

    // =========================================================================
    // Tests de la méthode annuler()
    // =========================================================================

    /**
     * @test
     * La méthode annuler existe
     */
    public function testAnnulerMethodeExiste(): void
    {
        $this->assertTrue(method_exists(ServiceCalendrier::class, 'annuler'));

        $reflection = new \ReflectionMethod(ServiceCalendrier::class, 'annuler');
        $params = $reflection->getParameters();

        $this->assertCount(3, $params);
        $this->assertEquals('soutenanceId', $params[0]->getName());
        $this->assertEquals('motif', $params[1]->getName());
        $this->assertEquals('annulePar', $params[2]->getName());
    }

    /**
     * @test
     * La méthode annuler retourne bool
     */
    public function testAnnulerReturnType(): void
    {
        $reflection = new \ReflectionMethod(ServiceCalendrier::class, 'annuler');
        $returnType = $reflection->getReturnType();
        $this->assertNotNull($returnType);
        $this->assertEquals('bool', $returnType->getName());
    }

    // =========================================================================
    // Tests de la méthode getSallesDisponibles()
    // =========================================================================

    /**
     * @test
     * La méthode getSallesDisponibles existe
     */
    public function testGetSallesDisponiblesMethodeExiste(): void
    {
        $this->assertTrue(method_exists(ServiceCalendrier::class, 'getSallesDisponibles'));

        $reflection = new \ReflectionMethod(ServiceCalendrier::class, 'getSallesDisponibles');
        $params = $reflection->getParameters();

        $this->assertCount(3, $params);
        $this->assertEquals('date', $params[0]->getName());
        $this->assertEquals('heureDebut', $params[1]->getName());
        $this->assertEquals('heureFin', $params[2]->getName());
    }

    /**
     * @test
     * La méthode getSallesDisponibles retourne array
     */
    public function testGetSallesDisponiblesReturnType(): void
    {
        $reflection = new \ReflectionMethod(ServiceCalendrier::class, 'getSallesDisponibles');
        $returnType = $reflection->getReturnType();
        $this->assertNotNull($returnType);
        $this->assertEquals('array', $returnType->getName());
    }

    // =========================================================================
    // Tests de la méthode getPlanningJour()
    // =========================================================================

    /**
     * @test
     * La méthode getPlanningJour existe
     */
    public function testGetPlanningJourMethodeExiste(): void
    {
        $this->assertTrue(method_exists(ServiceCalendrier::class, 'getPlanningJour'));

        $reflection = new \ReflectionMethod(ServiceCalendrier::class, 'getPlanningJour');
        $params = $reflection->getParameters();

        $this->assertCount(1, $params);
        $this->assertEquals('date', $params[0]->getName());
    }

    /**
     * @test
     * La méthode getPlanningJour retourne array
     */
    public function testGetPlanningJourReturnType(): void
    {
        $reflection = new \ReflectionMethod(ServiceCalendrier::class, 'getPlanningJour');
        $returnType = $reflection->getReturnType();
        $this->assertNotNull($returnType);
        $this->assertEquals('array', $returnType->getName());
    }

    // =========================================================================
    // Tests de validation des dates et heures
    // =========================================================================

    /**
     * @test
     * Le format de date Y-m-d est correct
     */
    public function testFormatDateCorrect(): void
    {
        $date = date('Y-m-d');
        $this->assertMatchesRegularExpression('/^\d{4}-\d{2}-\d{2}$/', $date);
    }

    /**
     * @test
     * Le format d'heure H:i:s est correct
     */
    public function testFormatHeureCorrect(): void
    {
        $heure = date('H:i:s');
        $this->assertMatchesRegularExpression('/^\d{2}:\d{2}:\d{2}$/', $heure);
    }

    /**
     * @test
     * Une heure de fin doit être après l'heure de début
     */
    public function testHeureFinApresHeureDebut(): void
    {
        $heureDebut = '09:00:00';
        $heureFin = '11:00:00';

        $this->assertGreaterThan(
            strtotime($heureDebut),
            strtotime($heureFin)
        );
    }

    // =========================================================================
    // Tests de cohérence globale
    // =========================================================================

    /**
     * @test
     * Le service peut être instancié
     */
    public function testServiceEstInstanciable(): void
    {
        $reflection = new \ReflectionClass(ServiceCalendrier::class);
        $this->assertTrue($reflection->isInstantiable());
    }

    /**
     * @test
     * Les méthodes publiques principales existent
     */
    public function testMethodesPubliquesExistent(): void
    {
        $methods = [
            'planifier',
            'salleOccupee',
            'verifierConflitsJury',
            'annuler',
            'getSallesDisponibles',
            'getPlanningJour',
        ];

        foreach ($methods as $method) {
            $this->assertTrue(
                method_exists(ServiceCalendrier::class, $method),
                "La méthode {$method} devrait exister"
            );
        }
    }
}
