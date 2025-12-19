<?php

declare(strict_types=1);

namespace Tests\Unit\Validators;

use App\Validators\PaiementValidator;
use PHPUnit\Framework\TestCase;

/**
 * Tests unitaires pour PaiementValidator
 */
class PaiementValidatorTest extends TestCase
{
    private PaiementValidator $validator;

    protected function setUp(): void
    {
        $this->validator = new PaiementValidator();
    }

    // =========================================================================
    // Tests données valides
    // =========================================================================

    /**
     * @test
     */
    public function testValidateAvecDonneesValides(): void
    {
        $result = $this->validator->validate([
            'etudiant_id' => 1,
            'montant' => 150000,
            'mode_paiement' => 'Especes',
            'annee_acad_id' => 1,
        ]);

        $this->assertTrue($result);
        $this->assertEmpty($this->validator->getErrors());
    }

    /**
     * @test
     * @dataProvider modesValidesProvider
     */
    public function testValidateModesValides(string $mode): void
    {
        $result = $this->validator->validate([
            'etudiant_id' => 1,
            'montant' => 100000,
            'mode_paiement' => $mode,
            'annee_acad_id' => 1,
        ]);

        $this->assertTrue($result, "Mode '$mode' devrait être valide");
    }

    public static function modesValidesProvider(): array
    {
        return [
            ['Especes'],
            ['Cheque'],
            ['Virement'],
            ['Mobile_Money'],
        ];
    }

    // =========================================================================
    // Tests étudiant
    // =========================================================================

    /**
     * @test
     */
    public function testValidateEtudiantObligatoire(): void
    {
        $result = $this->validator->validate([
            'etudiant_id' => null,
            'montant' => 150000,
            'mode_paiement' => 'Especes',
            'annee_acad_id' => 1,
        ]);

        $this->assertFalse($result);
        $this->assertArrayHasKey('etudiant_id', $this->validator->getErrors());
    }

    // =========================================================================
    // Tests montant
    // =========================================================================

    /**
     * @test
     */
    public function testValidateMontantObligatoire(): void
    {
        $result = $this->validator->validate([
            'etudiant_id' => 1,
            'mode_paiement' => 'Especes',
            'annee_acad_id' => 1,
        ]);

        $this->assertFalse($result);
        $this->assertArrayHasKey('montant', $this->validator->getErrors());
    }

    /**
     * @test
     */
    public function testValidateMontantNonNumerique(): void
    {
        $result = $this->validator->validate([
            'etudiant_id' => 1,
            'montant' => 'abc',
            'mode_paiement' => 'Especes',
            'annee_acad_id' => 1,
        ]);

        $this->assertFalse($result);
        $this->assertStringContainsString('nombre', $this->validator->getErrors()['montant']);
    }

    /**
     * @test
     */
    public function testValidateMontantNegatif(): void
    {
        $result = $this->validator->validate([
            'etudiant_id' => 1,
            'montant' => -1000,
            'mode_paiement' => 'Especes',
            'annee_acad_id' => 1,
        ]);

        $this->assertFalse($result);
        $this->assertStringContainsString('positif', $this->validator->getErrors()['montant']);
    }

    /**
     * @test
     */
    public function testValidateMontantZero(): void
    {
        $result = $this->validator->validate([
            'etudiant_id' => 1,
            'montant' => 0,
            'mode_paiement' => 'Especes',
            'annee_acad_id' => 1,
        ]);

        $this->assertFalse($result);
        $this->assertArrayHasKey('montant', $this->validator->getErrors());
    }

    // =========================================================================
    // Tests mode de paiement
    // =========================================================================

    /**
     * @test
     */
    public function testValidateModePaiementObligatoire(): void
    {
        $result = $this->validator->validate([
            'etudiant_id' => 1,
            'montant' => 150000,
            'mode_paiement' => '',
            'annee_acad_id' => 1,
        ]);

        $this->assertFalse($result);
        $this->assertArrayHasKey('mode_paiement', $this->validator->getErrors());
    }

    /**
     * @test
     */
    public function testValidateModePaiementInvalide(): void
    {
        $result = $this->validator->validate([
            'etudiant_id' => 1,
            'montant' => 150000,
            'mode_paiement' => 'Bitcoin',
            'annee_acad_id' => 1,
        ]);

        $this->assertFalse($result);
        $this->assertStringContainsString('invalide', $this->validator->getErrors()['mode_paiement']);
    }

    // =========================================================================
    // Tests année académique
    // =========================================================================

    /**
     * @test
     */
    public function testValidateAnneeAcadObligatoire(): void
    {
        $result = $this->validator->validate([
            'etudiant_id' => 1,
            'montant' => 150000,
            'mode_paiement' => 'Especes',
            'annee_acad_id' => null,
        ]);

        $this->assertFalse($result);
        $this->assertArrayHasKey('annee_acad_id', $this->validator->getErrors());
    }

    // =========================================================================
    // Tests erreurs
    // =========================================================================

    /**
     * @test
     */
    public function testGetFirstError(): void
    {
        $this->validator->validate([]);

        $this->assertNotNull($this->validator->getFirstError());
    }
}
