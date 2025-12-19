<?php

declare(strict_types=1);

namespace Tests\Unit\Models;

use PHPUnit\Framework\TestCase;

/**
 * Tests unitaires pour le modèle RapportEtudiant
 */
class RapportEtudiantTest extends TestCase
{
    /**
     * @test
     */
    public function testRapportAttributsRequis(): void
    {
        $rapport = [
            'id_rapport' => 1,
            'dossier_id' => 1,
            'fichier_path' => '/uploads/rapport.pdf',
            'version' => 1,
            'statut' => 'Soumis',
        ];

        $this->assertArrayHasKey('id_rapport', $rapport);
        $this->assertArrayHasKey('fichier_path', $rapport);
        $this->assertArrayHasKey('statut', $rapport);
    }

    /**
     * @test
     */
    public function testStatutsValides(): void
    {
        $statutsValides = ['Brouillon', 'Soumis', 'En_revision', 'Valide', 'Rejete'];
        $this->assertContains('Soumis', $statutsValides);
    }
}
