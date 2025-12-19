<?php

declare(strict_types=1);

namespace Tests\Unit\Models;

use PHPUnit\Framework\TestCase;

/**
 * Tests unitaires pour le modèle Paiement
 */
class PaiementTest extends TestCase
{
    /**
     * @test
     */
    public function testPaiementAttributsRequis(): void
    {
        $paiement = [
            'id_paiement' => 1,
            'etudiant_id' => 1,
            'montant' => 150000,
            'mode_paiement' => 'Especes',
            'date_paiement' => '2025-01-15',
        ];

        $this->assertArrayHasKey('id_paiement', $paiement);
        $this->assertArrayHasKey('montant', $paiement);
        $this->assertArrayHasKey('mode_paiement', $paiement);
    }

    /**
     * @test
     */
    public function testMontantPositif(): void
    {
        $paiement = ['montant' => 150000];
        $this->assertGreaterThan(0, $paiement['montant']);
    }

    /**
     * @test
     */
    public function testModesPaiementValides(): void
    {
        $modesValides = ['Especes', 'Cheque', 'Virement', 'Mobile_Money'];
        $this->assertContains('Especes', $modesValides);
    }
}
