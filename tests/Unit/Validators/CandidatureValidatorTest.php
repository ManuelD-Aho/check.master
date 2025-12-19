<?php

declare(strict_types=1);

namespace Tests\Unit\Validators;

use App\Validators\CandidatureValidator;
use PHPUnit\Framework\TestCase;

/**
 * Tests unitaires pour CandidatureValidator
 */
class CandidatureValidatorTest extends TestCase
{
    private CandidatureValidator $validator;

    protected function setUp(): void
    {
        $this->validator = new CandidatureValidator();
    }

    // =========================================================================
    // Tests de validation complète
    // =========================================================================

    /**
     * @test
     */
    public function testValidateAvecDonneesValides(): void
    {
        $result = $this->validator->validate([
            'theme' => 'Implémentation d\'un système de gestion académique avec tableau de bord',
            'entreprise_id' => 1,
            'maitre_stage_nom' => 'M. Dupont',
            'maitre_stage_email' => 'dupont@entreprise.com',
            'date_debut_stage' => '2025-01-01',
            'date_fin_stage' => '2025-06-30',
        ]);

        $this->assertTrue($result);
        $this->assertEmpty($this->validator->getErrors());
    }

    // =========================================================================
    // Tests du thème
    // =========================================================================

    /**
     * @test
     */
    public function testValidateThemeObligatoire(): void
    {
        $result = $this->validator->validate([
            'theme' => '',
            'entreprise_id' => 1,
            'maitre_stage_nom' => 'M. Dupont',
            'maitre_stage_email' => 'dupont@entreprise.com',
            'date_debut_stage' => '2025-01-01',
            'date_fin_stage' => '2025-06-30',
        ]);

        $this->assertFalse($result);
        $this->assertArrayHasKey('theme', $this->validator->getErrors());
    }

    /**
     * @test
     */
    public function testValidateThemeTropCourt(): void
    {
        $result = $this->validator->validate([
            'theme' => 'Thème court',
            'entreprise_id' => 1,
            'maitre_stage_nom' => 'M. Dupont',
            'maitre_stage_email' => 'dupont@entreprise.com',
            'date_debut_stage' => '2025-01-01',
            'date_fin_stage' => '2025-06-30',
        ]);

        $this->assertFalse($result);
        $this->assertStringContainsString('20 caractères', $this->validator->getErrors()['theme']);
    }

    /**
     * @test
     */
    public function testValidateThemeTropLong(): void
    {
        $result = $this->validator->validate([
            'theme' => str_repeat('A', 501),
            'entreprise_id' => 1,
            'maitre_stage_nom' => 'M. Dupont',
            'maitre_stage_email' => 'dupont@entreprise.com',
            'date_debut_stage' => '2025-01-01',
            'date_fin_stage' => '2025-06-30',
        ]);

        $this->assertFalse($result);
        $this->assertStringContainsString('500 caractères', $this->validator->getErrors()['theme']);
    }

    // =========================================================================
    // Tests de l'entreprise
    // =========================================================================

    /**
     * @test
     */
    public function testValidateEntrepriseObligatoire(): void
    {
        $result = $this->validator->validate([
            'theme' => 'Implémentation d\'un système de gestion académique',
            'entreprise_id' => null,
            'maitre_stage_nom' => 'M. Dupont',
            'maitre_stage_email' => 'dupont@entreprise.com',
            'date_debut_stage' => '2025-01-01',
            'date_fin_stage' => '2025-06-30',
        ]);

        $this->assertFalse($result);
        $this->assertArrayHasKey('entreprise_id', $this->validator->getErrors());
    }

    // =========================================================================
    // Tests du maître de stage
    // =========================================================================

    /**
     * @test
     */
    public function testValidateMaitreStageNomObligatoire(): void
    {
        $result = $this->validator->validate([
            'theme' => 'Implémentation d\'un système de gestion académique',
            'entreprise_id' => 1,
            'maitre_stage_nom' => '',
            'maitre_stage_email' => 'dupont@entreprise.com',
            'date_debut_stage' => '2025-01-01',
            'date_fin_stage' => '2025-06-30',
        ]);

        $this->assertFalse($result);
        $this->assertArrayHasKey('maitre_stage_nom', $this->validator->getErrors());
    }

    /**
     * @test
     */
    public function testValidateMaitreStageEmailObligatoire(): void
    {
        $result = $this->validator->validate([
            'theme' => 'Implémentation d\'un système de gestion académique',
            'entreprise_id' => 1,
            'maitre_stage_nom' => 'M. Dupont',
            'maitre_stage_email' => '',
            'date_debut_stage' => '2025-01-01',
            'date_fin_stage' => '2025-06-30',
        ]);

        $this->assertFalse($result);
        $this->assertArrayHasKey('maitre_stage_email', $this->validator->getErrors());
    }

    /**
     * @test
     */
    public function testValidateMaitreStageEmailFormatInvalide(): void
    {
        $result = $this->validator->validate([
            'theme' => 'Implémentation d\'un système de gestion académique',
            'entreprise_id' => 1,
            'maitre_stage_nom' => 'M. Dupont',
            'maitre_stage_email' => 'invalid-email',
            'date_debut_stage' => '2025-01-01',
            'date_fin_stage' => '2025-06-30',
        ]);

        $this->assertFalse($result);
        $this->assertStringContainsString('email', $this->validator->getErrors()['maitre_stage_email']);
    }

    /**
     * @test
     */
    public function testValidateMaitreStageTelephoneFormatValide(): void
    {
        $result = $this->validator->validate([
            'theme' => 'Implémentation d\'un système de gestion académique',
            'entreprise_id' => 1,
            'maitre_stage_nom' => 'M. Dupont',
            'maitre_stage_email' => 'dupont@entreprise.com',
            'maitre_stage_tel' => '+2250712345678',
            'date_debut_stage' => '2025-01-01',
            'date_fin_stage' => '2025-06-30',
        ]);

        $this->assertTrue($result);
    }

    /**
     * @test
     */
    public function testValidateMaitreStageTelephoneFormatInvalide(): void
    {
        $result = $this->validator->validate([
            'theme' => 'Implémentation d\'un système de gestion académique',
            'entreprise_id' => 1,
            'maitre_stage_nom' => 'M. Dupont',
            'maitre_stage_email' => 'dupont@entreprise.com',
            'maitre_stage_tel' => '123',
            'date_debut_stage' => '2025-01-01',
            'date_fin_stage' => '2025-06-30',
        ]);

        $this->assertFalse($result);
        $this->assertArrayHasKey('maitre_stage_tel', $this->validator->getErrors());
    }

    // =========================================================================
    // Tests des dates de stage
    // =========================================================================

    /**
     * @test
     */
    public function testValidateDateDebutObligatoire(): void
    {
        $result = $this->validator->validate([
            'theme' => 'Implémentation d\'un système de gestion académique',
            'entreprise_id' => 1,
            'maitre_stage_nom' => 'M. Dupont',
            'maitre_stage_email' => 'dupont@entreprise.com',
            'date_debut_stage' => '',
            'date_fin_stage' => '2025-06-30',
        ]);

        $this->assertFalse($result);
        $this->assertArrayHasKey('date_debut_stage', $this->validator->getErrors());
    }

    /**
     * @test
     */
    public function testValidateDateFinObligatoire(): void
    {
        $result = $this->validator->validate([
            'theme' => 'Implémentation d\'un système de gestion académique',
            'entreprise_id' => 1,
            'maitre_stage_nom' => 'M. Dupont',
            'maitre_stage_email' => 'dupont@entreprise.com',
            'date_debut_stage' => '2025-01-01',
            'date_fin_stage' => '',
        ]);

        $this->assertFalse($result);
        $this->assertArrayHasKey('date_fin_stage', $this->validator->getErrors());
    }

    /**
     * @test
     */
    public function testValidateDateFinAvantDateDebut(): void
    {
        $result = $this->validator->validate([
            'theme' => 'Implémentation d\'un système de gestion académique',
            'entreprise_id' => 1,
            'maitre_stage_nom' => 'M. Dupont',
            'maitre_stage_email' => 'dupont@entreprise.com',
            'date_debut_stage' => '2025-06-30',
            'date_fin_stage' => '2025-01-01',
        ]);

        $this->assertFalse($result);
        $this->assertStringContainsString('postérieure', $this->validator->getErrors()['date_fin_stage']);
    }

    // =========================================================================
    // Tests des erreurs
    // =========================================================================

    /**
     * @test
     */
    public function testGetFirstErrorRetournePremiereErreur(): void
    {
        $this->validator->validate([
            'theme' => '',
            'entreprise_id' => null,
        ]);

        $firstError = $this->validator->getFirstError();

        $this->assertNotNull($firstError);
        $this->assertIsString($firstError);
    }

    /**
     * @test
     */
    public function testGetFirstErrorRetourneNullSansErreur(): void
    {
        $this->validator->validate([
            'theme' => 'Implémentation d\'un système de gestion académique',
            'entreprise_id' => 1,
            'maitre_stage_nom' => 'M. Dupont',
            'maitre_stage_email' => 'dupont@entreprise.com',
            'date_debut_stage' => '2025-01-01',
            'date_fin_stage' => '2025-06-30',
        ]);

        $this->assertNull($this->validator->getFirstError());
    }
}
