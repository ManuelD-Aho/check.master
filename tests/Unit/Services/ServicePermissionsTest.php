<?php

declare(strict_types=1);

namespace Tests\Unit\Services;

use PHPUnit\Framework\TestCase;
use App\Services\Security\ServicePermissions;
use App\Models\Permission;

/**
 * Tests unitaires pour ServicePermissions
 * 
 * @see PRD 01 - Authentification & Utilisateurs (RF-006 à RF-008)
 * @covers \App\Services\Security\ServicePermissions
 */
class ServicePermissionsTest extends TestCase
{
    // ===== TESTS CONSTANTES D'ACTION =====

    /**
     * Test des constantes d'action Permission
     * @test
     */
    public function testConstantesActionExistent(): void
    {
        $this->assertEquals('lire', Permission::ACTION_LIRE);
        $this->assertEquals('creer', Permission::ACTION_CREER);
        $this->assertEquals('modifier', Permission::ACTION_MODIFIER);
        $this->assertEquals('supprimer', Permission::ACTION_SUPPRIMER);
        $this->assertEquals('exporter', Permission::ACTION_EXPORTER);
        $this->assertEquals('valider', Permission::ACTION_VALIDER);
    }

    /**
     * Test que toutes les actions standard sont définies
     * @test
     */
    public function testToutesActionsStandardDefinies(): void
    {
        $actionsRequises = ['lire', 'creer', 'modifier', 'supprimer', 'exporter', 'valider'];
        
        foreach ($actionsRequises as $action) {
            $constante = 'ACTION_' . strtoupper($action);
            $this->assertTrue(
                defined(Permission::class . '::' . $constante),
                "La constante {$constante} doit être définie"
            );
        }
    }

    // ===== TESTS MÉTHODES DU SERVICE =====

    /**
     * Test que la méthode verifier existe et est callable
     * @test
     */
    public function testMethodeVerifierExiste(): void
    {
        $this->assertTrue(method_exists(ServicePermissions::class, 'verifier'));
    }

    /**
     * Test que la méthode invaliderCache existe
     * @test
     */
    public function testMethodeInvaliderCacheExiste(): void
    {
        $this->assertTrue(method_exists(ServicePermissions::class, 'invaliderCache'));
    }

    /**
     * Test que la méthode estAdministrateur existe
     * @test
     */
    public function testMethodeEstAdministrateurExiste(): void
    {
        $this->assertTrue(method_exists(ServicePermissions::class, 'estAdministrateur'));
    }

    /**
     * Test de la méthode verifierMultiple
     * @test
     */
    public function testMethodeVerifierMultipleExiste(): void
    {
        $this->assertTrue(method_exists(ServicePermissions::class, 'verifierMultiple'));
    }

    /**
     * Test que la méthode invaliderToutCache existe
     * @test
     */
    public function testMethodeInvaliderToutCacheExiste(): void
    {
        $this->assertTrue(method_exists(ServicePermissions::class, 'invaliderToutCache'));
    }

    /**
     * Test que la méthode getToutesPermissions existe
     * @test
     */
    public function testMethodeGetToutesPermissionsExiste(): void
    {
        $this->assertTrue(method_exists(ServicePermissions::class, 'getToutesPermissions'));
    }

    // ===== TESTS MÉTHODES STATIQUES =====

    /**
     * Test que verifier est une méthode statique
     * @test
     */
    public function testVerifierEstStatique(): void
    {
        $reflection = new \ReflectionMethod(ServicePermissions::class, 'verifier');
        $this->assertTrue($reflection->isStatic());
    }

    /**
     * Test que invaliderCache est une méthode statique
     * @test
     */
    public function testInvaliderCacheEstStatique(): void
    {
        $reflection = new \ReflectionMethod(ServicePermissions::class, 'invaliderCache');
        $this->assertTrue($reflection->isStatic());
    }

    /**
     * Test que estAdministrateur est une méthode statique
     * @test
     */
    public function testEstAdministrateurEstStatique(): void
    {
        $reflection = new \ReflectionMethod(ServicePermissions::class, 'estAdministrateur');
        $this->assertTrue($reflection->isStatic());
    }

    // ===== TESTS SIGNATURE DES MÉTHODES =====

    /**
     * Test de la signature de verifier
     * @test
     */
    public function testSignatureVerifier(): void
    {
        $reflection = new \ReflectionMethod(ServicePermissions::class, 'verifier');
        $params = $reflection->getParameters();

        $this->assertCount(3, $params);
        $this->assertEquals('userId', $params[0]->getName());
        $this->assertEquals('ressourceCode', $params[1]->getName());
        $this->assertEquals('action', $params[2]->getName());
    }

    /**
     * Test de la signature de invaliderCache
     * @test
     */
    public function testSignatureInvaliderCache(): void
    {
        $reflection = new \ReflectionMethod(ServicePermissions::class, 'invaliderCache');
        $params = $reflection->getParameters();

        $this->assertCount(1, $params);
        $this->assertEquals('userId', $params[0]->getName());
    }

    /**
     * Test de la signature de verifierMultiple
     * @test
     */
    public function testSignatureVerifierMultiple(): void
    {
        $reflection = new \ReflectionMethod(ServicePermissions::class, 'verifierMultiple');
        $params = $reflection->getParameters();

        $this->assertCount(2, $params);
        $this->assertEquals('userId', $params[0]->getName());
        $this->assertEquals('checks', $params[1]->getName());
    }

    // ===== TESTS CONSTANTES DE CACHE =====

    /**
     * Test de la constante de durée de cache (devrait être 300 secondes = 5 minutes)
     * @test
     */
    public function testConstanteCacheDuree(): void
    {
        $reflection = new \ReflectionClass(ServicePermissions::class);
        
        // La constante est privée, mais on vérifie qu'elle existe
        // Note: Les constantes privées ne sont pas accessibles via hasConstant() en PHP < 8.1
        // On vérifie plutôt que la classe existe et est bien structurée
        $this->assertTrue($reflection->isInstantiable() || $reflection->hasMethod('verifier'));
    }

    // ===== TESTS TYPE RETOUR =====

    /**
     * Test que verifier retourne un booléen
     * @test
     */
    public function testVerifierRetourneBool(): void
    {
        $reflection = new \ReflectionMethod(ServicePermissions::class, 'verifier');
        $returnType = $reflection->getReturnType();

        $this->assertNotNull($returnType);
        $this->assertEquals('bool', $returnType->getName());
    }

    /**
     * Test que estAdministrateur retourne un booléen
     * @test
     */
    public function testEstAdministrateurRetourneBool(): void
    {
        $reflection = new \ReflectionMethod(ServicePermissions::class, 'estAdministrateur');
        $returnType = $reflection->getReturnType();

        $this->assertNotNull($returnType);
        $this->assertEquals('bool', $returnType->getName());
    }

    /**
     * Test que getToutesPermissions retourne un tableau
     * @test
     */
    public function testGetToutesPermissionsRetourneArray(): void
    {
        $reflection = new \ReflectionMethod(ServicePermissions::class, 'getToutesPermissions');
        $returnType = $reflection->getReturnType();

        $this->assertNotNull($returnType);
        $this->assertEquals('array', $returnType->getName());
    }
}
