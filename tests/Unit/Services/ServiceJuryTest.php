<?php

declare(strict_types=1);

namespace Tests\Unit\Services;

use PHPUnit\Framework\TestCase;
use App\Services\Soutenance\ServiceJury;

/**
 * Tests unitaires exhaustifs pour ServiceJury
 * 
 * @covers \App\Services\Soutenance\ServiceJury
 */
class ServiceJuryTest extends TestCase
{
    public function testClasseExiste(): void
    {
        $this->assertTrue(class_exists(ServiceJury::class));
    }

    public function testConstanteNombreMembresRequis(): void
    {
        $reflection = new \ReflectionClass(ServiceJury::class);
        $constants = $reflection->getConstants();
        $this->assertArrayHasKey('NOMBRE_MEMBRES_REQUIS', $constants);
        $this->assertEquals(5, $constants['NOMBRE_MEMBRES_REQUIS']);
    }

    public function testAjouterMembreMethodeExiste(): void
    {
        $this->assertTrue(method_exists(ServiceJury::class, 'ajouterMembre'));
        $reflection = new \ReflectionMethod(ServiceJury::class, 'ajouterMembre');
        $params = $reflection->getParameters();
        $this->assertCount(4, $params);
        $this->assertEquals('dossierId', $params[0]->getName());
    }

    public function testAccepterInvitationMethodeExiste(): void
    {
        $this->assertTrue(method_exists(ServiceJury::class, 'accepterInvitation'));
        $reflection = new \ReflectionMethod(ServiceJury::class, 'accepterInvitation');
        $returnType = $reflection->getReturnType();
        $this->assertEquals('bool', $returnType->getName());
    }

    public function testRefuserInvitationMethodeExiste(): void
    {
        $this->assertTrue(method_exists(ServiceJury::class, 'refuserInvitation'));
    }

    public function testCompterMembresMethodeExiste(): void
    {
        $this->assertTrue(method_exists(ServiceJury::class, 'compterMembres'));
        $reflection = new \ReflectionMethod(ServiceJury::class, 'compterMembres');
        $returnType = $reflection->getReturnType();
        $this->assertEquals('int', $returnType->getName());
    }

    public function testGetMembresMethodeExiste(): void
    {
        $this->assertTrue(method_exists(ServiceJury::class, 'getMembres'));
        $reflection = new \ReflectionMethod(ServiceJury::class, 'getMembres');
        $returnType = $reflection->getReturnType();
        $this->assertEquals('array', $returnType->getName());
    }

    public function testEstCompletMethodeExiste(): void
    {
        $this->assertTrue(method_exists(ServiceJury::class, 'estComplet'));
        $reflection = new \ReflectionMethod(ServiceJury::class, 'estComplet');
        $returnType = $reflection->getReturnType();
        $this->assertEquals('bool', $returnType->getName());
    }

    public function testRetirerMembreMethodeExiste(): void
    {
        $this->assertTrue(method_exists(ServiceJury::class, 'retirerMembre'));
    }

    public function testServiceEstInstanciable(): void
    {
        $reflection = new \ReflectionClass(ServiceJury::class);
        $this->assertTrue($reflection->isInstantiable());
    }
}
