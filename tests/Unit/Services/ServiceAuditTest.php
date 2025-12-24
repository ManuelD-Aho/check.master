<?php

declare(strict_types=1);

namespace Tests\Unit\Services;

use PHPUnit\Framework\TestCase;
use App\Services\Security\ServiceAudit;
use App\Models\Pister;

/**
 * Tests unitaires pour ServiceAudit
 * 
 * @covers \App\Services\Security\ServiceAudit
 */
class ServiceAuditTest extends TestCase
{
    /**
     * @test
     * Test de la méthode log
     */
    public function testLog(): void
    {
        // Vérifie que la méthode log existe et accepte les bons paramètres
        $this->assertTrue(method_exists(ServiceAudit::class, 'log'));

        $reflection = new \ReflectionMethod(ServiceAudit::class, 'log');
        $params = $reflection->getParameters();

        $this->assertCount(4, $params);
        $this->assertEquals('action', $params[0]->getName());
        $this->assertEquals('entiteType', $params[1]->getName());
        $this->assertEquals('entiteId', $params[2]->getName());
        $this->assertEquals('snapshot', $params[3]->getName());
    }

    /**
     * @test
     * Test de la méthode logLogin
     */
    public function testLogLogin(): void
    {
        $this->assertTrue(method_exists(ServiceAudit::class, 'logLogin'));

        $reflection = new \ReflectionMethod(ServiceAudit::class, 'logLogin');
        $params = $reflection->getParameters();

        $this->assertCount(1, $params);
        $this->assertEquals('userId', $params[0]->getName());
        $this->assertEquals('int', $params[0]->getType()->getName());
    }

    /**
     * @test
     * Test de la méthode logLogout
     */
    public function testLogLogout(): void
    {
        $this->assertTrue(method_exists(ServiceAudit::class, 'logLogout'));

        $reflection = new \ReflectionMethod(ServiceAudit::class, 'logLogout');
        $params = $reflection->getParameters();

        $this->assertCount(1, $params);
        $this->assertEquals('userId', $params[0]->getName());
    }

    /**
     * @test
     * Test de la méthode logLoginEchec
     */
    public function testLogLoginEchec(): void
    {
        $this->assertTrue(method_exists(ServiceAudit::class, 'logLoginEchec'));

        $reflection = new \ReflectionMethod(ServiceAudit::class, 'logLoginEchec');
        $params = $reflection->getParameters();

        $this->assertCount(2, $params);
        $this->assertEquals('login', $params[0]->getName());
        $this->assertEquals('raison', $params[1]->getName());
    }

    /**
     * @test
     * Test de la méthode logDeconnexionForcee
     */
    public function testLogDeconnexionForcee(): void
    {
        $this->assertTrue(method_exists(ServiceAudit::class, 'logDeconnexionForcee'));

        $reflection = new \ReflectionMethod(ServiceAudit::class, 'logDeconnexionForcee');
        $params = $reflection->getParameters();

        $this->assertCount(3, $params);
        $this->assertEquals('userId', $params[0]->getName());
        $this->assertEquals('sessionId', $params[1]->getName());
        $this->assertEquals('adminId', $params[2]->getName());
    }

    /**
     * @test
     * Test de la méthode logCreation
     */
    public function testLogCreation(): void
    {
        $this->assertTrue(method_exists(ServiceAudit::class, 'logCreation'));

        $reflection = new \ReflectionMethod(ServiceAudit::class, 'logCreation');
        $params = $reflection->getParameters();

        $this->assertCount(3, $params);
        $this->assertEquals('entiteType', $params[0]->getName());
        $this->assertEquals('entiteId', $params[1]->getName());
        $this->assertEquals('donnees', $params[2]->getName());
    }

    /**
     * @test
     * Test de la méthode logModification
     */
    public function testLogModification(): void
    {
        $this->assertTrue(method_exists(ServiceAudit::class, 'logModification'));

        $reflection = new \ReflectionMethod(ServiceAudit::class, 'logModification');
        $params = $reflection->getParameters();

        $this->assertCount(4, $params);
        $this->assertEquals('entiteType', $params[0]->getName());
        $this->assertEquals('entiteId', $params[1]->getName());
        $this->assertEquals('avant', $params[2]->getName());
        $this->assertEquals('apres', $params[3]->getName());
    }

    /**
     * @test
     * Test de la méthode logSuppression
     */
    public function testLogSuppression(): void
    {
        $this->assertTrue(method_exists(ServiceAudit::class, 'logSuppression'));

        $reflection = new \ReflectionMethod(ServiceAudit::class, 'logSuppression');
        $params = $reflection->getParameters();

        $this->assertCount(3, $params);
        $this->assertEquals('entiteType', $params[0]->getName());
        $this->assertEquals('entiteId', $params[1]->getName());
        $this->assertEquals('donnees', $params[2]->getName());
    }

    /**
     * @test
     * Test que la méthode logFichier est privée
     */
    public function testLogFichierEstPrivee(): void
    {
        $reflection = new \ReflectionMethod(ServiceAudit::class, 'logFichier');
        $this->assertTrue($reflection->isPrivate());
    }

    /**
     * @test
     * Test des constantes de l'entité Pister
     */
    public function testConstantesPisterExistent(): void
    {
        $this->assertTrue(defined(Pister::class . '::ACTION_LOGIN'));
        $this->assertTrue(defined(Pister::class . '::ACTION_LOGOUT'));
        $this->assertTrue(defined(Pister::class . '::ACTION_LOGIN_ECHEC'));
        $this->assertTrue(defined(Pister::class . '::ACTION_DECONNEXION_FORCEE'));
        $this->assertTrue(defined(Pister::class . '::ACTION_CREATION'));
        $this->assertTrue(defined(Pister::class . '::ACTION_MODIFICATION'));
        $this->assertTrue(defined(Pister::class . '::ACTION_SUPPRESSION'));
    }
}
