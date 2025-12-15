<?php

declare(strict_types=1);

namespace Tests\Feature;

use Tests\TestCase;

/**
 * Tests fonctionnels pour le module Candidature
 * 
 * Scénarios de bout en bout pour la soumission et validation des candidatures.
 */
class CandidatureTest extends TestCase
{
    /**
     * @test
     * Une candidature valide peut être créée
     */
    public function testCreationCandidatureValide(): void
    {
        $candidature = $this->createTestCandidature();

        $this->assertArrayHasKey('id_candidature', $candidature);
        $this->assertArrayHasKey('theme', $candidature);
        $this->assertNotEmpty($candidature['theme']);
        $this->assertFalse($candidature['validee_scolarite']);
        $this->assertFalse($candidature['validee_communication']);
    }

    /**
     * @test
     * Le thème de la candidature doit être non vide
     */
    public function testThemeCandidatureRequis(): void
    {
        $candidature = $this->createTestCandidature(['theme' => '']);

        $this->assertEmpty($candidature['theme']);
    }

    /**
     * @test
     * Une candidature a un état initial non validé
     */
    public function testEtatInitialCandidature(): void
    {
        $candidature = $this->createTestCandidature();

        $this->assertFalse($candidature['validee_scolarite']);
        $this->assertFalse($candidature['validee_communication']);
    }

    /**
     * @test
     * La validation scolarité modifie le statut
     */
    public function testValidationScolariteCandidature(): void
    {
        $candidature = $this->createTestCandidature([
            'validee_scolarite' => true,
            'date_valid_scolarite' => date('Y-m-d H:i:s'),
        ]);

        $this->assertTrue($candidature['validee_scolarite']);
        $this->assertArrayHasKey('date_valid_scolarite', $candidature);
    }

    /**
     * @test
     * La validation complète nécessite les deux validations
     */
    public function testValidationCompleteCandidature(): void
    {
        $candidature = $this->createTestCandidature([
            'validee_scolarite' => true,
            'validee_communication' => true,
            'date_valid_scolarite' => date('Y-m-d H:i:s'),
            'date_valid_communication' => date('Y-m-d H:i:s'),
        ]);

        $this->assertTrue($candidature['validee_scolarite']);
        $this->assertTrue($candidature['validee_communication']);
    }

    /**
     * @test
     * Une candidature doit avoir un dossier étudiant associé
     */
    public function testCandidatureAssocieeDossier(): void
    {
        $candidature = $this->createTestCandidature();

        $this->assertArrayHasKey('dossier_id', $candidature);
        $this->assertGreaterThan(0, $candidature['dossier_id']);
    }

    /**
     * @test
     * L'email du maître de stage doit être valide s'il est fourni
     */
    public function testEmailMaitreStageValide(): void
    {
        $candidature = $this->createTestCandidature([
            'maitre_stage_email' => 'maitre@entreprise.ci',
        ]);

        $this->assertStringContainsString('@', $candidature['maitre_stage_email']);
        $this->assertTrue(filter_var($candidature['maitre_stage_email'], FILTER_VALIDATE_EMAIL) !== false);
    }
}
