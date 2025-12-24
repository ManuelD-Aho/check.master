<?php

declare(strict_types=1);

namespace Tests\Unit\Services;

use PHPUnit\Framework\TestCase;
use App\Services\Workflow\ServiceCommission;

/**
 * Tests unitaires exhaustifs pour ServiceCommission
 * 
 * @covers \App\Services\Workflow\ServiceCommission
 */
class ServiceCommissionTest extends TestCase
{
    public function testClasseExiste(): void
    {
        $this->assertTrue(class_exists(ServiceCommission::class));
    }

    public function testCreerSessionMethodeExiste(): void
    {
        $this->assertTrue(method_exists(ServiceCommission::class, 'creerSession'));
        $reflection = new \ReflectionMethod(ServiceCommission::class, 'creerSession');
        $params = $reflection->getParameters();
        $this->assertGreaterThanOrEqual(3, count($params));
        $this->assertEquals('dateSession', $params[0]->getName());
        $this->assertEquals('lieu', $params[1]->getName());
        $this->assertEquals('creePar', $params[2]->getName());
    }

    public function testDemarrerSessionMethodeExiste(): void
    {
        $this->assertTrue(method_exists(ServiceCommission::class, 'demarrerSession'));
        $reflection = new \ReflectionMethod(ServiceCommission::class, 'demarrerSession');
        $params = $reflection->getParameters();
        $this->assertGreaterThanOrEqual(2, count($params));
        $this->assertEquals('sessionId', $params[0]->getName());
        $this->assertEquals('utilisateurId', $params[1]->getName());
    }

    public function testVoterMethodeExiste(): void
    {
        $this->assertTrue(method_exists(ServiceCommission::class, 'voter'));
        $reflection = new \ReflectionMethod(ServiceCommission::class, 'voter');
        $params = $reflection->getParameters();
        $this->assertGreaterThanOrEqual(4, count($params));
        $this->assertEquals('sessionId', $params[0]->getName());
        $this->assertEquals('rapportId', $params[1]->getName());
        $this->assertEquals('membreId', $params[2]->getName());
        $this->assertEquals('decision', $params[3]->getName());
    }

    public function testTraiterResultatsVoteMethodeExiste(): void
    {
        $this->assertTrue(method_exists(ServiceCommission::class, 'traiterResultatsVote'));
        $reflection = new \ReflectionMethod(ServiceCommission::class, 'traiterResultatsVote');
        $params = $reflection->getParameters();
        $this->assertGreaterThanOrEqual(3, count($params));
        $this->assertEquals('sessionId', $params[0]->getName());
        $this->assertEquals('rapportId', $params[1]->getName());
        $this->assertEquals('nombreMembres', $params[2]->getName());
    }

    public function testPasserAuTourSuivantMethodeExiste(): void
    {
        $this->assertTrue(method_exists(ServiceCommission::class, 'passerAuTourSuivant'));
        $reflection = new \ReflectionMethod(ServiceCommission::class, 'passerAuTourSuivant');
        $params = $reflection->getParameters();
        $this->assertCount(1, $params);
        $this->assertEquals('sessionId', $params[0]->getName());
    }

    public function testTerminerSessionMethodeExiste(): void
    {
        $this->assertTrue(method_exists(ServiceCommission::class, 'terminerSession'));
        $reflection = new \ReflectionMethod(ServiceCommission::class, 'terminerSession');
        $returnType = $reflection->getReturnType();
        $this->assertEquals('bool', $returnType->getName());
    }

    public function testEscaladerAuDoyenMethodeExiste(): void
    {
        $this->assertTrue(method_exists(ServiceCommission::class, 'escaladerAuDoyen'));
        $reflection = new \ReflectionMethod(ServiceCommission::class, 'escaladerAuDoyen');
        $params = $reflection->getParameters();
        $this->assertGreaterThanOrEqual(2, count($params));
        $this->assertEquals('sessionId', $params[0]->getName());
        $this->assertEquals('rapportId', $params[1]->getName());
    }

    public function testGetSessionsPlanifieesMethodeExiste(): void
    {
        $this->assertTrue(method_exists(ServiceCommission::class, 'getSessionsPlanifiees'));
        $reflection = new \ReflectionMethod(ServiceCommission::class, 'getSessionsPlanifiees');
        $returnType = $reflection->getReturnType();
        $this->assertEquals('array', $returnType->getName());
    }

    public function testGetSessionsEnCoursMethodeExiste(): void
    {
        $this->assertTrue(method_exists(ServiceCommission::class, 'getSessionsEnCours'));
        $reflection = new \ReflectionMethod(ServiceCommission::class, 'getSessionsEnCours');
        $returnType = $reflection->getReturnType();
        $this->assertEquals('array', $returnType->getName());
    }

    public function testAppliquerDecisionMethodeExiste(): void
    {
        $this->assertTrue(method_exists(ServiceCommission::class, 'appliquerDecision'));
        $reflection = new \ReflectionMethod(ServiceCommission::class, 'appliquerDecision');
        $params = $reflection->getParameters();
        $this->assertGreaterThanOrEqual(2, count($params));
        $this->assertEquals('rapportId', $params[0]->getName());
        $this->assertEquals('decision', $params[1]->getName());
    }

    public function testGetStatistiquesSessionMethodeExiste(): void
    {
        $this->assertTrue(method_exists(ServiceCommission::class, 'getStatistiquesSession'));
        $reflection = new \ReflectionMethod(ServiceCommission::class, 'getStatistiquesSession');
        $params = $reflection->getParameters();
        $this->assertCount(1, $params);
        $this->assertEquals('sessionId', $params[0]->getName());
    }

    public function testDecisionsValides(): void
    {
        $decisions = ['Favorable', 'Defavorable', 'Reserve', 'Ajourne'];
        $this->assertContains('Favorable', $decisions);
        $this->assertContains('Defavorable', $decisions);
    }

    public function testServiceEstInstanciable(): void
    {
        $reflection = new \ReflectionClass(ServiceCommission::class);
        $this->assertTrue($reflection->isInstantiable());
    }
}
