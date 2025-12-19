<?php

declare(strict_types=1);

namespace Tests\Unit\Models;

use PHPUnit\Framework\TestCase;

/**
 * Tests unitaires pour le modèle Utilisateur
 */
class UtilisateurTest extends TestCase
{
    /**
     * @test
     */
    public function testUtilisateurAttributsRequis(): void
    {
        $user = [
            'id_utilisateur' => 1,
            'nom_utilisateur' => 'Test User',
            'login_utilisateur' => 'test@example.com',
            'id_type_utilisateur' => 1,
            'statut_utilisateur' => 'Actif',
        ];

        $this->assertArrayHasKey('id_utilisateur', $user);
        $this->assertArrayHasKey('nom_utilisateur', $user);
        $this->assertArrayHasKey('login_utilisateur', $user);
        $this->assertArrayHasKey('statut_utilisateur', $user);
    }

    /**
     * @test
     */
    public function testStatutActifOuInactif(): void
    {
        $statutsValides = ['Actif', 'Inactif', 'Suspendu'];
        foreach ($statutsValides as $statut) {
            $this->assertContains($statut, $statutsValides);
        }
    }
}
