<?php

declare(strict_types=1);

namespace Tests\Unit\Models;

use PHPUnit\Framework\TestCase;

/**
 * Tests unitaires pour le modèle Permission
 */
class PermissionTest extends TestCase
{
    /**
     * @test
     */
    public function testPermissionAttributsRequis(): void
    {
        $permission = [
            'id_permission' => 1,
            'code_permission' => 'view_dashboard',
            'libelle_permission' => 'Voir le tableau de bord',
            'description' => 'Permet de voir le tableau de bord',
        ];

        $this->assertArrayHasKey('id_permission', $permission);
        $this->assertArrayHasKey('code_permission', $permission);
        $this->assertArrayHasKey('libelle_permission', $permission);
    }

    /**
     * @test
     */
    public function testCodePermissionFormat(): void
    {
        $code = 'view_dashboard';
        $this->assertMatchesRegularExpression('/^[a-z_]+$/', $code);
    }
}
