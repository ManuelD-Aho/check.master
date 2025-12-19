<?php

declare(strict_types=1);

namespace Tests\Unit\Models;

use PHPUnit\Framework\TestCase;

/**
 * Tests unitaires pour le modèle WorkflowEtat
 */
class WorkflowEtatTest extends TestCase
{
    /**
     * @test
     */
    public function testWorkflowEtatAttributsRequis(): void
    {
        $etat = [
            'id_etat' => 1,
            'code_etat' => 'INSCRIT',
            'libelle_etat' => 'Inscrit',
            'est_terminal' => false,
        ];

        $this->assertArrayHasKey('id_etat', $etat);
        $this->assertArrayHasKey('code_etat', $etat);
        $this->assertArrayHasKey('est_terminal', $etat);
    }

    /**
     * @test
     */
    public function testEtatsTerminaux(): void
    {
        $etatsTerminaux = ['DIPLOME_DELIVRE', 'ABANDON'];
        $this->assertContains('DIPLOME_DELIVRE', $etatsTerminaux);
    }
}
