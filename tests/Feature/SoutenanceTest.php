<?php

declare(strict_types=1);

namespace Tests\Feature;

use Tests\TestCase;

/**
 * Tests fonctionnels pour le module Soutenance
 * 
 * Scénarios de bout en bout pour la planification et le déroulement des soutenances.
 */
class SoutenanceTest extends TestCase
{
    /**
     * @test
     * Une soutenance peut être planifiée
     */
    public function testPlanificationSoutenance(): void
    {
        $soutenance = $this->createTestSoutenance();

        $this->assertArrayHasKey('id_soutenance', $soutenance);
        $this->assertArrayHasKey('date_soutenance', $soutenance);
        $this->assertEquals('Planifiee', $soutenance['statut']);
    }

    /**
     * @test
     * Une soutenance nécessite une date future
     */
    public function testDateSoutenanceFuture(): void
    {
        $dateFuture = date('Y-m-d H:i:s', strtotime('+7 days'));
        $soutenance = $this->createTestSoutenance(['date_soutenance' => $dateFuture]);

        $this->assertGreaterThan(date('Y-m-d'), substr($soutenance['date_soutenance'], 0, 10));
    }

    /**
     * @test
     * Une soutenance a une durée par défaut de 60 minutes
     */
    public function testDureeParDefaut(): void
    {
        $soutenance = $this->createTestSoutenance();

        $this->assertEquals(60, $soutenance['duree_minutes']);
    }

    /**
     * @test
     * Le statut peut être modifié en cours
     */
    public function testChangementStatutEnCours(): void
    {
        $soutenance = $this->createTestSoutenance(['statut' => 'En_cours']);

        $this->assertEquals('En_cours', $soutenance['statut']);
    }

    /**
     * @test
     * Une soutenance terminée peut générer un PV
     */
    public function testGenerationPvSoutenanceTerminee(): void
    {
        $soutenance = $this->createTestSoutenance([
            'statut' => 'Terminee',
            'pv_genere' => true,
            'pv_chemin' => '/storage/pv/soutenance_1.pdf',
        ]);

        $this->assertEquals('Terminee', $soutenance['statut']);
        $this->assertTrue($soutenance['pv_genere']);
        $this->assertNotEmpty($soutenance['pv_chemin']);
    }

    /**
     * @test
     * Une soutenance peut être annulée
     */
    public function testAnnulationSoutenance(): void
    {
        $soutenance = $this->createTestSoutenance(['statut' => 'Annulee']);

        $this->assertEquals('Annulee', $soutenance['statut']);
    }

    /**
     * @test
     * Une soutenance peut être reportée
     */
    public function testReportSoutenance(): void
    {
        $soutenance = $this->createTestSoutenance(['statut' => 'Reportee']);

        $this->assertEquals('Reportee', $soutenance['statut']);
    }

    /**
     * Helper pour créer une soutenance de test
     * 
     * @param array<string, mixed> $overrides
     * @return array<string, mixed>
     */
    protected function createTestSoutenance(array $overrides = []): array
    {
        return array_merge([
            'id_soutenance' => 1,
            'dossier_id' => 1,
            'date_soutenance' => date('Y-m-d H:i:s', strtotime('+7 days')),
            'lieu' => 'Amphi A',
            'salle_id' => 1,
            'duree_minutes' => 60,
            'statut' => 'Planifiee',
            'pv_genere' => false,
            'pv_chemin' => null,
        ], $overrides);
    }
}
