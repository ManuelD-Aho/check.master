<?php

declare(strict_types=1);

namespace Tests\Unit\Models;

use PHPUnit\Framework\TestCase;

/**
 * Tests unitaires pour le modèle Soutenance
 */
class SoutenanceTest extends TestCase
{
    /**
     * @test
     */
    public function testSoutenanceAttributsRequis(): void
    {
        $soutenance = [
            'id_soutenance' => 1,
            'dossier_id' => 1,
            'date_soutenance' => '2025-12-20',
            'heure_debut' => '09:00',
            'heure_fin' => '11:00',
            'salle_id' => 1,
            'statut' => 'Planifiee',
        ];

        $this->assertArrayHasKey('id_soutenance', $soutenance);
        $this->assertArrayHasKey('date_soutenance', $soutenance);
        $this->assertArrayHasKey('salle_id', $soutenance);
    }

    /**
     * @test
     */
    public function testStatutsValides(): void
    {
        $statutsValides = ['Planifiee', 'En_cours', 'Terminee', 'Annulee'];
        $this->assertContains('Planifiee', $statutsValides);
    }
}
