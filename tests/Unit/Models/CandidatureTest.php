<?php

declare(strict_types=1);

namespace Tests\Unit\Models;

use Tests\TestCase;

/**
 * Tests unitaires pour le modèle Candidature
 */
class CandidatureTest extends TestCase
{
    /**
     * @test
     */
    public function testCandidatureAttributsRequis(): void
    {
        $candidature = $this->createTestCandidature();

        $this->assertArrayHasKey('id_candidature', $candidature);
        $this->assertArrayHasKey('theme', $candidature);
        $this->assertArrayHasKey('date_soumission', $candidature);
    }

    /**
     * @test
     */
    public function testThemeDoitAvoirLongueurMinimale(): void
    {
        $candidature = $this->createTestCandidature([
            'theme' => 'Implémentation d\'un système de gestion académique',
        ]);

        $this->assertGreaterThanOrEqual(20, strlen($candidature['theme']));
    }

    /**
     * @test
     */
    public function testValidationStatuts(): void
    {
        $candidature = $this->createTestCandidature();

        $this->assertIsBool($candidature['validee_scolarite']);
        $this->assertIsBool($candidature['validee_communication']);
    }
}
