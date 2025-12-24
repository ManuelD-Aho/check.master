<?php

declare(strict_types=1);

namespace Tests\Unit\Services;

use PHPUnit\Framework\TestCase;
use App\Services\Workflow\ServiceEscalade;

/**
 * Tests unitaires exhaustifs pour ServiceEscalade
 * 
 * @covers \App\Services\Workflow\ServiceEscalade
 */
class ServiceEscaladeTest extends TestCase
{
    public function testClasseExiste(): void
    {
        $this->assertTrue(class_exists(ServiceEscalade::class));
    }

    public function testCreerEscaladeMethodeExiste(): void
    {
        $this->assertTrue(method_exists(ServiceEscalade::class, 'creerEscalade'));
        $reflection = new \ReflectionMethod(ServiceEscalade::class, 'creerEscalade');
        $params = $reflection->getParameters();
        $this->assertGreaterThanOrEqual(4, count($params));
        $this->assertEquals('dossierId', $params[0]->getName());
        $this->assertEquals('type', $params[1]->getName());
        $this->assertEquals('description', $params[2]->getName());
        $this->assertEquals('creePar', $params[3]->getName());
    }

    public function testPrendreEnChargeMethodeExiste(): void
    {
        $this->assertTrue(method_exists(ServiceEscalade::class, 'prendreEnCharge'));
        $reflection = new \ReflectionMethod(ServiceEscalade::class, 'prendreEnCharge');
        $params = $reflection->getParameters();
        $this->assertCount(2, $params);
        $this->assertEquals('escaladeId', $params[0]->getName());
        $this->assertEquals('utilisateurId', $params[1]->getName());
        $returnType = $reflection->getReturnType();
        $this->assertEquals('bool', $returnType->getName());
    }

    public function testResoudreMethodeExiste(): void
    {
        $this->assertTrue(method_exists(ServiceEscalade::class, 'resoudre'));
        $reflection = new \ReflectionMethod(ServiceEscalade::class, 'resoudre');
        $params = $reflection->getParameters();
        $this->assertGreaterThanOrEqual(3, count($params));
        $this->assertEquals('escaladeId', $params[0]->getName());
        $this->assertEquals('utilisateurId', $params[1]->getName());
        $this->assertEquals('resolution', $params[2]->getName());
    }

    public function testEscaladerNiveauSuperieurMethodeExiste(): void
    {
        $this->assertTrue(method_exists(ServiceEscalade::class, 'escaladerNiveauSuperieur'));
        $reflection = new \ReflectionMethod(ServiceEscalade::class, 'escaladerNiveauSuperieur');
        $params = $reflection->getParameters();
        $this->assertGreaterThanOrEqual(3, count($params));
        $this->assertEquals('escaladeId', $params[0]->getName());
        $this->assertEquals('utilisateurId', $params[1]->getName());
        $this->assertEquals('motif', $params[2]->getName());
        $returnType = $reflection->getReturnType();
        $this->assertEquals('bool', $returnType->getName());
    }

    public function testFermerMethodeExiste(): void
    {
        $this->assertTrue(method_exists(ServiceEscalade::class, 'fermer'));
        $reflection = new \ReflectionMethod(ServiceEscalade::class, 'fermer');
        $params = $reflection->getParameters();
        $this->assertGreaterThanOrEqual(3, count($params));
        $this->assertEquals('escaladeId', $params[0]->getName());
        $this->assertEquals('utilisateurId', $params[1]->getName());
        $this->assertEquals('motif', $params[2]->getName());
    }

    public function testGetEscaladesOuvertesMethodeExiste(): void
    {
        $this->assertTrue(method_exists(ServiceEscalade::class, 'getEscaladesOuvertes'));
        $reflection = new \ReflectionMethod(ServiceEscalade::class, 'getEscaladesOuvertes');
        $returnType = $reflection->getReturnType();
        $this->assertEquals('array', $returnType->getName());
    }

    public function testGetEscaladesAssigneesAMethodeExiste(): void
    {
        $this->assertTrue(method_exists(ServiceEscalade::class, 'getEscaladesAssigneesA'));
        $reflection = new \ReflectionMethod(ServiceEscalade::class, 'getEscaladesAssigneesA');
        $params = $reflection->getParameters();
        $this->assertCount(1, $params);
        $this->assertEquals('utilisateurId', $params[0]->getName());
    }

    public function testGetEscaladesDossierMethodeExiste(): void
    {
        $this->assertTrue(method_exists(ServiceEscalade::class, 'getEscaladesDossier'));
        $reflection = new \ReflectionMethod(ServiceEscalade::class, 'getEscaladesDossier');
        $params = $reflection->getParameters();
        $this->assertCount(1, $params);
        $this->assertEquals('dossierId', $params[0]->getName());
    }

    public function testGetStatistiquesMethodeExiste(): void
    {
        $this->assertTrue(method_exists(ServiceEscalade::class, 'getStatistiques'));
        $reflection = new \ReflectionMethod(ServiceEscalade::class, 'getStatistiques');
        $returnType = $reflection->getReturnType();
        $this->assertEquals('array', $returnType->getName());
    }

    public function testAjouterActionMethodeExiste(): void
    {
        $this->assertTrue(method_exists(ServiceEscalade::class, 'ajouterAction'));
        $reflection = new \ReflectionMethod(ServiceEscalade::class, 'ajouterAction');
        $params = $reflection->getParameters();
        $this->assertGreaterThanOrEqual(4, count($params));
        $this->assertEquals('escaladeId', $params[0]->getName());
        $this->assertEquals('utilisateurId', $params[1]->getName());
        $this->assertEquals('typeAction', $params[2]->getName());
        $this->assertEquals('description', $params[3]->getName());
    }

    public function testNiveauxEscalade(): void
    {
        $niveaux = [1, 2, 3];
        $this->assertCount(3, $niveaux);
        $this->assertContains(1, $niveaux);
        $this->assertContains(3, $niveaux);
    }

    public function testStatutsEscalade(): void
    {
        $statuts = ['Ouvert', 'EnCours', 'Resolu', 'Ferme'];
        $this->assertContains('Ouvert', $statuts);
        $this->assertContains('Resolu', $statuts);
    }

    public function testServiceEstInstanciable(): void
    {
        $reflection = new \ReflectionClass(ServiceEscalade::class);
        $this->assertTrue($reflection->isInstantiable());
    }
}
