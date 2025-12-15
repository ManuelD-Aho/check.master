<?php

declare(strict_types=1);

namespace Tests\Unit\Services;

use PHPUnit\Framework\TestCase;
use App\Services\Security\ServicePermissions;
use App\Models\Permission;

/**
 * Tests unitaires pour ServicePermissions
 */
class ServicePermissionsTest extends TestCase
{
    /**
     * Test des constantes d'action Permission
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
     * Test que la méthode verifier existe et est callable
     */
    public function testMethodeVerifierExiste(): void
    {
        $this->assertTrue(method_exists(ServicePermissions::class, 'verifier'));
    }

    /**
     * Test que la méthode invaliderCache existe
     */
    public function testMethodeInvaliderCacheExiste(): void
    {
        $this->assertTrue(method_exists(ServicePermissions::class, 'invaliderCache'));
    }

    /**
     * Test que la méthode estAdministrateur existe
     */
    public function testMethodeEstAdministrateurExiste(): void
    {
        $this->assertTrue(method_exists(ServicePermissions::class, 'estAdministrateur'));
    }

    /**
     * Test de la méthode verifierMultiple
     */
    public function testMethodeVerifierMultipleExiste(): void
    {
        $this->assertTrue(method_exists(ServicePermissions::class, 'verifierMultiple'));
    }
}
